<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Notifications\DeadlineReminderNotification;

class SendDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-deadline-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // جلب المهام التي موعدها النهائي خلال 3 أيام ولم تكتمل
        $tasks = Task::whereNotNull('deadline')
            ->where('status', '!=', Task::STATUS_COMPLETED)
            ->whereDate('deadline', '>=', now()->toDateString())
            ->whereDate('deadline', '<=', now()->addDays(3)->toDateString())
            ->with('assignments.user')
            ->get();

        $remindersSent = 0;
        foreach ($tasks as $task) {
            foreach ($task->assignments as $assignment) {
                $user = $assignment->user;
                if ($user) {
                    $user->notify(new DeadlineReminderNotification($task, $user));
                    $remindersSent++;
                }
            }
        }
        $this->info("تم إرسال {$remindersSent} تذكير للمهام التي اقترب موعدها النهائي.");
    }
}
