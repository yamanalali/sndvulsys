<?php

namespace App\Listeners;

use App\Events\TaskOverdue;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTaskOverdueNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(TaskOverdue $event): void
    {
        try {
            // إرسال إشعار التأخير لجميع المكلفين بالمهمة
            foreach ($event->task->assignments as $assignment) {
                $user = $assignment->user;
                $settings = $user->getNotificationSettings();
                
                if ($settings->isDeadlineReminderNotificationsEnabled()) {
                    $user->notify(new \App\Notifications\TaskDeadlineReminderNotification($event->task, -$event->daysOverdue));
                }
            }

            // تسجيل الحدث
            $severity = $event->daysOverdue <= 1 ? 'low' : ($event->daysOverdue <= 3 ? 'medium' : 'high');
            
            Log::warning('Task overdue notification sent', [
                'task_id' => $event->task->id,
                'task_title' => $event->task->title,
                'days_overdue' => $event->daysOverdue,
                'overdue_date' => $event->overdueDate->toISOString(),
                'deadline' => $event->task->deadline->format('Y-m-d'),
                'severity' => $severity,
                'priority' => $event->task->priority,
                'assigned_users_count' => $event->task->assignments->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send task overdue notification', [
                'task_id' => $event->task->id,
                'days_overdue' => $event->daysOverdue,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
