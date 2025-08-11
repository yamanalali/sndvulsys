<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskDeadlineReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $task;
    public $daysLeft;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, $daysLeft = 0)
    {
        $this->task = $task;
        $this->daysLeft = $daysLeft;
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
        $daysText = $this->daysLeft == 0 ? 'اليوم' : "بعد {$this->daysLeft} يوم";
        
        return (new MailMessage)
            ->subject('تذكير بالموعد النهائي للمهمة')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line("تذكير: الموعد النهائي للمهمة يقترب")
            ->line("المهمة: {$this->task->title}")
            ->line("الموعد النهائي: " . $this->task->deadline->format('Y-m-d'))
            ->line("الوقت المتبقي: {$daysText}")
            ->line("الأولوية: " . ($this->task->priority ?? 'غير محدد'))
            ->action('عرض المهمة', route('tasks.show', $this->task->id))
            ->line('يرجى إكمال المهمة قبل الموعد النهائي!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $daysText = $this->daysLeft == 0 ? 'اليوم' : "بعد {$this->daysLeft} يوم";
        
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'deadline' => $this->task->deadline->format('Y-m-d'),
            'days_left' => $this->daysLeft,
            'days_text' => $daysText,
            'priority' => $this->task->priority,
            'type' => 'task_deadline_reminder',
            'message' => "تذكير: الموعد النهائي للمهمة '{$this->task->title}' {$daysText}",
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $daysText = $this->daysLeft == 0 ? 'اليوم' : "بعد {$this->daysLeft} يوم";
        
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'deadline' => $this->task->deadline->format('Y-m-d'),
            'days_left' => $this->daysLeft,
            'days_text' => $daysText,
            'priority' => $this->task->priority,
            'type' => 'task_deadline_reminder',
            'message' => "تذكير: الموعد النهائي للمهمة '{$this->task->title}' {$daysText}",
            'icon' => 'fas fa-clock',
            'color' => 'warning',
        ];
    }
}
