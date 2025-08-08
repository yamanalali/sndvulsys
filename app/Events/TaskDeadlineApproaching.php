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

class TaskDeadlineApproaching
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $daysLeft;

    /**
     * Create a new event instance.
     */
    public function __construct(Task $task, $daysLeft = 0)
    {
        $this->task = $task;
        $this->daysLeft = $daysLeft;
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
}
