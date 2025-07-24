<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeadlineReminderNotification extends Notification
{
    use Queueable;

    protected $task;
    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($task, $user)
    {
        $this->task = $task;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail']; // يمكن إضافة 'database' لاحقاً
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تذكير بقرب موعد تسليم المهمة')
            ->greeting('مرحباً ' . $notifiable->name . '،')
            ->line('هذا تذكير بأن موعد تسليم المهمة التالية اقترب:')
            ->line('عنوان المهمة: ' . $this->task->title)
            ->line('الوصف: ' . ($this->task->description ?: '-'))
            ->line('تاريخ التسليم: ' . ($this->task->deadline ? $this->task->deadline->format('Y-m-d') : 'غير محدد'))
            ->action('عرض المهمة', url('/tasks/' . $this->task->id))
            ->line('يرجى التأكد من إنجاز المهمة قبل الموعد النهائي.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
        ];
    }
}
