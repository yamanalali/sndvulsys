<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;
use App\Models\User;

class TaskDependencyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $task;
    public $dependencyTask;
    public $dependencyType;
    public $addedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, Task $dependencyTask, $dependencyType, User $addedBy = null)
    {
        $this->task = $task;
        $this->dependencyTask = $dependencyTask;
        $this->dependencyType = $dependencyType;
        $this->addedBy = $addedBy;
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
        
        if ($settings->dependency_email && $settings->email_notifications) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $dependencyTypes = [
            'finish_to_start' => 'إنهاء-لبدء',
            'start_to_start' => 'بدء-لبدء',
            'finish_to_finish' => 'إنهاء-لإنهاء',
            'start_to_finish' => 'بدء-لإنهاء',
        ];

        $dependencyTypeLabel = $dependencyTypes[$this->dependencyType] ?? $this->dependencyType;
        $addedByText = $this->addedBy ? "بواسطة {$this->addedBy->name}" : "";
        
        return (new MailMessage)
            ->subject('تم إضافة تبعية جديدة للمهمة')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line("تم إضافة تبعية جديدة للمهمة: {$this->task->title}")
            ->line("المهمة المعتمَد عليها: {$this->dependencyTask->title}")
            ->line("نوع التبعية: {$dependencyTypeLabel}")
            ->line($addedByText)
            ->action('عرض المهمة', route('tasks.show', $this->task->id))
            ->line('يرجى مراجعة التبعيات الجديدة!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $dependencyTypes = [
            'finish_to_start' => 'إنهاء-لبدء',
            'start_to_start' => 'بدء-لبدء',
            'finish_to_finish' => 'إنهاء-لإنهاء',
            'start_to_finish' => 'بدء-لإنهاء',
        ];

        $dependencyTypeLabel = $dependencyTypes[$this->dependencyType] ?? $this->dependencyType;
        
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'dependency_task_id' => $this->dependencyTask->id,
            'dependency_task_title' => $this->dependencyTask->title,
            'dependency_type' => $this->dependencyType,
            'dependency_type_label' => $dependencyTypeLabel,
            'added_by' => $this->addedBy ? $this->addedBy->name : null,
            'added_by_id' => $this->addedBy ? $this->addedBy->id : null,
            'type' => 'task_dependency',
            'message' => "تم إضافة تبعية جديدة للمهمة '{$this->task->title}': {$this->dependencyTask->title}",
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $dependencyTypes = [
            'finish_to_start' => 'إنهاء-لبدء',
            'start_to_start' => 'بدء-لبدء',
            'finish_to_finish' => 'إنهاء-لإنهاء',
            'start_to_finish' => 'بدء-لإنهاء',
        ];

        $dependencyTypeLabel = $dependencyTypes[$this->dependencyType] ?? $this->dependencyType;
        
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'dependency_task_id' => $this->dependencyTask->id,
            'dependency_task_title' => $this->dependencyTask->title,
            'dependency_type' => $this->dependencyType,
            'dependency_type_label' => $dependencyTypeLabel,
            'added_by' => $this->addedBy ? $this->addedBy->name : null,
            'added_by_id' => $this->addedBy ? $this->addedBy->id : null,
            'type' => 'task_dependency',
            'message' => "تم إضافة تبعية جديدة للمهمة '{$this->task->title}': {$this->dependencyTask->title}",
            'icon' => 'fas fa-link',
            'color' => 'success',
        ];
    }
}
