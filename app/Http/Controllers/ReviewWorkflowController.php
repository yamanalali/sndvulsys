<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\Models\Submission;
use App\Models\VolunteerRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewWorkflowController extends Controller
{
    /**
     * عرض لوحة تحكم سير المراجعة
     */
    public function dashboard()
    {
        $statistics = $this->getWorkflowStatistics();
        $recentWorkflows = Workflow::with(['volunteerRequest', 'reviewer', 'assignedTo'])
            ->latest()
            ->take(10)
            ->get();
        
        $pendingWorkflows = Workflow::with(['volunteerRequest', 'assignedTo'])
            ->pending()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        $overdueWorkflows = Workflow::with(['volunteerRequest', 'assignedTo'])
            ->where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        return view('workflows.dashboard', compact('statistics', 'recentWorkflows', 'pendingWorkflows', 'overdueWorkflows'));
    }

    /**
     * عرض قائمة سير المراجعة
     */
    public function index(Request $request)
    {
        $query = Workflow::with(['volunteerRequest', 'reviewer', 'assignedTo']);

        // فلترة حسب الحالة
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الأولوية
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        // فلترة حسب المراجع
        if ($request->reviewer) {
            $query->where('assigned_to', $request->reviewer);
        }

        // فلترة حسب التاريخ
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $workflows = $query->orderBy('created_at', 'desc')->paginate(15);
        $statuses = Workflow::getStatuses();
        $priorities = Workflow::getPriorities();
        $reviewers = User::where('role', 'reviewer')->orWhere('role', 'admin')->get();

        return view('workflows.index', compact('workflows', 'statuses', 'priorities', 'reviewers'));
    }

    /**
     * عرض تفاصيل سير المراجعة
     */
    public function show($id)
    {
        $workflow = Workflow::with([
            'volunteerRequest',
            'reviewer',
            'assignedTo',
            'submission'
        ])->findOrFail($id);

        $workflowHistory = $this->getWorkflowHistory($workflow);
        $statuses = Workflow::getStatuses();
        $steps = Workflow::getSteps();
        $priorities = Workflow::getPriorities();
        $reviewers = User::where('role', 'reviewer')->orWhere('role', 'admin')->get();

        return view('workflows.show', compact(
            'workflow',
            'workflowHistory',
            'statuses',
            'steps',
            'priorities',
            'reviewers'
        ));
    }

    /**
     * تحديث حالة سير المراجعة
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in_review,approved,rejected,needs_revision,completed,cancelled',
            'notes' => 'nullable|string|max:2000',
            'next_step' => 'nullable|integer|min:1|max:6',
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $workflow = Workflow::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $updateData = [
                'status' => $request->status,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'notes' => $request->notes
            ];

            // تحديث الخطوة التالية إذا تم تحديدها
            if ($request->next_step) {
                $steps = Workflow::getSteps();
                $updateData['step'] = $request->next_step;
                $updateData['step_name'] = $steps[$request->next_step] ?? 'غير محدد';
                $updateData['is_completed'] = false;
            }

            // تحديث المراجع إذا تم تحديده
            if ($request->assigned_to) {
                $updateData['assigned_to'] = $request->assigned_to;
            }

            // إذا كانت الحالة موافق عليها أو مرفوضة، إكمال الخطوة
            if (in_array($request->status, ['approved', 'rejected'])) {
                $updateData['is_completed'] = true;
            }

            $workflow->update($updateData);

            // تحديث حالة الإرسال المرتبط
            $this->updateSubmissionStatus($workflow, $request->status);

            // إنشاء سجل في التاريخ
            $this->logWorkflowAction($workflow, $request->status, $request->notes);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة سير المراجعة بنجاح',
                'workflow' => $workflow->load(['volunteerRequest', 'reviewer'])
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في تحديث حالة سير المراجعة: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ أثناء تحديث الحالة'], 500);
        }
    }

    /**
     * تعيين مراجع لسير المراجعة
     */
    public function assignReviewer(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date|after:now'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $workflow = Workflow::findOrFail($id);
        $workflow->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'in_review',
            'due_date' => $request->due_date
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تعيين المراجع بنجاح',
            'workflow' => $workflow->load(['assignedTo'])
        ]);
    }

    /**
     * الانتقال للخطوة التالية
     */
    public function proceedToNextStep($id)
    {
        $workflow = Workflow::findOrFail($id);
        
        if (!$workflow->canProceedToNext()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن الانتقال للخطوة التالية'
            ], 400);
        }

        $nextStepInfo = $workflow->getNextStepInfo();
        
        if (!$nextStepInfo['exists']) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد خطوات إضافية'
            ], 400);
        }

        $workflow->update([
            'step' => $nextStepInfo['step'],
            'step_name' => $nextStepInfo['name'],
            'is_completed' => false,
            'status' => 'pending',
            'reviewed_by' => null,
            'reviewed_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم الانتقال للخطوة التالية بنجاح',
            'workflow' => $workflow->load(['volunteerRequest', 'reviewer'])
        ]);
    }

    /**
     * إعادة تعيين سير المراجعة
     */
    public function reassign(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|exists:users,id',
            'reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $workflow = Workflow::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $workflow->update([
                'assigned_to' => $request->assigned_to,
                'status' => 'pending',
                'notes' => $request->reason
            ]);

            // إنشاء سجل في التاريخ
            $this->logWorkflowAction($workflow, 'reassigned', $request->reason);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم إعادة تعيين سير المراجعة بنجاح',
                'workflow' => $workflow->load(['assignedTo'])
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في إعادة تعيين سير المراجعة: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ أثناء إعادة التعيين'], 500);
        }
    }

    /**
     * الحصول على إحصائيات سير المراجعة
     */
    private function getWorkflowStatistics()
    {
        return [
            'total' => Workflow::count(),
            'pending' => Workflow::where('status', 'pending')->count(),
            'in_review' => Workflow::where('status', 'in_review')->count(),
            'approved' => Workflow::where('status', 'approved')->count(),
            'rejected' => Workflow::where('status', 'rejected')->count(),
            'completed' => Workflow::where('status', 'completed')->count(),
            'overdue' => Workflow::where('due_date', '<', now())
                ->where('status', '!=', 'completed')
                ->count(),
            'avg_review_time' => $this->getAverageReviewTime(),
            'completion_rate' => $this->getCompletionRate()
        ];
    }

    /**
     * الحصول على متوسط وقت المراجعة
     */
    private function getAverageReviewTime()
    {
        $completedWorkflows = Workflow::whereNotNull('reviewed_at')
            ->whereNotNull('created_at')
            ->get();

        if ($completedWorkflows->isEmpty()) {
            return 0;
        }

        $totalHours = $completedWorkflows->sum(function ($workflow) {
            return $workflow->created_at->diffInHours($workflow->reviewed_at);
        });

        return round($totalHours / $completedWorkflows->count(), 2);
    }

    /**
     * الحصول على معدل الإكمال
     */
    private function getCompletionRate()
    {
        $total = Workflow::count();
        $completed = Workflow::where('status', 'completed')->count();

        if ($total === 0) {
            return 0;
        }

        return round(($completed / $total) * 100, 2);
    }

    /**
     * الحصول على تاريخ سير المراجعة
     */
    private function getWorkflowHistory($workflow)
    {
        // يمكن إضافة جدول منفصل لتتبع التاريخ
        // حالياً نعيد البيانات الأساسية
        return [
            [
                'action' => 'إنشاء سير المراجعة',
                'user' => 'النظام',
                'date' => $workflow->created_at,
                'notes' => 'تم إنشاء سير المراجعة'
            ]
        ];
    }

    /**
     * تحديث حالة الإرسال المرتبط
     */
    private function updateSubmissionStatus($workflow, $status)
    {
        $submission = Submission::where('volunteer-request_id', $workflow->volunteer-request_id)
            ->latest()
            ->first();

        if ($submission) {
            $submission->update([
                'status' => $status,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now()
            ]);
        }

        // تحديث حالة طلب التطوع المرتبط
        $volunteerRequest = VolunteerRequest::where('id', $workflow->volunteer-request_id)->first();
        if ($volunteerRequest) {
            $volunteerRequest->update([
                'status' => $status,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now()
            ]);
        }
    }

    /**
     * تسجيل إجراء في سير المراجعة
     */
    private function logWorkflowAction($workflow, $action, $notes = null)
    {
        // يمكن إضافة جدول منفصل لتسجيل الإجراءات
        Log::info("إجراء سير المراجعة", [
            'workflow_id' => $workflow->id,
            'action' => $action,
            'user_id' => Auth::id(),
            'notes' => $notes
        ]);
    }

    /**
     * تصدير بيانات سير المراجعة
     */
    public function export(Request $request)
    {
        $query = Workflow::with(['volunteerRequest', 'reviewer', 'assignedTo']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $workflows = $query->get();

        return response()->json($workflows);
    }

    /**
     * البحث في سير المراجعة
     */
    public function search(Request $request)
    {
        $query = Workflow::with(['volunteerRequest', 'reviewer', 'assignedTo']);

        if ($request->search) {
            $query->whereHas('volunteerRequest', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        $workflows = $query->paginate(15);

        return response()->json($workflows);
    }
} 