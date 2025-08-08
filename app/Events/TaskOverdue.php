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

class TaskOverdue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $daysOverdue;
    public $overdueDate;

    /**
     * Create a new event instance.
     */
    public function __construct(Task $task, $daysOverdue = 0)
    {
        $this->task = $task;
        $this->daysOverdue = $daysOverdue;
        $this->overdueDate = now();
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
        $severity = $this->daysOverdue <= 1 ? 'low' : ($this->daysOverdue <= 3 ? 'medium' : 'high');
        
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'days_overdue' => $this->daysOverdue,
            'overdue_date' => $this->overdueDate->toISOString(),
            'deadline' => $this->task->deadline->format('Y-m-d'),
            'severity' => $severity,
            'priority' => $this->task->priority,
            'project_name' => $this->task->project ? $this->task->project->name : null,
            'event_type' => 'task_overdue',
            'timestamp' => now()->toISOString(),
        ];
    }
}
