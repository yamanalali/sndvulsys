<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTaskCompletionNotification implements ShouldQueue
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
    public function handle(TaskCompleted $event): void
    {
        try {
            // إرسال إشعار الإكمال لجميع المكلفين بالمهمة
            foreach ($event->task->assignments as $assignment) {
                $user = $assignment->user;
                $settings = $user->getNotificationSettings();
                
                if ($settings->isStatusUpdateNotificationsEnabled()) {
                    $user->notify(new \App\Notifications\TaskCompletionNotification($event->task, $event->completedBy));
                }
            }

            // تسجيل الحدث
            Log::info('Task completion notification sent', [
                'task_id' => $event->task->id,
                'task_title' => $event->task->title,
                'completed_by_id' => $event->completedBy ? $event->completedBy->id : null,
                'completed_by_name' => $event->completedBy ? $event->completedBy->name : null,
                'completion_time' => $event->completionTime->toISOString(),
                'days_to_deadline' => $event->task->deadline ? now()->diffInDays($event->task->deadline, false) : 0,
                'is_on_time' => $event->task->deadline ? now()->diffInDays($event->task->deadline, false) >= 0 : true,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send task completion notification', [
                'task_id' => $event->task->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
