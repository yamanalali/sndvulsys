<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskHistory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskHistoryService
{
    /**
     * Record task creation
     */
    public function recordTaskCreated(Task $task): void
    {
        $task->recordHistory(
            TaskHistory::ACTION_CREATED,
            null,
            null,
            null,
            'تم إنشاء المهمة الجديدة'
        );
    }

    /**
     * Record task update
     */
    public function recordTaskUpdated(Task $task, array $changes): void
    {
        foreach ($changes as $field => $values) {
            $oldValue = $values['old'] ?? null;
            $newValue = $values['new'] ?? null;
            
            if ($oldValue !== $newValue) {
                $actionType = $this->getActionTypeForField($field);
                $description = $this->getDescriptionForField($field, $oldValue, $newValue);
                
                $task->recordHistory(
                    $actionType,
                    $field,
                    $oldValue,
                    $newValue,
                    $description
                );
            }
        }
    }

    /**
     * Record status change
     */
    public function recordStatusChange(Task $task, string $oldStatus, string $newStatus): void
    {
        $task->recordHistory(
            TaskHistory::ACTION_STATUS_CHANGED,
            'status',
            $oldStatus,
            $newStatus,
            "تم تغيير حالة المهمة من {$this->getStatusLabel($oldStatus)} إلى {$this->getStatusLabel($newStatus)}"
        );
    }

    /**
     * Record priority change
     */
    public function recordPriorityChange(Task $task, string $oldPriority, string $newPriority): void
    {
        $task->recordHistory(
            TaskHistory::ACTION_PRIORITY_CHANGED,
            'priority',
            $oldPriority,
            $newPriority,
            "تم تغيير أولوية المهمة من {$this->getPriorityLabel($oldPriority)} إلى {$this->getPriorityLabel($newPriority)}"
        );
    }

    /**
     * Record assignment change
     */
    public function recordAssignmentChange(Task $task, ?User $oldAssignee, ?User $newAssignee): void
    {
        $oldAssigneeName = $oldAssignee ? $oldAssignee->name : 'غير محدد';
        $newAssigneeName = $newAssignee ? $newAssignee->name : 'غير محدد';
        
        $actionType = $newAssignee ? TaskHistory::ACTION_ASSIGNED : TaskHistory::ACTION_UNASSIGNED;
        $description = $newAssignee 
            ? "تم تعيين المهمة إلى {$newAssigneeName}"
            : "تم إلغاء تعيين المهمة من {$oldAssigneeName}";
        
        $task->recordHistory(
            $actionType,
            'assigned_to',
            $oldAssignee ? $oldAssignee->id : null,
            $newAssignee ? $newAssignee->id : null,
            $description,
            [
                'old_assignee_name' => $oldAssigneeName,
                'new_assignee_name' => $newAssigneeName
            ]
        );
    }

    /**
     * Record progress update
     */
    public function recordProgressUpdate(Task $task, int $oldProgress, int $newProgress, string $note = null): void
    {
        $description = "تم تحديث التقدم من {$oldProgress}% إلى {$newProgress}%";
        if ($note) {
            $description .= " - ملاحظة: {$note}";
        }
        
        $task->recordHistory(
            TaskHistory::ACTION_PROGRESS_UPDATED,
            'progress',
            $oldProgress,
            $newProgress,
            $description,
            [
                'progress_change' => $newProgress - $oldProgress,
                'note' => $note
            ]
        );
    }

    /**
     * Record deadline change
     */
    public function recordDeadlineChange(Task $task, ?Carbon $oldDeadline, ?Carbon $newDeadline): void
    {
        $oldDate = $oldDeadline ? $oldDeadline->format('Y-m-d') : 'غير محدد';
        $newDate = $newDeadline ? $newDeadline->format('Y-m-d') : 'غير محدد';
        
        $task->recordHistory(
            TaskHistory::ACTION_DEADLINE_CHANGED,
            'deadline',
            $oldDate,
            $newDate,
            "تم تغيير الموعد النهائي من {$oldDate} إلى {$newDate}"
        );
    }

    /**
     * Record task completion
     */
    public function recordTaskCompleted(Task $task): void
    {
        $task->recordHistory(
            TaskHistory::ACTION_COMPLETED,
            'status',
            $task->getOriginal('status'),
            'completed',
            'تم إنجاز المهمة بنجاح',
            [
                'completed_at' => now()->toISOString(),
                'completion_time' => $task->created_at->diffInDays(now())
            ]
        );
    }

    /**
     * Get task timeline data
     */
    public function getTaskTimeline(Task $task): array
    {
        $history = $task->history()->with('user')->get();
        
        $timeline = [];
        foreach ($history as $record) {
            $timeline[] = [
                'id' => $record->id,
                'action_type' => $record->action_type,
                'action_description' => $record->action_description,
                'field_name' => $record->field_name,
                'old_value' => $record->formatted_old_value,
                'new_value' => $record->formatted_new_value,
                'description' => $record->description,
                'user_name' => $record->user ? $record->user->name : 'النظام',
                'user_avatar' => $record->user ? $record->user->avatar : null,
                'time_ago' => $record->time_ago,
                'created_at' => $record->created_at->format('Y-m-d H:i:s'),
                'icon' => $record->action_icon,
                'color' => $record->action_color,
                'metadata' => $record->metadata
            ];
        }
        
        return $timeline;
    }

    /**
     * Get user's task history summary
     */
    public function getUserTaskHistory(User $user, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $history = TaskHistory::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->with(['task', 'task.project'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $summary = [
            'total_actions' => $history->count(),
            'actions_by_type' => $history->groupBy('action_type')->map->count(),
            'recent_activities' => $history->take(10)->map(function ($record) {
                return [
                    'task_title' => $record->task->title,
                    'action_description' => $record->action_description,
                    'time_ago' => $record->time_ago,
                    'project_name' => $record->task->project ? $record->task->project->name : null
                ];
            })
        ];
        
        return $summary;
    }

    /**
     * Get action type for field
     */
    private function getActionTypeForField(string $field): string
    {
        $fieldActionMap = [
            'status' => TaskHistory::ACTION_STATUS_CHANGED,
            'priority' => TaskHistory::ACTION_PRIORITY_CHANGED,
            'assigned_to' => TaskHistory::ACTION_ASSIGNED,
            'progress' => TaskHistory::ACTION_PROGRESS_UPDATED,
            'deadline' => TaskHistory::ACTION_DEADLINE_CHANGED,
            'start_date' => TaskHistory::ACTION_DEADLINE_CHANGED,
        ];
        
        return $fieldActionMap[$field] ?? TaskHistory::ACTION_UPDATED;
    }

    /**
     * Get description for field change
     */
    private function getDescriptionForField(string $field, $oldValue, $newValue): string
    {
        $fieldNames = [
            'title' => 'العنوان',
            'description' => 'الوصف',
            'status' => 'الحالة',
            'priority' => 'الأولوية',
            'deadline' => 'الموعد النهائي',
            'start_date' => 'تاريخ البدء',
            'progress' => 'التقدم',
            'notes' => 'الملاحظات',
        ];
        
        $fieldName = $fieldNames[$field] ?? $field;
        return "تم تحديث {$fieldName}";
    }

    /**
     * Get status label
     */
    private function getStatusLabel(string $status): string
    {
        $labels = [
            'new' => 'جديدة',
            'in_progress' => 'قيد التنفيذ',
            'pending' => 'معلقة',
            'completed' => 'منجزة',
            'cancelled' => 'ملغاة',
            'archived' => 'مؤرشفة',
        ];
        
        return $labels[$status] ?? $status;
    }

    /**
     * Get priority label
     */
    private function getPriorityLabel(string $priority): string
    {
        $labels = [
            'urgent' => 'عاجلة',
            'high' => 'عالية',
            'medium' => 'متوسطة',
            'low' => 'منخفضة',
        ];
        
        return $labels[$priority] ?? $priority;
    }
} 