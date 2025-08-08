<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Models\Assignment;
use App\Events\TaskAssigned;
use App\Events\TaskStatusChanged;
use App\Events\TaskCompleted;
use App\Events\TaskOverdue;
use App\Events\TaskDeadlineApproaching;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TaskEventService
{
    /**
     * إرسال حدث تخصيص مهمة
     */
    public function dispatchTaskAssigned(Task $task, User $assignedUser, User $assignedBy = null, Assignment $assignment = null)
    {
        try {
            event(new TaskAssigned($task, $assignedUser, $assignedBy, $assignment));
            
            Log::info('Task assigned event dispatched', [
                'task_id' => $task->id,
                'assigned_user_id' => $assignedUser->id,
                'assigned_by_id' => $assignedBy ? $assignedBy->id : null,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to dispatch task assigned event', [
                'task_id' => $task->id,
                'user_id' => $assignedUser->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * إرسال حدث تغيير حالة المهمة
     */
    public function dispatchTaskStatusChanged(Task $task, $oldStatus, $newStatus, User $updatedBy = null)
    {
        try {
            event(new TaskStatusChanged($task, $oldStatus, $newStatus, $updatedBy));
            
            Log::info('Task status changed event dispatched', [
                'task_id' => $task->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'updated_by_id' => $updatedBy ? $updatedBy->id : null,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to dispatch task status changed event', [
                'task_id' => $task->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * إرسال حدث إكمال المهمة
     */
    public function dispatchTaskCompleted(Task $task, User $completedBy = null)
    {
        try {
            event(new TaskCompleted($task, $completedBy));
            
            Log::info('Task completed event dispatched', [
                'task_id' => $task->id,
                'completed_by_id' => $completedBy ? $completedBy->id : null,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to dispatch task completed event', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * إرسال حدث تأخير المهمة
     */
    public function dispatchTaskOverdue(Task $task, $daysOverdue = 0)
    {
        try {
            event(new TaskOverdue($task, $daysOverdue));
            
            Log::warning('Task overdue event dispatched', [
                'task_id' => $task->id,
                'days_overdue' => $daysOverdue,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to dispatch task overdue event', [
                'task_id' => $task->id,
                'days_overdue' => $daysOverdue,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * إرسال حدث اقتراب الموعد النهائي
     */
    public function dispatchTaskDeadlineApproaching(Task $task, $daysLeft = 0)
    {
        try {
            event(new TaskDeadlineApproaching($task, $daysLeft));
            
            Log::info('Task deadline approaching event dispatched', [
                'task_id' => $task->id,
                'days_left' => $daysLeft,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to dispatch task deadline approaching event', [
                'task_id' => $task->id,
                'days_left' => $daysLeft,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * فحص وإرسال أحداث المهام المتأخرة
     */
    public function checkAndDispatchOverdueEvents()
    {
        $overdueTasks = Task::where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('deadline', '<', Carbon::now())
            ->with(['assignments.user'])
            ->get();

        $count = 0;
        foreach ($overdueTasks as $task) {
            $daysOverdue = Carbon::now()->diffInDays($task->deadline);
            $this->dispatchTaskOverdue($task, $daysOverdue);
            $count++;
        }

        return $count;
    }

    /**
     * فحص وإرسال أحداث اقتراب المواعيد النهائية
     */
    public function checkAndDispatchDeadlineApproachingEvents()
    {
        $approachingTasks = Task::where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('deadline', '>=', Carbon::now())
            ->where('deadline', '<=', Carbon::now()->addDays(7))
            ->with(['assignments.user'])
            ->get();

        $count = 0;
        foreach ($approachingTasks as $task) {
            $daysLeft = Carbon::now()->diffInDays($task->deadline, false);
            if ($daysLeft >= 0 && $daysLeft <= 7) {
                $this->dispatchTaskDeadlineApproaching($task, $daysLeft);
                $count++;
            }
        }

        return $count;
    }

    /**
     * إرسال جميع الأحداث المتعلقة بتحديث حالة المهمة
     */
    public function handleTaskStatusUpdate(Task $task, $oldStatus, $newStatus, User $updatedBy = null)
    {
        // إرسال حدث تغيير الحالة
        $this->dispatchTaskStatusChanged($task, $oldStatus, $newStatus, $updatedBy);

        // إذا تم إكمال المهمة، أرسل حدث الإكمال
        if ($newStatus === 'completed') {
            $this->dispatchTaskCompleted($task, $updatedBy);
        }

        // إذا كانت المهمة متأخرة، أرسل حدث التأخير
        if ($task->deadline && $task->deadline < Carbon::now()) {
            $daysOverdue = Carbon::now()->diffInDays($task->deadline);
            $this->dispatchTaskOverdue($task, $daysOverdue);
        }
    }
} 