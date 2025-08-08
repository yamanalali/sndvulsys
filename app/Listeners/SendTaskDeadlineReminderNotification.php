<?php

namespace App\Listeners;

use App\Events\TaskDeadlineApproaching;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTaskDeadlineReminderNotification implements ShouldQueue
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
    public function handle(TaskDeadlineApproaching $event): void
    {
        // Send deadline reminder notification to all assigned users
        foreach ($event->task->assignments as $assignment) {
            $user = $assignment->user;
            $settings = $user->getNotificationSettings();
            
            if ($settings->isDeadlineReminderNotificationsEnabled()) {
                $user->notify(new \App\Notifications\TaskDeadlineReminderNotification($event->task, $event->daysLeft));
            }
        }
    }
}
