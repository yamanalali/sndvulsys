<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class DailyTaskSummaryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $todayTasks;
    public $overdueTasks;
    public $dueTodayTasks;
    public $dueTomorrowTasks;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        Collection $todayTasks,
        Collection $overdueTasks,
        Collection $dueTodayTasks,
        Collection $dueTomorrowTasks
    ) {
        $this->todayTasks = $todayTasks;
        $this->overdueTasks = $overdueTasks;
        $this->dueTodayTasks = $dueTodayTasks;
        $this->dueTomorrowTasks = $dueTomorrowTasks;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // التحقق من إعدادات المستخدم
        $settings = $notifiable->getNotificationSettings();
        
        if ($settings->deadline_reminder_email && $settings->email_notifications) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('ملخص المهام اليومي - ' . now()->format('Y-m-d'))
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('إليك ملخص مهامك لهذا اليوم:');

        if ($this->overdueTasks->count() > 0) {
            $message->line('🔴 المهام المتأخرة: ' . $this->overdueTasks->count());
            foreach ($this->overdueTasks->take(3) as $assignment) {
                $message->line('- ' . $assignment->task->title);
            }
        }

        if ($this->dueTodayTasks->count() > 0) {
            $message->line('🟡 المهام المستحقة اليوم: ' . $this->dueTodayTasks->count());
            foreach ($this->dueTodayTasks->take(3) as $assignment) {
                $message->line('- ' . $assignment->task->title);
            }
        }

        if ($this->dueTomorrowTasks->count() > 0) {
            $message->line('🟢 المهام المستحقة غداً: ' . $this->dueTomorrowTasks->count());
            foreach ($this->dueTomorrowTasks->take(3) as $assignment) {
                $message->line('- ' . $assignment->task->title);
            }
        }

        $message->action('عرض جميع المهام', route('tasks.index'))
                ->line('شكراً لك على استخدام نظام إدارة المهام!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'daily_summary',
            'message' => 'ملخص المهام اليومي - ' . now()->format('Y-m-d'),
            'overdue_count' => $this->overdueTasks->count(),
            'due_today_count' => $this->dueTodayTasks->count(),
            'due_tomorrow_count' => $this->dueTomorrowTasks->count(),
            'total_active_count' => $this->todayTasks->count(),
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'daily_summary',
            'message' => 'ملخص المهام اليومي - ' . now()->format('Y-m-d'),
            'overdue_count' => $this->overdueTasks->count(),
            'due_today_count' => $this->dueTodayTasks->count(),
            'due_tomorrow_count' => $this->dueTomorrowTasks->count(),
            'total_active_count' => $this->todayTasks->count(),
            'icon' => 'calendar',
            'color' => 'info',
        ];
    }
} 