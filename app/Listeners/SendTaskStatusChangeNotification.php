<?php

namespace App\Listeners;

use App\Events\TaskStatusChanged;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTaskStatusChangeNotification implements ShouldQueue
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
    public function handle(TaskStatusChanged $event): void
    {
        try {
            // إرسال الإشعار عبر خدمة الإشعارات
            $this->notificationService->sendTaskStatusUpdateNotification(
                $event->task,
                $event->oldStatus,
                $event->newStatus,
                $event->updatedBy
            );

            // تسجيل الحدث
            Log::info('Task status change notification sent', [
                'task_id' => $event->task->id,
                'task_title' => $event->task->title,
                'old_status' => $event->oldStatus,
                'new_status' => $event->newStatus,
                'old_status_label' => $event->statusLabels[$event->oldStatus] ?? $event->oldStatus,
                'new_status_label' => $event->statusLabels[$event->newStatus] ?? $event->newStatus,
                'updated_by_id' => $event->updatedBy ? $event->updatedBy->id : null,
                'updated_by_name' => $event->updatedBy ? $event->updatedBy->name : null,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send task status change notification', [
                'task_id' => $event->task->id,
                'old_status' => $event->oldStatus,
                'new_status' => $event->newStatus,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
