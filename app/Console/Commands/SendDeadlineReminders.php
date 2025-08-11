<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\Task;
use App\Events\TaskDeadlineApproaching;
use App\Events\TaskOverdue;
use Carbon\Carbon;

class SendDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-deadline-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إرسال تذكيرات المواعيد النهائية للمهام';

    protected $notificationService;

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        
        $this->info('بدء إرسال تذكيرات المواعيد النهائية...');
        
        try {
            // Send deadline reminders for approaching deadlines
            $this->sendApproachingDeadlineReminders();
            
            // Send overdue notifications
            $this->sendOverdueNotifications();
            
            $this->info('تم إرسال تذكيرات المواعيد النهائية بنجاح!');
        } catch (\Exception $e) {
            $this->error('حدث خطأ أثناء إرسال التذكيرات: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }

    /**
     * Send reminders for approaching deadlines
     */
    private function sendApproachingDeadlineReminders()
    {
        $tasks = Task::where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('deadline', '>=', Carbon::now())
            ->where('deadline', '<=', Carbon::now()->addDays(7))
            ->with(['assignments.user'])
            ->get();

        $count = 0;
        foreach ($tasks as $task) {
            foreach ($task->assignments as $assignment) {
                $user = $assignment->user;
                $settings = $user->getNotificationSettings();
                
                if ($settings->isDeadlineReminderNotificationsEnabled()) {
                    $daysLeft = Carbon::now()->diffInDays($task->deadline, false);
                    
                    // إرسال الإشعار فقط إذا كان عدد الأيام المتبقية يساوي أو أقل من الإعداد
                    if ($daysLeft <= $settings->deadline_reminder_days) {
                        // Dispatch event for deadline approaching
                        event(new TaskDeadlineApproaching($task, $daysLeft));
                        $count++;
                    }
                }
            }
        }
        
        $this->info("تم إرسال {$count} تذكير للمواعيد النهائية القريبة");
    }

    /**
     * Send notifications for overdue tasks
     */
    private function sendOverdueNotifications()
    {
        $tasks = Task::where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('deadline', '<', Carbon::now())
            ->with(['assignments.user'])
            ->get();

        $count = 0;
        foreach ($tasks as $task) {
            foreach ($task->assignments as $assignment) {
                $user = $assignment->user;
                $settings = $user->getNotificationSettings();
                
                if ($settings->isDeadlineReminderNotificationsEnabled()) {
                    $daysOverdue = Carbon::now()->diffInDays($task->deadline);
                    
                    // Send overdue notification
                    event(new TaskOverdue($task, $daysOverdue));
                    $count++;
                }
            }
        }
        
        $this->info("تم إرسال {$count} إشعار للمهام المتأخرة");
    }
}
