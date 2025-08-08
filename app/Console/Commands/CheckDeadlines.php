<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deadlines:check {--days=7 : Number of days to check ahead} {--overdue : Check overdue tasks only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'فحص المواعيد النهائية وإرسال التنبيهات';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $overdueOnly = $this->option('overdue');
        
        $this->info('بدء فحص المواعيد النهائية...');
        
        try {
            if ($overdueOnly) {
                $this->checkOverdueTasks();
            } else {
                $this->checkUpcomingDeadlines($days);
                $this->checkOverdueTasks();
            }
            
            $this->info('تم فحص المواعيد النهائية بنجاح!');
            
        } catch (\Exception $e) {
            $this->error('حدث خطأ أثناء فحص المواعيد النهائية: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }

    /**
     * فحص المواعيد النهائية القريبة
     */
    private function checkUpcomingDeadlines($days)
    {
        $this->info("فحص المواعيد النهائية خلال {$days} أيام...");
        
        $tasks = Task::where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('deadline', '>=', Carbon::now())
            ->where('deadline', '<=', Carbon::now()->addDays($days))
            ->with(['assignments.user'])
            ->get();

        $count = 0;
        foreach ($tasks as $task) {
            $daysLeft = Carbon::now()->diffInDays($task->deadline, false);
            
            foreach ($task->assignments as $assignment) {
                $user = $assignment->user;
                $settings = $user->getNotificationSettings();
                
                if ($settings->isDeadlineReminderNotificationsEnabled()) {
                    // إرسال تنبيه للموعد النهائي القريب
                    $this->sendDeadlineAlert($user, $task, $daysLeft);
                    $count++;
                }
            }
        }
        
        if ($count > 0) {
            $this->warn("تم إرسال {$count} تنبيه للمواعيد النهائية القريبة");
        } else {
            $this->info('لا توجد مواعيد نهائية قريبة تتطلب تنبيهات');
        }
    }

    /**
     * فحص المهام المتأخرة
     */
    private function checkOverdueTasks()
    {
        $this->info('فحص المهام المتأخرة...');
        
        $tasks = Task::where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->where('deadline', '<', Carbon::now())
            ->with(['assignments.user'])
            ->get();

        $count = 0;
        foreach ($tasks as $task) {
            $daysOverdue = Carbon::now()->diffInDays($task->deadline);
            
            foreach ($task->assignments as $assignment) {
                $user = $assignment->user;
                $settings = $user->getNotificationSettings();
                
                if ($settings->isDeadlineReminderNotificationsEnabled()) {
                    // إرسال تنبيه للمهام المتأخرة
                    $this->sendOverdueAlert($user, $task, $daysOverdue);
                    $count++;
                }
            }
        }
        
        if ($count > 0) {
            $this->warn("تم إرسال {$count} تنبيه للمهام المتأخرة");
        } else {
            $this->info('لا توجد مهام متأخرة');
        }
    }

    /**
     * إرسال تنبيه الموعد النهائي القريب
     */
    private function sendDeadlineAlert($user, $task, $daysLeft)
    {
        $urgency = $daysLeft <= 1 ? 'عاجل' : ($daysLeft <= 3 ? 'مهم' : 'عادي');
        $color = $daysLeft <= 1 ? 'danger' : ($daysLeft <= 3 ? 'warning' : 'info');
        
        $message = "تنبيه: المهمة '{$task->title}' تستحق خلال {$daysLeft} يوم";
        
        // إنشاء إشعار في قاعدة البيانات
        $user->notify(new \App\Notifications\TaskDeadlineReminderNotification($task, $daysLeft));
        
        // تسجيل التنبيه
        Log::info('Deadline alert sent', [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'days_left' => $daysLeft,
            'urgency' => $urgency,
            'message' => $message
        ]);
        
        $this->line("✓ {$urgency}: {$message} للمستخدم {$user->name}");
    }

    /**
     * إرسال تنبيه المهام المتأخرة
     */
    private function sendOverdueAlert($user, $task, $daysOverdue)
    {
        $severity = $daysOverdue > 7 ? 'حرج' : ($daysOverdue > 3 ? 'عالي' : 'متوسط');
        
        $message = "تنبيه: المهمة '{$task->title}' متأخرة {$daysOverdue} يوم";
        
        // إنشاء إشعار في قاعدة البيانات
        $user->notify(new \App\Notifications\TaskDeadlineReminderNotification($task, -$daysOverdue));
        
        // تسجيل التنبيه
        Log::warning('Overdue alert sent', [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'days_overdue' => $daysOverdue,
            'severity' => $severity,
            'message' => $message
        ]);
        
        $this->line("⚠ {$severity}: {$message} للمستخدم {$user->name}");
    }
} 