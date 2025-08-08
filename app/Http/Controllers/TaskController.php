<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Services\NotificationService;
use App\Events\TaskAssigned;
use App\Events\TaskStatusChanged;
use App\Events\TaskCompleted;
use App\Events\TaskDeadlineApproaching;

class TaskController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Task::with(['project', 'assignments.user', 'category']);
        
        // Filter by project if specified
        if (request('project')) {
            $query->where('project_id', request('project'));
        }
        
        $tasks = $query->orderByDesc('created_at')->get();
        $projects = \App\Models\Project::all();
        return view('tasks.index', compact('tasks', 'projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:new,in_progress,pending,completed,cancelled',
                'deadline' => 'required|date',
                'project_id' => 'required|exists:projects,id',
                'priority' => 'nullable|in:urgent,high,medium,low',
                'start_date' => 'nullable|date',
                'user_ids' => 'nullable|array',
                'user_ids.*' => 'exists:users,id',
            ]);
            
            // الحصول على أول فئة متاحة أو إنشاء فئة افتراضية
            $category = \App\Models\Category::first();
            if (!$category) {
                $category = \App\Models\Category::create([
                    'name' => 'عام',
                    'slug' => 'general',
                    'description' => 'فئة عامة للمهام',
                    'is_active' => true
                ]);
            }
            
            $validated['category_id'] = $category->id;
            $validated['created_by'] = auth()->id();
            $validated['start_date'] = $request->input('start_date') ?? now();
            
            // Remove user_ids from validated data before creating task
            unset($validated['user_ids']);
            $task = Task::create($validated);
            
            // Record task creation in history
            app(\App\Services\TaskHistoryService::class)->recordTaskCreated($task);
            
            // حفظ المكلفين
            if ($request->has('user_ids') && is_array($request->user_ids) && !empty($request->user_ids)) {
                foreach ($request->user_ids as $userId) {
                    $assignment = $task->assignments()->create([
                        'user_id' => $userId,
                        'assigned_at' => now(),
                        'status' => 'assigned',
                    ]);
                    
                    // إرسال إشعار للمستخدم المكلف
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        // Dispatch event for task assignment with assignment data
                        event(new TaskAssigned($task, $user, auth()->user(), $assignment));
                    }
                }
            }
            
            Toastr::success('تمت إضافة المهمة بنجاح', 'نجاح');
            return redirect()->route('tasks.index');
        } catch (\Exception $e) {
            Toastr::error('حدث خطأ أثناء إضافة المهمة', 'خطأ');
            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::with(['project', 'assignments.user', 'category', 'taskDependencies.prerequisiteTask', 'dependents.dependentTask'])->findOrFail($id);
        $allTasks = Task::where('id', '!=', $task->id)->get();
        $availableVolunteers = \App\Models\User::where('status', 'active')->get();
        return view('tasks.show', compact('task', 'allTasks', 'availableVolunteers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $task = Task::with(['project', 'assignments.user'])->findOrFail($id);
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:new,in_progress,pending,completed,cancelled',
                'deadline' => 'required|date',
                'project_id' => 'required|exists:projects,id',
                'priority' => 'nullable|in:urgent,high,medium,low',
                'start_date' => 'nullable|date',
                'user_ids' => 'nullable|array',
                'user_ids.*' => 'exists:users,id',
            ]);
            
            $task = Task::findOrFail($id);
            $oldStatus = $task->status;
            
            // Remove user_ids from validated data before updating task
            unset($validated['user_ids']);
            $task->update($validated);
            
            // Handle assignments
            if ($request->has('user_ids')) {
                // Delete existing assignments
                $task->assignments()->delete();
                
                // Create new assignments if user_ids is not empty
                if (!empty($request->user_ids)) {
                    foreach ($request->user_ids as $userId) {
                        $assignment = $task->assignments()->create([
                            'user_id' => $userId,
                            'assigned_at' => now(),
                            'status' => 'assigned',
                        ]);
                        
                        // إرسال إشعار للمستخدم المكلف
                        $user = \App\Models\User::find($userId);
                        if ($user) {
                            // Dispatch event for task assignment with assignment data
                            event(new TaskAssigned($task, $user, auth()->user(), $assignment));
                        }
                    }
                }
            }
            
            // Check if status changed and dispatch event
            if ($oldStatus !== $task->status) {
                // Record status change in history
                app(\App\Services\TaskHistoryService::class)->recordStatusChange($task, $oldStatus, $task->status);
                
                event(new TaskStatusChanged($task, $oldStatus, $task->status, auth()->user()));
                
                // If task is completed, dispatch completion event
                if ($task->status === 'completed') {
                    event(new TaskCompleted($task, auth()->user()));
                    app(\App\Services\TaskHistoryService::class)->recordTaskCompleted($task);
                }
            }
            
            Toastr::success('تم تحديث المهمة بنجاح', 'نجاح');
            return redirect()->route('tasks.index');
        } catch (\Exception $e) {
            Toastr::error('حدث خطأ أثناء تحديث المهمة', 'خطأ');
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();
            
            Toastr::success('تم حذف المهمة بنجاح', 'نجاح');
            return redirect()->route('tasks.index');
        } catch (\Exception $e) {
            Toastr::error('حدث خطأ أثناء حذف المهمة', 'خطأ');
            return back();
        }
    }

    /**
     * Update task status
     */
    public function updateStatus(Request $request, Task $task)
    {
        try {
            \Log::info('Status update request', [
                'task_id' => $task->id,
                'old_status' => $task->status,
                'new_status' => $request->input('status'),
                'user_id' => auth()->id()
            ]);

            // Validate request
            $request->validate([
                'status' => 'required|in:new,in_progress,pending,completed,cancelled',
                'progress' => 'nullable|integer|min:0|max:100'
            ]);

            $oldStatus = $task->status;
            $newStatus = $request->input('status');
            $progress = $request->input('progress', $task->progress);
            $oldProgress = $task->progress;

        // تحديث التقدم إذا تم توفيره
        if ($progress !== null) {
            $task->progress = max(0, min(100, (int)$progress));
        }

        // إذا كانت الحالة الجديدة هي نفس الحالة الحالية، نقوم بتحديث التاريخ فقط
        if ($oldStatus === $newStatus) {
            $task->updated_at = now();
            $task->save();
            
            // Record progress update in history
            if ($oldProgress !== $task->progress) {
                app(\App\Services\TaskHistoryService::class)->recordProgressUpdate($task, $oldProgress, $task->progress);
            }
            
            $statusLabels = [
                'new' => 'جديدة',
                'in_progress' => 'قيد التنفيذ',
                'pending' => 'معلقة',
                'completed' => 'منجزة',
                'cancelled' => 'ملغاة',
            ];
            
            return response()->json([
                'message' => 'تم تحديث التقدم بنجاح إلى ' . $task->progress . '%',
                'task' => $task,
                'success' => true
            ], 200);
        }

        $allowedTransitions = [
            'new' => ['in_progress', 'pending', 'cancelled'],
            'in_progress' => ['pending', 'completed', 'cancelled'],
            'pending' => ['in_progress', 'completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        if (!in_array($newStatus, $allowedTransitions[$task->status])) {
            $statusLabels = [
                'new' => 'جديدة',
                'in_progress' => 'قيد التنفيذ',
                'pending' => 'معلقة',
                'completed' => 'منجزة',
                'cancelled' => 'ملغاة',
            ];
            
            return response()->json([
                'message' => 'لا يمكن الانتقال من حالة "' . ($statusLabels[$task->status] ?? $task->status) . '" إلى حالة "' . ($statusLabels[$newStatus] ?? $newStatus) . '"',
                'error' => 'invalid_transition'
            ], 400);
        }

        $task->status = $newStatus;
        $task->save();

        // Record status change in history
        app(\App\Services\TaskHistoryService::class)->recordStatusChange($task, $oldStatus, $newStatus);

        // Dispatch status change event
        event(new TaskStatusChanged($task, $oldStatus, $newStatus, auth()->user()));
        
        // If task is completed, dispatch completion event
        if ($newStatus === 'completed') {
            event(new TaskCompleted($task, auth()->user()));
            app(\App\Services\TaskHistoryService::class)->recordTaskCompleted($task);
        }

        $statusLabels = [
            'new' => 'جديدة',
            'in_progress' => 'قيد التنفيذ',
            'pending' => 'معلقة',
            'completed' => 'منجزة',
            'cancelled' => 'ملغاة',
        ];

        return response()->json([
            'message' => 'تم تحديث حالة المهمة بنجاح إلى "' . ($statusLabels[$newStatus] ?? $newStatus) . '"',
            'task' => $task,
            'success' => true
        ]);
        
        } catch (\Exception $e) {
            \Log::error('Error updating task status', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'حدث خطأ أثناء تحديث حالة المهمة: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    /**
     * Show dependencies form
     */
    public function showDependenciesForm(Task $task)
    {
        $allTasks = Task::where('id', '!=', $task->id)->get(); 
        return view('tasks.dependencies', compact('task', 'allTasks'));
    }

    /**
     * Store task dependency
     */
    public function storeDependency(Request $request, Task $task)
    {
        try {
            $request->validate([
                'depends_on_id' => 'required|exists:tasks,id',
                'dependency_type' => 'required|in:finish_to_start,start_to_start,finish_to_finish,start_to_finish',
            ]);
            
            $dependsOnId = $request->input('depends_on_id');
            $dependencyType = $request->input('dependency_type');
            
            // منع تكرار التبعية
            if (!$task->dependencies()->where('depends_on_task_id', $dependsOnId)->exists()) {
                $task->dependencies()->attach($dependsOnId, [
                    'dependency_type' => $dependencyType,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // إرسال إشعارات التبعية
                $dependencyTask = Task::find($dependsOnId);
                if ($dependencyTask) {
                    $this->notificationService->sendTaskDependencyNotification($task, $dependencyTask, $dependencyType, auth()->user());
                }
                
                Toastr::success('تم إضافة التبعية بنجاح', 'نجاح');
            } else {
                Toastr::warning('التبعية موجودة مسبقاً', 'تحذير');
            }
            
            return redirect()->route('tasks.show', $task->id);
        } catch (\Exception $e) {
            Toastr::error('حدث خطأ أثناء إضافة التبعية', 'خطأ');
            return back()->withInput();
        }
    }

    /**
     * Remove task dependency
     */
    public function destroyDependency(Task $task, $dependencyId)
    {
        try {
            $task->dependencies()->detach($dependencyId);
            Toastr::success('تم حذف التبعية بنجاح', 'نجاح');
            return redirect()->route('tasks.show', $task->id);
        } catch (\Exception $e) {
            Toastr::error('حدث خطأ أثناء حذف التبعية', 'خطأ');
            return back();
        }
    }

    /**
     * Assign task to a volunteer
     */
    public function assign(Request $request, Task $task)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);
            
            $userId = $request->input('user_id');
            
            // Check if user is already assigned to this task
            if ($task->assignments()->where('user_id', $userId)->exists()) {
                Toastr::warning('المستخدم مكلف بهذه المهمة مسبقاً', 'تحذير');
                return redirect()->route('tasks.show', $task->id);
            }
            
            // Create assignment
            $assignment = $task->assignments()->create([
                'user_id' => $userId,
                'assigned_at' => now(),
                'status' => 'assigned',
            ]);
            
            // Get user and dispatch event
            $user = \App\Models\User::find($userId);
            if ($user) {
                event(new TaskAssigned($task, $user, auth()->user(), $assignment));
            }
            
            Toastr::success('تم تخصيص المهمة بنجاح', 'نجاح');
            return redirect()->route('tasks.show', $task->id);
        } catch (\Exception $e) {
            Toastr::error('حدث خطأ أثناء تخصيص المهمة', 'خطأ');
            return back()->withInput();
        }
    }

    /**
     * تحديث تقدم المهمة
     */
    public function updateProgress(Request $request, Task $task)
    {
        try {
            $request->validate([
                'progress' => 'required|integer|min:0|max:100',
                'note' => 'nullable|string|max:500'
            ]);

            $oldProgress = $task->progress;
            $newProgress = (int) $request->input('progress');
            $note = $request->input('note');

            // تحديث تقدم المهمة
            $task->progress = $newProgress;
            $task->save();

            // تسجيل التحديث في التاريخ
            app(\App\Services\TaskHistoryService::class)->recordProgressUpdate(
                $task, 
                $oldProgress, 
                $newProgress, 
                $note
            );

            // إرسال إشعار إذا كان التقدم 100%
            if ($newProgress == 100 && $oldProgress < 100) {
                event(new TaskCompleted($task, auth()->user()));
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث التقدم بنجاح إلى ' . $newProgress . '%',
                'task' => $task->fresh(),
                'change' => $newProgress - $oldProgress
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating task progress', [
                'task_id' => $task->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث التقدم: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على سجل تقدم المهمة
     */
    public function progressHistory(Task $task)
    {
        try {
            $history = $task->history()
                ->where('action_type', 'progress_update')
                ->with('user')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(function ($record) {
                    return [
                        'created_at' => $record->created_at->format('Y-m-d H:i'),
                        'progress' => $record->new_value,
                        'change' => (int)$record->new_value - (int)$record->old_value,
                        'note' => $record->description,
                        'user_name' => $record->user ? $record->user->name : 'غير محدد'
                    ];
                });

            return response()->json([
                'success' => true,
                'history' => $history
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching task progress history', [
                'task_id' => $task->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب سجل التقدم'
            ], 500);
        }
    }

    /**
     * الحصول على إحصائيات تقدم المهمة
     */
    public function progressStats(Task $task)
    {
        try {
            $stats = [
                'current_progress' => $task->progress,
                'days_remaining' => $task->getRemainingDays(),
                'is_overdue' => $task->isOverdue(),
                'completion_rate' => $this->calculateCompletionRate($task),
                'progress_trend' => $this->getProgressTrend($task),
                'estimated_completion' => $this->estimateCompletionDate($task)
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حساب الإحصائيات'
            ], 500);
        }
    }

    /**
     * حساب معدل الإنجاز
     */
    private function calculateCompletionRate(Task $task)
    {
        if (!$task->deadline || !$task->start_date) {
            return 0;
        }

        $totalDays = $task->start_date->diffInDays($task->deadline);
        $elapsedDays = $task->start_date->diffInDays(now());
        
        if ($totalDays <= 0) {
            return 100;
        }

        return min(100, ($elapsedDays / $totalDays) * 100);
    }

    /**
     * الحصول على اتجاه التقدم
     */
    private function getProgressTrend(Task $task)
    {
        $recentHistory = $task->history()
            ->where('action_type', 'progress_update')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        if ($recentHistory->count() < 2) {
            return 'stable';
        }

        $changes = [];
        for ($i = 0; $i < $recentHistory->count() - 1; $i++) {
            $changes[] = (int)$recentHistory[$i]->new_value - (int)$recentHistory[$i + 1]->new_value;
        }

        $avgChange = array_sum($changes) / count($changes);

        if ($avgChange > 5) {
            return 'increasing';
        } elseif ($avgChange < -5) {
            return 'decreasing';
        } else {
            return 'stable';
        }
    }

    /**
     * تقدير تاريخ الإنجاز
     */
    private function estimateCompletionDate(Task $task)
    {
        if ($task->progress >= 100) {
            return $task->updated_at->format('Y-m-d');
        }

        if ($task->progress <= 0) {
            return null;
        }

        $recentHistory = $task->history()
            ->where('action_type', 'progress_update')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        if ($recentHistory->count() < 2) {
            return null;
        }

        // حساب معدل التقدم اليومي
        $totalProgress = 0;
        $totalDays = 0;

        for ($i = 0; $i < $recentHistory->count() - 1; $i++) {
            $progressChange = (int)$recentHistory[$i]->new_value - (int)$recentHistory[$i + 1]->new_value;
            $daysChange = $recentHistory[$i]->created_at->diffInDays($recentHistory[$i + 1]->created_at);
            
            if ($daysChange > 0) {
                $totalProgress += $progressChange;
                $totalDays += $daysChange;
            }
        }

        if ($totalDays <= 0) {
            return null;
        }

        $dailyProgressRate = $totalProgress / $totalDays;
        $remainingProgress = 100 - $task->progress;
        $estimatedDays = $dailyProgressRate > 0 ? ceil($remainingProgress / $dailyProgressRate) : null;

        if ($estimatedDays) {
            return now()->addDays($estimatedDays)->format('Y-m-d');
        }

        return null;
    }
}
