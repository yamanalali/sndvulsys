<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TaskEventService;

class ManageTaskEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:manage-events {action=check : Action to perform (check, overdue, approaching, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إدارة أحداث المهام (التأخير، اقتراب المواعيد النهائية)';

    protected $taskEventService;

    /**
     * Execute the console command.
     */
    public function handle(TaskEventService $taskEventService)
    {
        $this->taskEventService = $taskEventService;
        $action = $this->argument('action');
        
        $this->info('بدء إدارة أحداث المهام...');
        
        try {
            switch ($action) {
                case 'overdue':
                    $this->checkOverdueTasks();
                    break;
                    
                case 'approaching':
                    $this->checkApproachingDeadlines();
                    break;
                    
                case 'all':
                    $this->checkOverdueTasks();
                    $this->checkApproachingDeadlines();
                    break;
                    
                case 'check':
                default:
                    $this->checkOverdueTasks();
                    $this->checkApproachingDeadlines();
                    break;
            }
            
            $this->info('تم إدارة أحداث المهام بنجاح!');
            
        } catch (\Exception $e) {
            $this->error('حدث خطأ أثناء إدارة أحداث المهام: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }

    /**
     * فحص المهام المتأخرة
     */
    private function checkOverdueTasks()
    {
        $this->info('فحص المهام المتأخرة...');
        
        $count = $this->taskEventService->checkAndDispatchOverdueEvents();
        
        if ($count > 0) {
            $this->warn("تم العثور على {$count} مهمة متأخرة وتم إرسال الإشعارات");
        } else {
            $this->info('لا توجد مهام متأخرة');
        }
    }

    /**
     * فحص المهام التي يقترب موعدها النهائي
     */
    private function checkApproachingDeadlines()
    {
        $this->info('فحص المهام التي يقترب موعدها النهائي...');
        
        $count = $this->taskEventService->checkAndDispatchDeadlineApproachingEvents();
        
        if ($count > 0) {
            $this->warn("تم العثور على {$count} مهمة يقترب موعدها النهائي وتم إرسال الإشعارات");
        } else {
            $this->info('لا توجد مهام يقترب موعدها النهائي');
        }
    }
} 