<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskDependency;
use App\Models\Assignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskWorkflowService
{
    /**
     * Transition task to a new status
     */
    public function transitionTaskStatus(Task $task, string $newStatus, array $data = []): bool
    {
        try {
            DB::beginTransaction();

            // Validate transition
            if (!$this->canTransitionToStatus($task, $newStatus)) {
                throw new \Exception("Cannot transition task from {$task->status} to {$newStatus}");
            }

            $oldStatus = $task->status;
            
            // Update task status
            $task->update([
                'status' => $newStatus,
                'progress' => $data['progress'] ?? $task->progress,
                'notes' => $data['notes'] ?? $task->notes
            ]);

            // Handle specific status transitions
            switch ($newStatus) {
                case Task::STATUS_IN_PROGRESS:
                    $this->handleStartTask($task);
                    break;
                    
                case Task::STATUS_COMPLETED:
                    $this->handleCompleteTask($task);
                    break;
                    
                case Task::STATUS_CANCELLED:
                    $this->handleCancelTask($task);
                    break;
            }

            // Log the transition
            Log::info("Task {$task->id} status changed from {$oldStatus} to {$newStatus}");

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to transition task status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if task can transition to the specified status
     */
    public function canTransitionToStatus(Task $task, string $newStatus): bool
    {
        $allowedTransitions = $this->getAllowedTransitions($task->status);
        return in_array($newStatus, $allowedTransitions);
    }

    /**
     * Get allowed status transitions for current status
     */
    public function getAllowedTransitions(string $currentStatus): array
    {
        $transitions = [
            Task::STATUS_NEW => [
                Task::STATUS_IN_PROGRESS,
                Task::STATUS_PENDING,
                Task::STATUS_CANCELLED
            ],
            Task::STATUS_IN_PROGRESS => [
                Task::STATUS_PENDING,
                Task::STATUS_COMPLETED,
                Task::STATUS_CANCELLED
            ],
            Task::STATUS_PENDING => [
                Task::STATUS_IN_PROGRESS,
                Task::STATUS_CANCELLED
            ],
            Task::STATUS_COMPLETED => [
                // Once completed, can only be cancelled (for corrections)
                Task::STATUS_CANCELLED
            ],
            Task::STATUS_CANCELLED => [
                // Once cancelled, can be reactivated
                Task::STATUS_NEW
            ]
        ];

        return $transitions[$currentStatus] ?? [];
    }

    /**
     * Handle task start
     */
    private function handleStartTask(Task $task): void
    {
        // Check dependencies
        if (!$this->checkDependencies($task)) {
            throw new \Exception("Cannot start task: dependencies not satisfied");
        }

        // Update assignments
        $task->assignments()->update([
            'status' => Assignment::STATUS_IN_PROGRESS,
            'assigned_at' => now()
        ]);

        // Notify dependents that they might be able to start
        $this->notifyDependents($task);
    }

    /**
     * Handle task completion
     */
    private function handleCompleteTask(Task $task): void
    {
        // Mark task as completed
        $task->markAsCompleted();

        // Update assignments
        $task->assignments()->update([
            'status' => Assignment::STATUS_COMPLETED,
            'completed_at' => now(),
            'progress' => 100
        ]);

        // Notify dependents that they might be able to start
        $this->notifyDependents($task);
    }

    /**
     * Handle task cancellation
     */
    private function handleCancelTask(Task $task): void
    {
        // Update assignments
        $task->assignments()->update([
            'status' => Assignment::STATUS_CANCELLED
        ]);

        // Check if any dependent tasks need to be cancelled
        $this->handleDependentTasksCancellation($task);
    }

    /**
     * Check if all dependencies are satisfied
     */
    private function checkDependencies(Task $task): bool
    {
        $dependencies = TaskDependency::getPrerequisitesForTask($task->id);
        
        foreach ($dependencies as $dependency) {
            if (!$dependency->isSatisfied()) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Notify dependent tasks that they might be able to start
     */
    private function notifyDependents(Task $task): void
    {
        $dependents = TaskDependency::getDependentsForTask($task->id);
        
        foreach ($dependents as $dependency) {
            $dependentTask = $dependency->dependentTask;
            
            if ($dependentTask && $dependentTask->status === Task::STATUS_NEW) {
                // Check if all dependencies are now satisfied
                if (TaskDependency::canTaskBeStarted($dependentTask->id)) {
                    Log::info("Task {$dependentTask->id} dependencies satisfied, can be started");
                    // You could trigger an event here to notify users
                }
            }
        }
    }

    /**
     * Handle cancellation of dependent tasks
     */
    private function handleDependentTasksCancellation(Task $task): void
    {
        $dependents = TaskDependency::getDependentsForTask($task->id);
        
        foreach ($dependents as $dependency) {
            $dependentTask = $dependency->dependentTask;
            
            if ($dependentTask && $dependentTask->status !== Task::STATUS_COMPLETED) {
                // Check if this dependency is critical
                if ($dependency->dependency_type === TaskDependency::TYPE_FINISH_TO_START) {
                    // Critical dependency - dependent task cannot proceed
                    Log::warning("Task {$dependentTask->id} has critical dependency on cancelled task {$task->id}");
                    // You could automatically cancel or suspend the dependent task
                }
            }
        }
    }

    /**
     * Get workflow statistics for a task
     */
    public function getTaskWorkflowStats(Task $task): array
    {
        return [
            'current_status' => $task->status,
            'allowed_transitions' => $this->getAllowedTransitions($task->status),
            'dependencies_satisfied' => $this->checkDependencies($task),
            'can_start' => $task->canBeStarted(),
            'is_overdue' => $task->isOverdue(),
            'progress' => $task->progress,
            'assignments_count' => $task->assignments()->count(),
            'active_assignments_count' => $task->assignments()
                ->whereNotIn('status', [Assignment::STATUS_COMPLETED, Assignment::STATUS_CANCELLED])
                ->count(),
            'dependencies_count' => $task->dependencies()->count(),
            'dependents_count' => $task->dependents()->count()
        ];
    }

    /**
     * Bulk status transition for multiple tasks
     */
    public function bulkTransitionStatus(array $taskIds, string $newStatus, array $data = []): array
    {
        $results = [
            'success' => [],
            'failed' => []
        ];

        foreach ($taskIds as $taskId) {
            $task = Task::find($taskId);
            
            if (!$task) {
                $results['failed'][] = ['task_id' => $taskId, 'reason' => 'Task not found'];
                continue;
            }

            if ($this->transitionTaskStatus($task, $newStatus, $data)) {
                $results['success'][] = $taskId;
            } else {
                $results['failed'][] = ['task_id' => $taskId, 'reason' => 'Transition failed'];
            }
        }

        return $results;
    }
} 