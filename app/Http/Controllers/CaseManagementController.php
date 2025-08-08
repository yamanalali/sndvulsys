<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\Models\Submission;
use App\Models\VolunteerRequest;
use App\Models\User;
use App\Models\CaseStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CaseManagementController extends Controller
{
    /**
     * عرض لوحة تحكم إدارة الحالات
     */
    public function dashboard()
    {
        try {
            $statistics = $this->getCaseStatistics();
            $recentCases = $this->getRecentCases();
            $urgentCases = $this->getUrgentCases();
            $overdueCases = $this->getOverdueCases();

            return view('cases.dashboard', compact('statistics', 'recentCases', 'urgentCases', 'overdueCases'));
        } catch (\Exception $e) {
            // في حالة حدوث خطأ، اعرض صفحة الترحيب
            return view('cases.welcome');
        }
    }

    /**
     * عرض قائمة الحالات
     */
    public function index(Request $request)
    {
        try {
            $query = $this->buildCaseQuery($request);
            $cases = $query->paginate(15);

            // إذا لم توجد حالات وليس هناك فلتر بحث، اعرض صفحة الترحيب
            if ($cases->count() == 0 && !$request->hasAny(['status', 'priority', 'search', 'assigned_to', 'date_from', 'date_to'])) {
                return view('cases.welcome');
            }

            $statuses = [
                'pending' => 'معلق',
                'in_progress' => 'قيد التقدم',
                'under_review' => 'قيد المراجعة',
                'approved' => 'موافق عليه',
                'rejected' => 'مرفوض',
                'needs_revision' => 'يحتاج مراجعة',
                'completed' => 'مكتمل',
                'cancelled' => 'ملغي'
            ];
            
            $priorities = [
                'low' => 'منخفضة',
                'medium' => 'متوسطة',
                'high' => 'عالية',
                'urgent' => 'عاجلة'
            ];
            
            $reviewers = User::all(); // مؤقتاً لجلب جميع المستخدمين

            return view('cases.index', compact('cases', 'statuses', 'priorities', 'reviewers'));
        } catch (\Exception $e) {
            // في حالة حدوث خطأ، اعرض صفحة الترحيب
            return view('cases.welcome');
        }
    }

    /**
     * عرض تفاصيل الحالة
     */
    public function show($id)
    {
        $case = $this->getCaseWithDetails($id);
        $caseHistory = $this->getCaseHistory($case);
        $relatedWorkflows = $this->getRelatedWorkflows($case);
        $caseProgress = $this->calculateCaseProgress($case);

        return view('cases.show', compact('case', 'caseHistory', 'relatedWorkflows', 'caseProgress'));
    }

    /**
     * تحديث حالة الحالة
     */
    public function updateCaseStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
            'notes' => 'nullable|string|max:2000',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date|after:now'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $case = $this->getCaseWithDetails($id);
        
        DB::beginTransaction();
        try {
            $updateData = [
                'status' => $request->status,
                'updated_by' => Auth::id(),
                'notes' => $request->notes
            ];

            if ($request->assigned_to) {
                $updateData['assigned_to'] = $request->assigned_to;
            }

            if ($request->due_date) {
                $updateData['due_date'] = $request->due_date;
            }

            $case->update($updateData);

            // تحديث الحالات المرتبطة
            $this->updateRelatedStatuses($case, $request->status);

            // تسجيل التغيير في التاريخ
            $this->logCaseStatusChange($case, $request->status, $request->notes);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الحالة بنجاح',
                'case' => $case->load(['volunteerRequest', 'assignedTo'])
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في تحديث حالة الحالة: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ أثناء تحديث الحالة'], 500);
        }
    }

    /**
     * تعيين مراجع للحالة
     */
    public function assignCase(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date|after:now',
            'priority' => 'nullable|in:low,medium,high,urgent'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $case = $this->getCaseWithDetails($id);
        
        $updateData = [
            'assigned_to' => $request->assigned_to,
            'status' => 'in_progress'
        ];

        if ($request->due_date) {
            $updateData['due_date'] = $request->due_date;
        }

        if ($request->priority) {
            $updateData['priority'] = $request->priority;
        }

        $case->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'تم تعيين الحالة بنجاح',
            'case' => $case->load(['assignedTo'])
        ]);
    }

    /**
     * إضافة ملاحظة للحالة
     */
    public function addCaseNote(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required|string|max:2000',
            'is_internal' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $case = $this->getCaseWithDetails($id);
        
        $note = $case->notes()->create([
            'user_id' => Auth::id(),
            'note' => $request->note,
            'is_internal' => $request->is_internal ?? false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الملاحظة بنجاح',
            'note' => $note->load('user')
        ]);
    }

    /**
     * تتبع تقدم الحالة
     */
    public function trackProgress($id)
    {
        $case = $this->getCaseWithDetails($id);
        $progress = $this->calculateCaseProgress($case);
        $timeline = $this->getCaseTimeline($case);
        $metrics = $this->getCaseMetrics($case);

        return response()->json([
            'case' => $case,
            'progress' => $progress,
            'timeline' => $timeline,
            'metrics' => $metrics
        ]);
    }

    /**
     * تصدير تقرير الحالة
     */
    public function exportCaseReport($id)
    {
        $case = $this->getCaseWithDetails($id);
        $caseHistory = $this->getCaseHistory($case);
        $relatedWorkflows = $this->getRelatedWorkflows($case);
        $caseProgress = $this->calculateCaseProgress($case);

        $report = [
            'case' => $case,
            'history' => $caseHistory,
            'workflows' => $relatedWorkflows,
            'progress' => $caseProgress,
            'exported_at' => now()
        ];

        return response()->json($report);
    }

    /**
     * بناء استعلام الحالات
     */
    private function buildCaseQuery(Request $request)
    {
        $query = VolunteerRequest::with(['submissions', 'workflows', 'assignedTo']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        if ($request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->search) {
            $query->where('full_name', 'like', '%' . $request->search . '%');
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * الحصول على الحالة مع التفاصيل
     */
    private function getCaseWithDetails($id)
    {
        return VolunteerRequest::with([
            'submissions.attachments',
            'submissions.comments.user',
            'workflows.reviewer',
            'workflows.assignedTo',
            'assignedTo',
            'notes.user'
        ])->findOrFail($id);
    }

    /**
     * الحصول على تاريخ الحالة
     */
    private function getCaseHistory($case)
    {
        $history = [];

        // إضافة إنشاء الحالة
        $history[] = [
            'action' => 'إنشاء الحالة',
            'user' => 'النظام',
            'date' => $case->created_at,
            'notes' => 'تم إنشاء الحالة'
        ];

        // إضافة تحديثات الحالة
        foreach ($case->workflows as $workflow) {
            if ($workflow->reviewed_at) {
                $history[] = [
                    'action' => 'تحديث الحالة',
                    'user' => $workflow->reviewer ? $workflow->reviewer->name : 'غير محدد',
                    'date' => $workflow->reviewed_at,
                    'notes' => $workflow->notes
                ];
            }
        }

        // إضافة مراجعة الطلب
        if ($case->reviewed_at) {
            $history[] = [
                'action' => 'مراجعة الطلب',
                'user' => $case->reviewed_by ? User::find($case->reviewed_by)->name : 'غير محدد',
                'date' => $case->reviewed_at,
                'notes' => $case->admin_notes
            ];
        }

        // إضافة الملاحظات
        foreach ($case->notes as $note) {
            $history[] = [
                'action' => 'إضافة ملاحظة',
                'user' => $note->user->name,
                'date' => $note->created_at,
                'notes' => $note->note
            ];
        }

        // ترتيب التاريخ حسب التاريخ
        usort($history, function($a, $b) {
            return $a['date']->compare($b['date']);
        });

        return $history;
    }

    /**
     * الحصول على سير المراجعة المرتبط
     */
    private function getRelatedWorkflows($case)
    {
        return $case->workflows()->with(['reviewer', 'assignedTo'])->get();
    }

    /**
     * الحصول على الإرسالات المرتبطة
     */
    private function getRelatedSubmissions($case)
    {
        return $case->submissions()->with(['reviewer', 'assignedTo'])->get();
    }

    /**
     * حساب تقدم الحالة
     */
    private function calculateCaseProgress($case)
    {
        $totalSteps = 6; // عدد الخطوات الإجمالي
        $completedSteps = 0;

        foreach ($case->workflows as $workflow) {
            if ($workflow->is_completed) {
                $completedSteps++;
            }
        }

        // إذا كانت الحالة موافق عليها، تعتبر مكتملة
        if ($case->status === 'approved') {
            $completedSteps = $totalSteps;
        }

        $progress = ($completedSteps / $totalSteps) * 100;

        return [
            'percentage' => round($progress, 2),
            'completed_steps' => $completedSteps,
            'total_steps' => $totalSteps,
            'remaining_steps' => $totalSteps - $completedSteps
        ];
    }

    /**
     * الحصول على الجدول الزمني للحالة
     */
    private function getCaseTimeline($case)
    {
        $timeline = [];

        // إضافة إنشاء الحالة
        $timeline[] = [
            'date' => $case->created_at,
            'event' => 'إنشاء الحالة',
            'description' => 'تم إنشاء الحالة بنجاح'
        ];

        // إضافة سير المراجعة
        foreach ($case->workflows as $workflow) {
            $timeline[] = [
                'date' => $workflow->created_at,
                'event' => 'بدء سير المراجعة',
                'description' => 'تم بدء سير المراجعة للخطوة: ' . $workflow->step_name
            ];

            if ($workflow->reviewed_at) {
                $timeline[] = [
                    'date' => $workflow->reviewed_at,
                    'event' => 'إكمال سير المراجعة',
                    'description' => 'تم إكمال سير المراجعة: ' . $workflow->status
                ];
            }
        }

        // إضافة مراجعة الطلب
        if ($case->reviewed_at) {
            $timeline[] = [
                'date' => $case->reviewed_at,
                'event' => 'مراجعة الطلب',
                'description' => 'تم مراجعة الطلب: ' . $case->status
            ];
        }

        // ترتيب الجدول الزمني
        usort($timeline, function($a, $b) {
            return $a['date']->compare($b['date']);
        });

        return $timeline;
    }

    /**
     * الحصول على مقاييس الحالة
     */
    private function getCaseMetrics($case)
    {
        $totalWorkflows = $case->workflows->count();
        $completedWorkflows = $case->workflows->where('is_completed', true)->count();
        $pendingWorkflows = $case->workflows->where('status', 'pending')->count();
        $inReviewWorkflows = $case->workflows->where('status', 'in_review')->count();
        $totalSubmissions = $case->submissions->count();
        $completedSubmissions = $case->submissions->where('status', 'completed')->count();
        $avgReviewTime = 0;
        $completedWithTime = $case->workflows->filter(function($workflow) {
            return $workflow->reviewed_at && $workflow->created_at;
        });

        if ($completedWithTime->count() > 0) {
            $totalTime = $completedWithTime->sum(function($workflow) {
                return $workflow->created_at->diffInHours($workflow->reviewed_at);
            });
            $avgReviewTime = round($totalTime / $completedWithTime->count(), 2);
        }

        return [
            'total_workflows' => $totalWorkflows,
            'completed_workflows' => $completedWorkflows,
            'pending_workflows' => $pendingWorkflows,
            'in_review_workflows' => $inReviewWorkflows,
            'total_submissions' => $totalSubmissions,
            'completed_submissions' => $completedSubmissions,
            'completion_rate' => $totalWorkflows > 0 ? round(($completedWorkflows / $totalWorkflows) * 100, 2) : 0,
            'avg_review_time_hours' => $avgReviewTime,
            'days_since_creation' => $case->created_at->diffInDays(now()),
            'is_overdue' => $case->due_date && $case->due_date->isPast()
        ];
    }

    /**
     * تحديث الحالات المرتبطة
     */
    private function updateRelatedStatuses($case, $status)
    {
        // تحديث حالة سير المراجعة المرتبط
        $latestWorkflow = $case->workflows()->latest()->first();
        if ($latestWorkflow) {
            $latestWorkflow->update(['status' => $status]);
        }

        // تحديث حالة الإرسال المرتبط
        $latestSubmission = $case->submissions()->latest()->first();
        if ($latestSubmission) {
            $latestSubmission->update(['status' => $status]);
        }

        // تحديث حالة الحالة نفسها
        $case->update(['status' => $status]);
    }

    /**
     * تسجيل تغيير حالة الحالة
     */
    private function logCaseStatusChange($case, $status, $notes = null)
    {
        Log::info("تغيير حالة الحالة", [
            'case_id' => $case->id,
            'old_status' => $case->getOriginal('status'),
            'new_status' => $status,
            'user_id' => Auth::id(),
            'notes' => $notes
        ]);
    }

    /**
     * الحصول على إحصائيات الحالات
     */
    private function getCaseStatistics()
    {
        return [
            'total' => VolunteerRequest::count(),
            'pending' => VolunteerRequest::where('status', 'pending')->count(),
            'approved' => VolunteerRequest::where('status', 'approved')->count(),
            'rejected' => VolunteerRequest::where('status', 'rejected')->count(),
            'withdrawn' => VolunteerRequest::where('status', 'withdrawn')->count(),
            'overdue' => VolunteerRequest::where('due_date', '<', now())
                ->where('status', '!=', 'approved')
                ->count(),
            'avg_completion_time' => $this->getAverageCompletionTime(),
            'completion_rate' => $this->getCaseCompletionRate()
        ];
    }

    /**
     * الحصول على متوسط وقت الإكمال
     */
    private function getAverageCompletionTime()
    {
        $completedCases = VolunteerRequest::where('status', 'approved')
            ->whereNotNull('created_at')
            ->whereNotNull('reviewed_at')
            ->get();

        if ($completedCases->isEmpty()) {
            return 0;
        }

        $totalDays = $completedCases->sum(function ($case) {
            return $case->created_at->diffInDays($case->reviewed_at);
        });

        return round($totalDays / $completedCases->count(), 2);
    }

    /**
     * الحصول على معدل إكمال الحالات
     */
    private function getCaseCompletionRate()
    {
        $total = VolunteerRequest::count();
        $completed = VolunteerRequest::where('status', 'approved')->count();

        if ($total === 0) {
            return 0;
        }

        return round(($completed / $total) * 100, 2);
    }

    /**
     * الحصول على الحالات الحديثة
     */
    private function getRecentCases()
    {
        return VolunteerRequest::with(['assignedTo'])
            ->latest()
            ->take(10)
            ->get();
    }

    /**
     * الحصول على الحالات العاجلة
     */
    private function getUrgentCases()
    {
        return VolunteerRequest::with(['assignedTo'])
            ->where('priority', 'urgent')
            ->where('status', '!=', 'approved')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();
    }

    /**
     * الحصول على الحالات المتأخرة
     */
    private function getOverdueCases()
    {
        return VolunteerRequest::with(['assignedTo'])
            ->where('due_date', '<', now())
            ->where('status', '!=', 'approved')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();
    }
} 