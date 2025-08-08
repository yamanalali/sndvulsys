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

class TaskCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $completedBy;
    public $completionTime;

    /**
     * Create a new event instance.
     */
    public function __construct(Task $task, User $completedBy = null)
    {
        $this->task = $task;
        $this->completedBy = $completedBy;
        $this->completionTime = now();
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
        $daysToDeadline = $this->task->deadline ? now()->diffInDays($this->task->deadline, false) : 0;
        $isOnTime = $daysToDeadline >= 0;
        
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'completed_by_id' => $this->completedBy ? $this->completedBy->id : null,
            'completed_by_name' => $this->completedBy ? $this->completedBy->name : null,
            'completion_time' => $this->completionTime->toISOString(),
            'deadline' => $this->task->deadline->format('Y-m-d'),
            'days_to_deadline' => $daysToDeadline,
            'is_on_time' => $isOnTime,
            'priority' => $this->task->priority,
            'project_name' => $this->task->project ? $this->task->project->name : null,
            'event_type' => 'task_completed',
            'timestamp' => now()->toISOString(),
        ];
    }
}
