<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\RecurringTaskException;
use App\Services\RecurringTaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RecurringTaskController extends Controller
{
    protected RecurringTaskService $recurringTaskService;

    public function __construct(RecurringTaskService $recurringTaskService)
    {
        $this->recurringTaskService = $recurringTaskService;
    }

    /**
     * Display recurring tasks management page
     */
    public function index(Request $request)
    {
        $recurringTasks = Task::where('is_recurring', true)
            ->where('is_recurring_instance', false)
            ->with(['category', 'creator', 'recurringInstances' => function($query) {
                $query->orderBy('start_date', 'desc')->limit(5);
            }])
            ->when($request->status, function($query) use ($request) {
                if ($request->status === 'active') {
                    $query->where('recurring_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('recurring_active', false);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = $this->recurringTaskService->getRecurringTaskStats();
        $upcomingTasks = $this->recurringTaskService->getUpcomingInstances(7);

        return view('recurring-tasks.index', compact('recurringTasks', 'stats', 'upcomingTasks'));
    }

    /**
     * Show recurring task details and instances
     */
    public function show(Task $task)
    {
        if (!$task->isRecurringMaster()) {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'هذه المهمة ليست مهمة متكررة رئيسية');
        }

        $task->load(['category', 'creator', 'recurringInstances.assignments.user', 'recurringExceptions']);
        
        $instances = $task->recurringInstances()
            ->with(['assignments.user'])
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        $upcomingOccurrences = $this->getUpcomingOccurrences($task, 10);

        return view('recurring-tasks.show', compact('task', 'instances', 'upcomingOccurrences'));
    }

    /**
     * Show form for editing recurring task configuration
     */
    public function edit(Task $task)
    {
        if (!$task->isRecurringMaster()) {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'هذه المهمة ليست مهمة متكررة رئيسية');
        }

        $recurrencePatterns = Task::getRecurrencePatterns();
        
        return view('recurring-tasks.edit', compact('task', 'recurrencePatterns'));
    }

    /**
     * Update recurring task configuration
     */
    public function update(Request $request, Task $task)
    {
        if (!$task->isRecurringMaster()) {
            return response()->json(['error' => 'المهمة ليست مهمة متكررة رئيسية'], 400);
        }

        $validator = Validator::make($request->all(), [
            'recurrence_pattern' => 'required|in:daily,weekly,monthly,yearly,custom',
            'recurrence_config' => 'array',
            'recurrence_start_date' => 'nullable|date',
            'recurrence_end_date' => 'nullable|date|after:recurrence_start_date',
            'recurrence_max_occurrences' => 'nullable|integer|min:1',
            'recurring_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $config = [
            'pattern' => $request->recurrence_pattern,
            'config' => $request->recurrence_config ?? [],
            'start_date' => $request->recurrence_start_date,
            'end_date' => $request->recurrence_end_date,
            'max_occurrences' => $request->recurrence_max_occurrences,
            'active' => $request->boolean('recurring_active')
        ];

        $errors = $this->recurringTaskService->validateRecurrenceConfig($config);
        
        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        try {
            $updatedTask = $this->recurringTaskService->updateRecurrenceConfig($task, $config);
            
            return response()->json([
                'message' => 'تم تحديث إعدادات التكرار بنجاح',
                'task' => $updatedTask->load(['category', 'creator'])
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء تحديث إعدادات التكرار'], 500);
        }
    }

    /**
     * Generate upcoming task instances manually
     */
    public function generate(Request $request, Task $task)
    {
        if (!$task->isRecurringMaster()) {
            return response()->json(['error' => 'المهمة ليست مهمة متكررة رئيسية'], 400);
        }

        $days = $request->input('days', 30);
        $endDate = now()->addDays($days);

        try {
            $generatedTasks = $this->recurringTaskService->generateTaskInstances($task, $endDate);
            
            return response()->json([
                'message' => 'تم إنشاء ' . count($generatedTasks) . ' مهمة جديدة',
                'generated_count' => count($generatedTasks)
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء إنشاء المهام'], 500);
        }
    }

    /**
     * Preview upcoming occurrences for recurrence configuration
     */
    public function preview(Request $request)
    {
        $config = [
            'pattern' => $request->input('pattern'),
            'config' => $request->input('config', []),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date')
        ];

        $errors = $this->recurringTaskService->validateRecurrenceConfig($config);
        
        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        $occurrences = $this->recurringTaskService->previewOccurrences($config, 10);
        
        return response()->json(['occurrences' => $occurrences]);
    }

    /**
     * Manage recurring task exceptions
     */
    public function exceptions(Task $task)
    {
        if (!$task->isRecurringMaster()) {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'هذه المهمة ليست مهمة متكررة رئيسية');
        }

        $exceptions = $task->recurringExceptions()
            ->with('creator')
            ->orderBy('exception_date', 'desc')
            ->paginate(15);

        return view('recurring-tasks.exceptions', compact('task', 'exceptions'));
    }

    /**
     * Create an exception for a recurring task
     */
    public function createException(Request $request, Task $task)
    {
        if (!$task->isRecurringMaster()) {
            return response()->json(['error' => 'المهمة ليست مهمة متكررة رئيسية'], 400);
        }

        $validator = Validator::make($request->all(), [
            'exception_date' => 'required|date',
            'exception_type' => 'required|in:skip,reschedule,modify',
            'new_date' => 'nullable|date|required_if:exception_type,reschedule',
            'modified_data' => 'nullable|array',
            'reason' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $exceptionDate = Carbon::parse($request->exception_date);
            
            // Check if exception already exists
            if ($task->hasExceptionOnDate($exceptionDate)) {
                return response()->json(['error' => 'يوجد استثناء مسجل لهذا التاريخ مسبقاً'], 400);
            }

            $data = [
                'new_date' => $request->new_date ? Carbon::parse($request->new_date) : null,
                'modified_data' => $request->modified_data,
                'reason' => $request->reason
            ];

            $exception = $this->recurringTaskService->createException(
                $task,
                $exceptionDate,
                $request->exception_type,
                $data
            );

            return response()->json([
                'message' => 'تم إنشاء الاستثناء بنجاح',
                'exception' => $exception->load('creator')
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء إنشاء الاستثناء'], 500);
        }
    }

    /**
     * Delete a recurring task exception
     */
    public function deleteException(Task $task, RecurringTaskException $exception)
    {
        if ($exception->parent_task_id !== $task->id) {
            return response()->json(['error' => 'الاستثناء غير مرتبط بهذه المهمة'], 400);
        }

        try {
            $exception->delete();
            
            return response()->json(['message' => 'تم حذف الاستثناء بنجاح']);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء حذف الاستثناء'], 500);
        }
    }

    /**
     * Toggle recurring task active status
     */
    public function toggleActive(Task $task)
    {
        if (!$task->isRecurringMaster()) {
            return response()->json(['error' => 'المهمة ليست مهمة متكررة رئيسية'], 400);
        }

        try {
            if ($task->recurring_active) {
                $task->deactivateRecurring();
                $message = 'تم إيقاف التكرار';
            } else {
                $task->activateRecurring();
                $message = 'تم تفعيل التكرار';
            }

            return response()->json([
                'message' => $message,
                'active' => $task->fresh()->recurring_active
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء تغيير حالة التكرار'], 500);
        }
    }

    /**
     * Get statistics for recurring tasks
     */
    public function statistics()
    {
        $stats = $this->recurringTaskService->getRecurringTaskStats();
        $upcomingTasks = $this->recurringTaskService->getUpcomingInstances(30);
        
        return view('recurring-tasks.statistics', compact('stats', 'upcomingTasks'));
    }

    /**
     * Get upcoming occurrences for a task
     */
    protected function getUpcomingOccurrences(Task $task, int $count = 10): array
    {
        if (!$task->recurring_active) {
            return [];
        }

        $occurrences = [];
        $currentDate = $task->next_occurrence_date ?? now();
        $iterations = 0;

        while (count($occurrences) < $count && $iterations < 50 && $task->shouldContinueRecurring()) {
            $iterations++;
            
            if ($currentDate && $currentDate->isFuture()) {
                $hasException = $task->hasExceptionOnDate($currentDate);
                $exception = $hasException ? $task->getExceptionForDate($currentDate) : null;
                
                $occurrences[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'has_exception' => $hasException,
                    'exception_type' => $exception ? $exception->exception_type : null,
                    'exception_label' => $exception ? $exception->exception_type_label : null
                ];
            }

            $currentDate = $task->calculateNextOccurrence($currentDate);
            if (!$currentDate) break;
        }

        return $occurrences;
    }
}