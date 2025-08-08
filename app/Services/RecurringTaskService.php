<?php

namespace App\Services;

use App\Models\Task;
use App\Models\RecurringTaskException;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecurringTaskService
{
    /**
     * Generate upcoming recurring task instances
     */
    public function generateUpcomingTasks(int $daysAhead = 30): array
    {
        $generatedTasks = [];
        $endDate = now()->addDays($daysAhead);
        
        $recurringTasks = Task::where('is_recurring', true)
            ->where('is_recurring_instance', false)
            ->where('recurring_active', true)
            ->get();

        foreach ($recurringTasks as $task) {
            $generated = $this->generateTaskInstances($task, $endDate);
            $generatedTasks = array_merge($generatedTasks, $generated);
        }

        return $generatedTasks;
    }

    /**
     * Generate task instances for a specific recurring task
     */
    public function generateTaskInstances(Task $task, Carbon $endDate = null): array
    {
        if (!$task->isRecurringMaster()) {
            return [];
        }

        $endDate = $endDate ?? now()->addDays(30);
        $generatedTasks = [];
        $currentDate = $task->next_occurrence_date ?? $task->recurrence_start_date ?? $task->start_date ?? now();

        // Ensure we don't generate past tasks unless specifically requested
        if ($currentDate->isPast()) {
            $currentDate = now();
        }

        $maxIterations = 100; // Prevent infinite loops
        $iterations = 0;

        while ($currentDate->lte($endDate) && $task->shouldContinueRecurring() && $iterations < $maxIterations) {
            $iterations++;

            // Check if this date should be skipped due to exceptions
            if ($this->shouldSkipDate($task, $currentDate)) {
                $currentDate = $task->calculateNextOccurrence($currentDate);
                if (!$currentDate) break;
                continue;
            }

            // Check if instance already exists
            if (!$this->instanceExistsForDate($task, $currentDate)) {
                try {
                    DB::beginTransaction();
                    
                    // Apply any date-specific modifications
                    $overrides = $this->getDateSpecificModifications($task, $currentDate);
                    
                    $instance = $task->createRecurringInstance($currentDate, $overrides);
                    $generatedTasks[] = $instance;
                    
                    // Update next occurrence date
                    $nextDate = $task->calculateNextOccurrence($currentDate);
                    $task->update(['next_occurrence_date' => $nextDate]);
                    
                    DB::commit();
                    
                    Log::info("Generated recurring task instance", [
                        'parent_task_id' => $task->id,
                        'instance_id' => $instance->id,
                        'occurrence_date' => $currentDate->toDateString()
                    ]);
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Failed to generate recurring task instance", [
                        'parent_task_id' => $task->id,
                        'occurrence_date' => $currentDate->toDateString(),
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Calculate next occurrence
            $currentDate = $task->calculateNextOccurrence($currentDate);
            if (!$currentDate) break;
        }

        return $generatedTasks;
    }

    /**
     * Check if a date should be skipped due to exceptions
     */
    protected function shouldSkipDate(Task $task, Carbon $date): bool
    {
        $exception = $task->getExceptionForDate($date);
        
        if (!$exception) {
            return false;
        }

        return $exception->exception_type === RecurringTaskException::TYPE_SKIP;
    }

    /**
     * Get date-specific modifications from exceptions
     */
    protected function getDateSpecificModifications(Task $task, Carbon $date): array
    {
        $exception = $task->getExceptionForDate($date);
        
        if (!$exception || $exception->exception_type !== RecurringTaskException::TYPE_MODIFY) {
            return [];
        }

        $modifications = $exception->modified_data ?? [];
        
        // If it's a reschedule, update the dates
        if ($exception->exception_type === RecurringTaskException::TYPE_RESCHEDULE && $exception->new_date) {
            $modifications['start_date'] = $exception->new_date;
            $modifications['deadline'] = $exception->new_date->copy()->addDays($task->getDurationInDays() ?? 1);
        }

        return $modifications;
    }

    /**
     * Check if an instance already exists for a specific date
     */
    protected function instanceExistsForDate(Task $task, Carbon $date): bool
    {
        return Task::where('parent_task_id', $task->id)
            ->whereDate('start_date', $date->toDateString())
            ->exists();
    }

    /**
     * Create an exception for a recurring task
     */
    public function createException(
        Task $task,
        Carbon $date,
        string $type,
        array $data = []
    ): RecurringTaskException {
        return RecurringTaskException::create([
            'parent_task_id' => $task->id,
            'exception_date' => $date,
            'exception_type' => $type,
            'new_date' => $data['new_date'] ?? null,
            'modified_data' => $data['modified_data'] ?? null,
            'reason' => $data['reason'] ?? null,
            'created_by' => auth()->id()
        ]);
    }

    /**
     * Skip a specific occurrence
     */
    public function skipOccurrence(Task $task, Carbon $date, string $reason = null): RecurringTaskException
    {
        return $this->createException($task, $date, RecurringTaskException::TYPE_SKIP, [
            'reason' => $reason
        ]);
    }

    /**
     * Reschedule a specific occurrence
     */
    public function rescheduleOccurrence(
        Task $task,
        Carbon $originalDate,
        Carbon $newDate,
        string $reason = null
    ): RecurringTaskException {
        return $this->createException($task, $originalDate, RecurringTaskException::TYPE_RESCHEDULE, [
            'new_date' => $newDate,
            'reason' => $reason
        ]);
    }

    /**
     * Modify a specific occurrence
     */
    public function modifyOccurrence(
        Task $task,
        Carbon $date,
        array $modifications,
        string $reason = null
    ): RecurringTaskException {
        return $this->createException($task, $date, RecurringTaskException::TYPE_MODIFY, [
            'modified_data' => $modifications,
            'reason' => $reason
        ]);
    }

    /**
     * Update recurring task configuration
     */
    public function updateRecurrenceConfig(Task $task, array $config): Task
    {
        if (!$task->isRecurringMaster()) {
            throw new \InvalidArgumentException('Task is not a recurring master task');
        }

        $task->update([
            'recurrence_pattern' => $config['pattern'] ?? $task->recurrence_pattern,
            'recurrence_config' => $config['config'] ?? $task->recurrence_config,
            'recurrence_start_date' => $config['start_date'] ?? $task->recurrence_start_date,
            'recurrence_end_date' => $config['end_date'] ?? $task->recurrence_end_date,
            'recurrence_max_occurrences' => $config['max_occurrences'] ?? $task->recurrence_max_occurrences,
            'recurring_active' => $config['active'] ?? $task->recurring_active,
        ]);

        // Recalculate next occurrence if active
        if ($task->recurring_active) {
            $task->updateNextOccurrence();
        }

        return $task->fresh();
    }

    /**
     * Get recurring task statistics
     */
    public function getRecurringTaskStats(): array
    {
        $activeRecurring = Task::where('is_recurring', true)
            ->where('is_recurring_instance', false)
            ->where('recurring_active', true)
            ->count();

        $totalInstances = Task::where('is_recurring_instance', true)->count();
        
        $upcomingInstances = Task::where('is_recurring_instance', true)
            ->where('start_date', '>=', now())
            ->where('status', '!=', Task::STATUS_COMPLETED)
            ->count();

        $overdueInstances = Task::where('is_recurring_instance', true)
            ->where('deadline', '<', now())
            ->where('status', '!=', Task::STATUS_COMPLETED)
            ->count();

        return [
            'active_recurring_tasks' => $activeRecurring,
            'total_instances' => $totalInstances,
            'upcoming_instances' => $upcomingInstances,
            'overdue_instances' => $overdueInstances
        ];
    }

    /**
     * Get upcoming recurring task instances
     */
    public function getUpcomingInstances(int $days = 7): Collection
    {
        $endDate = now()->addDays($days);
        
        return Task::where('is_recurring_instance', true)
            ->whereBetween('start_date', [now(), $endDate])
            ->where('status', '!=', Task::STATUS_COMPLETED)
            ->with(['parentTask', 'category', 'assignments.user'])
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Clean up old recurring task instances
     */
    public function cleanupOldInstances(int $daysOld = 90): int
    {
        $cutoffDate = now()->subDays($daysOld);
        
        $deletedCount = Task::where('is_recurring_instance', true)
            ->where('status', Task::STATUS_COMPLETED)
            ->where('completed_at', '<', $cutoffDate)
            ->delete();

        Log::info("Cleaned up old recurring task instances", [
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toDateString()
        ]);

        return $deletedCount;
    }

    /**
     * Validate recurrence configuration
     */
    public function validateRecurrenceConfig(array $config): array
    {
        $errors = [];

        if (!isset($config['pattern']) || !in_array($config['pattern'], [
            Task::RECURRENCE_DAILY,
            Task::RECURRENCE_WEEKLY,
            Task::RECURRENCE_MONTHLY,
            Task::RECURRENCE_YEARLY,
            Task::RECURRENCE_CUSTOM
        ])) {
            $errors[] = 'نمط التكرار غير صحيح';
        }

        if (isset($config['config']['interval']) && (!is_numeric($config['config']['interval']) || $config['config']['interval'] < 1)) {
            $errors[] = 'فترة التكرار يجب أن تكون رقم أكبر من صفر';
        }

        if (isset($config['end_date']) && isset($config['start_date'])) {
            $startDate = Carbon::parse($config['start_date']);
            $endDate = Carbon::parse($config['end_date']);
            
            if ($endDate->lte($startDate)) {
                $errors[] = 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء';
            }
        }

        if (isset($config['max_occurrences']) && (!is_numeric($config['max_occurrences']) || $config['max_occurrences'] < 1)) {
            $errors[] = 'العدد الأقصى للتكرارات يجب أن يكون رقم أكبر من صفر';
        }

        return $errors;
    }

    /**
     * Preview upcoming occurrences for a recurrence configuration
     */
    public function previewOccurrences(array $config, int $count = 10): array
    {
        $tempTask = new Task();
        $tempTask->recurrence_pattern = $config['pattern'];
        $tempTask->recurrence_config = $config['config'] ?? [];
        $tempTask->is_recurring = true;
        $tempTask->recurring_active = true;
        
        $startDate = Carbon::parse($config['start_date'] ?? now());
        $occurrences = [];
        $currentDate = $startDate;
        
        for ($i = 0; $i < $count; $i++) {
            $occurrences[] = $currentDate->format('Y-m-d');
            $currentDate = $tempTask->calculateNextOccurrence($currentDate);
            
            if (!$currentDate) break;
            
            if (isset($config['end_date']) && $currentDate->gt(Carbon::parse($config['end_date']))) {
                break;
            }
        }
        
        return $occurrences;
    }
}