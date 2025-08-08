<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Task;
use App\Notifications\TaskAssignmentNotification;
use App\Notifications\TaskStatusUpdateNotification;
use App\Notifications\TaskDeadlineReminderNotification;
use App\Notifications\DailyTaskSummaryNotification;

class SendTestReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:test {type=all : Type of notification to test (assignment, status, deadline, summary, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إرسال إشعارات تجريبية لاختبار النظام';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        
        $this->info('بدء إرسال الإشعارات التجريبية...');
        
        try {
            $users = User::take(3)->get();
            $tasks = Task::take(2)->get();
            
            if ($users->isEmpty()) {
                $this->error('لا يوجد مستخدمين في النظام');
                return 1;
            }
            
            if ($tasks->isEmpty()) {
                $this->error('لا توجد مهام في النظام');
                return 1;
            }
            
            $count = 0;
            
            foreach ($users as $user) {
                foreach ($tasks as $task) {
                    switch ($type) {
                        case 'assignment':
                        case 'all':
                            $user->notify(new TaskAssignmentNotification($task, auth()->user()));
                            $count++;
                            break;
                            
                        case 'status':
                        case 'all':
                            $user->notify(new TaskStatusUpdateNotification($task, 'new', 'in_progress', auth()->user()));
                            $count++;
                            break;
                            
                        case 'deadline':
                        case 'all':
                            $user->notify(new TaskDeadlineReminderNotification($task, 2));
                            $count++;
                            break;
                            
                        case 'summary':
                        case 'all':
                            $user->notify(new DailyTaskSummaryNotification(
                                collect([]),
                                collect([]),
                                collect([]),
                                collect([])
                            ));
                            $count++;
                            break;
                    }
                }
            }
            
            $this->info("تم إرسال {$count} إشعار تجريبي بنجاح!");
            $this->info('يمكنك التحقق من الإشعارات في صفحة الإشعارات');
            
        } catch (\Exception $e) {
            $this->error('حدث خطأ أثناء إرسال الإشعارات التجريبية: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
} 