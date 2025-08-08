<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;
use App\Models\User;

class TaskAssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $task;
    public $assignedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, User $assignedBy = null)
    {
        $this->task = $task;
        $this->assignedBy = $assignedBy;
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
        
        if ($settings->assignment_email && $settings->email_notifications) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $assignedByText = $this->assignedBy ? "تم تخصيصها لك بواسطة {$this->assignedBy->name}" : "تم تخصيصها لك";
        
        return (new MailMessage)
            ->subject('مهمة جديدة مخصصة لك')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line("تم تخصيص مهمة جديدة لك: {$this->task->title}")
            ->line($assignedByText)
            ->line("الموعد النهائي: " . $this->task->deadline->format('Y-m-d'))
            ->line("الأولوية: " . ($this->task->priority ?? 'غير محدد'))
            ->action('عرض المهمة', route('tasks.show', $this->task->id))
            ->line('شكراً لك على استخدام نظام إدارة المهام!');
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
            'assigned_by' => $this->assignedBy ? $this->assignedBy->name : null,
            'assigned_by_id' => $this->assignedBy ? $this->assignedBy->id : null,
            'deadline' => $this->task->deadline->format('Y-m-d'),
            'priority' => $this->task->priority,
            'type' => 'task_assignment',
            'message' => "تم تخصيص مهمة جديدة لك: {$this->task->title}",
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'assigned_by' => $this->assignedBy ? $this->assignedBy->name : null,
            'assigned_by_id' => $this->assignedBy ? $this->assignedBy->id : null,
            'deadline' => $this->task->deadline->format('Y-m-d'),
            'priority' => $this->task->priority,
            'type' => 'task_assignment',
            'message' => "تم تخصيص مهمة جديدة لك: {$this->task->title}",
            'icon' => 'fas fa-user-plus',
            'color' => 'primary',
        ];
    }
}
