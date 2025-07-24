<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentNotification extends Notification
{
    use Queueable;

    protected $task;
    protected $assigner;

    /**
     * Create a new notification instance.
     */
    public function __construct($task, $assigner)
    {
        $this->task = $task;
        $this->assigner = $assigner;
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
            ->subject('تم تخصيص مهمة جديدة لك')
            ->greeting('مرحباً ' . $notifiable->name . '،')
            ->line('لقد تم تخصيص مهمة جديدة لك في النظام:')
            ->line('عنوان المهمة: ' . $this->task->title)
            ->line('الوصف: ' . ($this->task->description ?: '-'))
            ->line('تاريخ التسليم: ' . ($this->task->deadline ? $this->task->deadline->format('Y-m-d') : 'غير محدد'))
            ->line('تم التخصيص بواسطة: ' . $this->assigner->name)
            ->action('عرض المهمة', url('/tasks/' . $this->task->id))
            ->line('نتمنى لك التوفيق!');
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
            'assigned_by' => $this->assigner->name,
        ];
    }
}
