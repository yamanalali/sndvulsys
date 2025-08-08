<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\Models\VolunteerRequest;
use App\Models\User;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubmissionController extends Controller
{
    /**
     * عرض قائمة الإرسالات
     */
    public function index()
    {
        $submissions = Submission::with(['volunteerRequest', 'reviewer', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $statuses = Submission::getStatuses();
        $priorities = Submission::getPriorities();
        $statistics = $this->getSubmissionStatistics();
        $users = User::all();

        return view('submissions.index', compact('submissions', 'statuses', 'priorities', 'statistics', 'users'));
    }

    /**
     * عرض نموذج إنشاء إرسال جديد
     */
    public function create()
    {
        $volunteerRequests = VolunteerRequest::all();
        $users = User::all();
        $statuses = Submission::getStatuses();
        $priorities = Submission::getPriorities();

        return view('submissions.create', compact('volunteerRequests', 'users', 'statuses', 'priorities'));
    }

    /**
     * حفظ إرسال جديد
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'volunteer-request_id' => 'required|exists:volunteer-requests,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:2000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $submission = Submission::create([
                'volunteer-request_id' => $request->input('volunteer-request_id'),
                'assigned_to' => $request->assigned_to,
                'priority' => $request->priority,
                'due_date' => $request->due_date,
                'notes' => $request->notes,
                'status' => 'pending',
                'created_by' => Auth::id()
            ]);

            // معالجة المرفقات
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('submissions/' . $submission->id, 'public');
                    $submission->attachments()->create([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'file_type' => $file->getMimeType()
                    ]);
                }
            }

            // إنشاء سير مراجعة تلقائي
            $this->createWorkflowForSubmission($submission);

            DB::commit();
            return redirect()->route('submissions.index')->with('success', 'تم إنشاء الإرسال بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في إنشاء الإرسال: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء الإرسال')->withInput();
        }
    }

    /**
     * عرض تفاصيل الإرسال
     */
    public function show($id)
    {
        $submission = Submission::with([
            'volunteerRequest',
            'reviewer',
            'assignedTo',
            'attachments',
            'workflow',
            'comments.user'
        ])->findOrFail($id);

        $statuses = Submission::getStatuses();
        $priorities = Submission::getPriorities();
        $workflowSteps = Workflow::getSteps();

        return view('submissions.show', compact('submission', 'statuses', 'priorities', 'workflowSteps'));
    }

    /**
     * عرض نموذج تعديل الإرسال
     */
    public function edit($id)
    {
        $submission = Submission::with([
            'volunteerRequest',
            'reviewer',
            'assignedTo',
            'attachments'
        ])->findOrFail($id);

        $volunteerRequests = VolunteerRequest::all();
        $users = User::all();
        $statuses = Submission::getStatuses();
        $priorities = Submission::getPriorities();

        return view('submissions.edit', compact('submission', 'volunteerRequests', 'users', 'statuses', 'priorities'));
    }

    /**
     * تحديث الإرسال
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'volunteer-request_id' => 'required|exists:volunteer-requests,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:2000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $submission = Submission::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $submission->update([
                'volunteer-request_id' => $request->input('volunteer-request_id'),
                'assigned_to' => $request->assigned_to,
                'priority' => $request->priority,
                'due_date' => $request->due_date,
                'notes' => $request->notes
            ]);

            // معالجة المرفقات الجديدة
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('submissions/' . $submission->id, 'public');
                    $submission->attachments()->create([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'file_type' => $file->getMimeType()
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('submissions.show', $submission)
                ->with('success', 'تم تحديث الإرسال بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في تحديث الإرسال: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الإرسال')->withInput();
        }
    }

    /**
     * تحديث حالة الإرسال
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in_review,approved,rejected,needs_revision,completed',
            'notes' => 'nullable|string|max:2000',
            'next_reviewer' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $submission = Submission::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $updateData = [
                'status' => $request->status,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'notes' => $request->notes
            ];

            if ($request->next_reviewer) {
                $updateData['assigned_to'] = $request->next_reviewer;
            }

            $submission->update($updateData);

            // تحديث سير المراجعة
            $this->updateWorkflowStatus($submission, $request->status);

            // إرسال إشعار للمستخدم المعني
            $this->sendStatusNotification($submission, $request->status);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الحالة بنجاح',
                'submission' => $submission->load(['volunteerRequest', 'reviewer'])
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في تحديث حالة الإرسال: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ أثناء تحديث الحالة'], 500);
        }
    }

    /**
     * تعيين مراجع للإرسال
     */
    public function assignReviewer(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $submission = Submission::findOrFail($id);
        $submission->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'in_review'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تعيين المراجع بنجاح',
            'submission' => $submission->load(['assignedTo'])
        ]);
    }

    /**
     * إضافة تعليق على الإرسال
     */
    public function addComment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $submission = Submission::findOrFail($id);
        $comment = $submission->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة التعليق بنجاح',
            'comment' => $comment->load('user')
        ]);
    }

    /**
     * الحصول على إحصائيات الإرسالات
     */
    private function getSubmissionStatistics()
    {
        return [
            'total' => Submission::count(),
            'pending' => Submission::where('status', 'pending')->count(),
            'in_review' => Submission::where('status', 'in_review')->count(),
            'approved' => Submission::where('status', 'approved')->count(),
            'rejected' => Submission::where('status', 'rejected')->count(),
            'completed' => Submission::where('status', 'completed')->count(),
            'overdue' => Submission::where('due_date', '<', now())->where('status', '!=', 'completed')->count()
        ];
    }

    /**
     * إنشاء سير مراجعة للإرسال
     */
    private function createWorkflowForSubmission($submission)
    {
        Workflow::create([
            'volunteer-request_id' => $submission->{'volunteer-request_id'},
            'assigned_to' => $submission->assigned_to,
            'status' => 'pending',
            'step' => 1,
            'step_name' => 'استلام الطلب',
            'priority' => $submission->priority,
            'due_date' => $submission->due_date,
            'notes' => $submission->notes
        ]);
    }

    /**
     * تحديث حالة سير المراجعة
     */
    private function updateWorkflowStatus($submission, $status)
    {
        $workflow = Workflow::where('volunteer-request_id', $submission->{'volunteer-request_id'})
            ->latest()
            ->first();

        if ($workflow) {
            $workflow->update([
                'status' => $status,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'notes' => $submission->notes
            ]);
        }

        // تحديث حالة طلب التطوع المرتبط
        $volunteerRequest = VolunteerRequest::where('id', $submission->{'volunteer-request_id'})->first();
        if ($volunteerRequest) {
            $volunteerRequest->update([
                'status' => $status,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now()
            ]);
        }
    }

    /**
     * إرسال إشعار بتغيير الحالة
     */
    private function sendStatusNotification($submission, $status)
    {
        // يمكن إضافة منطق إرسال الإشعارات هنا
        // مثل إرسال إيميل أو إشعار في النظام
        Log::info("تم تحديث حالة الإرسال {$submission->id} إلى {$status}");
    }

    /**
     * تصدير الإرسالات
     */
    public function export(Request $request)
    {
        $query = Submission::with(['volunteerRequest', 'reviewer', 'assignedTo']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        $submissions = $query->get();

        // يمكن إضافة منطق التصدير هنا
        return response()->json($submissions);
    }

    /**
     * البحث في الإرسالات
     */
    public function search(Request $request)
    {
        $query = Submission::with(['volunteerRequest', 'reviewer', 'assignedTo']);

        if ($request->search) {
            $query->whereHas('volunteerRequest', function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        $submissions = $query->paginate(15);

        return response()->json($submissions);
    }
} 