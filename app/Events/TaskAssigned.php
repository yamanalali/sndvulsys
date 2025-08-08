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

class TaskAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $assignedUser;
    public $assignedBy;
    public $assignment;

    /**
     * Create a new event instance.
     */
    public function __construct(Task $task, User $assignedUser, User $assignedBy = null, $assignment = null)
    {
        $this->task = $task;
        $this->assignedUser = $assignedUser;
        $this->assignedBy = $assignedBy;
        $this->assignment = $assignment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->assignedUser->id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'assigned_user_id' => $this->assignedUser->id,
            'assigned_user_name' => $this->assignedUser->name,
            'assigned_by_id' => $this->assignedBy ? $this->assignedBy->id : null,
            'assigned_by_name' => $this->assignedBy ? $this->assignedBy->name : null,
            'deadline' => $this->task->deadline->format('Y-m-d'),
            'priority' => $this->task->priority,
            'project_name' => $this->task->project ? $this->task->project->name : null,
            'event_type' => 'task_assigned',
            'timestamp' => now()->toISOString(),
        ];
    }
}
