<?php

namespace App\Listeners;

use App\Events\TaskAssigned;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTaskAssignmentNotification implements ShouldQueue
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
    public function handle(TaskAssigned $event): void
    {
        try {
            // إرسال الإشعار عبر خدمة الإشعارات
            $this->notificationService->sendTaskAssignmentNotification(
                $event->task,
                $event->assignedUser,
                $event->assignedBy
            );

            // تسجيل الحدث
            Log::info('Task assignment notification sent', [
                'task_id' => $event->task->id,
                'task_title' => $event->task->title,
                'assigned_user_id' => $event->assignedUser->id,
                'assigned_user_name' => $event->assignedUser->name,
                'assigned_by_id' => $event->assignedBy ? $event->assignedBy->id : null,
                'assigned_by_name' => $event->assignedBy ? $event->assignedBy->name : null,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send task assignment notification', [
                'task_id' => $event->task->id,
                'user_id' => $event->assignedUser->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
