<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\Models\VolunteerRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class WorkflowController extends Controller
{
    public function index() {
        $workflows = Workflow::with(['volunteerRequest', 'reviewer', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $statuses = Workflow::getStatuses();
        $steps = Workflow::getSteps();
        $priorities = Workflow::getPriorities();
        
        return view('workflows.index', compact('workflows', 'statuses', 'steps', 'priorities'));
    }

    public function create() {
        $volunteerRequests = VolunteerRequest::all();
        $users = User::all();
        $statuses = Workflow::getStatuses();
        $steps = Workflow::getSteps();
        $priorities = Workflow::getPriorities();
        
        return view('workflows.create', compact('volunteerRequests', 'users', 'statuses', 'steps', 'priorities'));
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'volunteer-request_id' => 'required|exists:volunteer-requests,id',
            'reviewed_by' => 'nullable|exists:users,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,in_review,approved,rejected,needs_revision,completed,cancelled',
            'notes' => 'nullable|string|max:2000',
            'step' => 'required|integer|min:1|max:6',
            'step_name' => 'nullable|string|max:255',
            'is_completed' => 'boolean',
            'next_step' => 'nullable|integer|min:1|max:6',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_duration' => 'nullable|integer|min:1',
            'due_date' => 'nullable|date|after:now'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // إذا كانت الحالة موافق عليها، تحديث وقت المراجعة
        if ($request->status === 'approved' || $request->status === 'rejected') {
            $request->merge(['reviewed_at' => now()]);
        }

        Workflow::create($request->all());
        return redirect()->route('workflows.index')->with('success', 'تم إنشاء سير العمل بنجاح');
    }

    public function show($id) {
        $workflow = Workflow::with(['volunteerRequest', 'reviewer', 'assignedTo'])->findOrFail($id);
        $statuses = Workflow::getStatuses();
        $steps = Workflow::getSteps();
        $priorities = Workflow::getPriorities();
        
        return view('workflows.show', compact('workflow', 'statuses', 'steps', 'priorities'));
    }

    public function edit($id) {
        $workflow = Workflow::findOrFail($id);
        $volunteerRequests = VolunteerRequest::all();
        $users = User::all();
        $statuses = Workflow::getStatuses();
        $steps = Workflow::getSteps();
        $priorities = Workflow::getPriorities();
        
        return view('workflows.edit', compact('workflow', 'volunteerRequests', 'users', 'statuses', 'steps', 'priorities'));
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'volunteer-request_id' => 'required|exists:volunteer-requests,id',
            'reviewed_by' => 'nullable|exists:users,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,in_review,approved,rejected,needs_revision,completed,cancelled',
            'notes' => 'nullable|string|max:2000',
            'step' => 'required|integer|min:1|max:6',
            'step_name' => 'nullable|string|max:255',
            'is_completed' => 'boolean',
            'next_step' => 'nullable|integer|min:1|max:6',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_duration' => 'nullable|integer|min:1',
            'due_date' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $workflow = Workflow::findOrFail($id);
        
        // إذا كانت الحالة موافق عليها أو مرفوضة، تحديث وقت المراجعة
        if (($request->status === 'approved' || $request->status === 'rejected') && !$workflow->reviewed_at) {
            $request->merge(['reviewed_at' => now()]);
        }

        $workflow->update($request->all());
        return redirect()->route('workflows.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy($id) {
        $workflow = Workflow::findOrFail($id);
        $workflow->delete();
        return redirect()->route('workflows.index')->with('success', 'تم الحذف بنجاح');
    }

    // API methods
    public function updateStatus(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in_review,approved,rejected,needs_revision,completed,cancelled',
            'notes' => 'nullable|string|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $workflow = Workflow::findOrFail($id);
        
        $updateData = [
            'status' => $request->status,
            'notes' => $request->notes,
            'reviewed_by' => Auth::id()
        ];

        // إذا كانت الحالة موافق عليها أو مرفوضة، تحديث وقت المراجعة
        if ($request->status === 'approved' || $request->status === 'rejected') {
            $updateData['reviewed_at'] = now();
            $updateData['is_completed'] = true;
        }

        $workflow->update($updateData);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الحالة بنجاح',
            'workflow' => $workflow->load(['volunteerRequest', 'reviewer'])
        ]);
    }

    public function getWorkflowsByStatus($status) {
        $workflows = Workflow::with(['volunteerRequest', 'reviewer', 'assignedTo'])
            ->byStatus($status)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($workflows);
    }

    public function getPendingWorkflows() {
        $workflows = Workflow::with(['volunteerRequest', 'reviewer', 'assignedTo'])
            ->pending()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
        
        return response()->json($workflows);
    }

    public function assignToUser(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $workflow = Workflow::findOrFail($id);
        $workflow->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'in_review'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تعيين المهمة بنجاح',
            'workflow' => $workflow->load(['volunteerRequest', 'assignedTo'])
        ]);
    }

    public function proceedToNextStep($id) {
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
            'status' => 'pending'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'تم الانتقال للخطوة التالية بنجاح',
            'workflow' => $workflow->load(['volunteerRequest', 'reviewer'])
        ]);
    }
}
