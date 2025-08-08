<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;
use App\Models\User;

class TaskStatusUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $task;
    public $oldStatus;
    public $newStatus;
    public $updatedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, $oldStatus, $newStatus, User $updatedBy = null)
    {
        $this->task = $task;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->updatedBy = $updatedBy;
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
        
        if ($settings->status_update_email && $settings->email_notifications) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            'new' => 'جديدة',
            'in_progress' => 'قيد التنفيذ',
            'pending' => 'معلقة',
            'completed' => 'منجزة',
            'cancelled' => 'ملغاة',
        ];

        $oldStatusLabel = $statusLabels[$this->oldStatus] ?? $this->oldStatus;
        $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;
        
        $updatedByText = $this->updatedBy ? "بواسطة {$this->updatedBy->name}" : "";
        
        return (new MailMessage)
            ->subject('تم تحديث حالة المهمة')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line("تم تحديث حالة المهمة: {$this->task->title}")
            ->line("الحالة السابقة: {$oldStatusLabel}")
            ->line("الحالة الجديدة: {$newStatusLabel}")
            ->line($updatedByText)
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
        $statusLabels = [
            'new' => 'جديدة',
            'in_progress' => 'قيد التنفيذ',
            'pending' => 'معلقة',
            'completed' => 'منجزة',
            'cancelled' => 'ملغاة',
        ];

        $oldStatusLabel = $statusLabels[$this->oldStatus] ?? $this->oldStatus;
        $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;

        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'old_status' => $this->oldStatus,
            'old_status_label' => $oldStatusLabel,
            'new_status' => $this->newStatus,
            'new_status_label' => $newStatusLabel,
            'updated_by' => $this->updatedBy ? $this->updatedBy->name : null,
            'updated_by_id' => $this->updatedBy ? $this->updatedBy->id : null,
            'type' => 'task_status_update',
            'message' => "تم تحديث حالة المهمة: {$this->task->title} من {$oldStatusLabel} إلى {$newStatusLabel}",
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $statusLabels = [
            'new' => 'جديدة',
            'in_progress' => 'قيد التنفيذ',
            'pending' => 'معلقة',
            'completed' => 'منجزة',
            'cancelled' => 'ملغاة',
        ];

        $oldStatusLabel = $statusLabels[$this->oldStatus] ?? $this->oldStatus;
        $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;

        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'old_status' => $this->oldStatus,
            'old_status_label' => $oldStatusLabel,
            'new_status' => $this->newStatus,
            'new_status_label' => $newStatusLabel,
            'updated_by' => $this->updatedBy ? $this->updatedBy->name : null,
            'updated_by_id' => $this->updatedBy ? $this->updatedBy->id : null,
            'type' => 'task_status_update',
            'message' => "تم تحديث حالة المهمة: {$this->task->title} من {$oldStatusLabel} إلى {$newStatusLabel}",
            'icon' => 'fas fa-sync-alt',
            'color' => 'info',
        ];
    }
}
