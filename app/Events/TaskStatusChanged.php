<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Task;
use App\Models\User;

class TaskStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $oldStatus;
    public $newStatus;
    public $updatedBy;
    public $statusLabels;

    /**
     * Create a new event instance.
     */
    public function __construct(Task $task, $oldStatus, $newStatus, User $updatedBy = null)
    {
        $this->task = $task;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->updatedBy = $updatedBy;
        
        $this->statusLabels = [
            'new' => 'جديدة',
            'in_progress' => 'قيد التنفيذ',
            'pending' => 'معلقة',
            'completed' => 'منجزة',
            'cancelled' => 'ملغاة',
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];
        foreach ($this->task->assignments as $assignment) {
            $channels[] = new PrivateChannel('user.' . $assignment->user_id);
        }
        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'old_status_label' => $this->statusLabels[$this->oldStatus] ?? $this->oldStatus,
            'new_status_label' => $this->statusLabels[$this->newStatus] ?? $this->newStatus,
            'updated_by_id' => $this->updatedBy ? $this->updatedBy->id : null,
            'updated_by_name' => $this->updatedBy ? $this->updatedBy->name : null,
            'deadline' => $this->task->deadline->format('Y-m-d'),
            'priority' => $this->task->priority,
            'project_name' => $this->task->project ? $this->task->project->name : null,
            'event_type' => 'task_status_changed',
            'timestamp' => now()->toISOString(),
        ];
    }
}
