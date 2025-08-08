<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\Task;
use App\Models\User;
use App\Notifications\DailyTaskSummaryNotification;
use Carbon\Carbon;

class SendDailyReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-daily-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إرسال التذكيرات اليومية للمهام';

    protected $notificationService;

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        
        $this->info('بدء إرسال التذكيرات اليومية...');
        
        try {
            // Send deadline reminders
            $this->notificationService->sendDailyReminders();
            
            // Send daily task summaries to users
            $this->sendDailyTaskSummaries();
            
            $this->info('تم إرسال التذكيرات اليومية بنجاح!');
        } catch (\Exception $e) {
            $this->error('حدث خطأ أثناء إرسال التذكيرات: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }

    /**
     * Send daily task summaries to users
     */
    private function sendDailyTaskSummaries()
    {
        $users = User::whereHas('assignments')->get();
        
        $count = 0;
        foreach ($users as $user) {
            $settings = $user->getNotificationSettings();
            
            if ($settings->isDeadlineReminderNotificationsEnabled()) {
                // Get user's tasks for today
                $todayTasks = $user->assignments()
                    ->with(['task'])
                    ->whereHas('task', function ($query) {
                        $query->where('status', '!=', 'completed')
                              ->where('status', '!=', 'cancelled');
                    })
                    ->get();

                // Get overdue tasks
                $overdueTasks = $user->assignments()
                    ->with(['task'])
                    ->whereHas('task', function ($query) {
                        $query->where('status', '!=', 'completed')
                              ->where('status', '!=', 'cancelled')
                              ->where('deadline', '<', Carbon::today());
                    })
                    ->get();

                // Get tasks due today
                $dueTodayTasks = $user->assignments()
                    ->with(['task'])
                    ->whereHas('task', function ($query) {
                        $query->where('status', '!=', 'completed')
                              ->where('status', '!=', 'cancelled')
                              ->whereDate('deadline', Carbon::today());
                    })
                    ->get();

                // Get tasks due tomorrow
                $dueTomorrowTasks = $user->assignments()
                    ->with(['task'])
                    ->whereHas('task', function ($query) {
                        $query->where('status', '!=', 'completed')
                              ->where('status', '!=', 'cancelled')
                              ->whereDate('deadline', Carbon::tomorrow());
                    })
                    ->get();

                // Only send if user has active tasks
                if ($todayTasks->count() > 0 || $overdueTasks->count() > 0 || $dueTodayTasks->count() > 0 || $dueTomorrowTasks->count() > 0) {
                    $user->notify(new DailyTaskSummaryNotification(
                        $todayTasks,
                        $overdueTasks,
                        $dueTodayTasks,
                        $dueTomorrowTasks
                    ));
                    $count++;
                }
            }
        }
        
        $this->info("تم إرسال {$count} ملخص يومي للمهام");
    }
}
