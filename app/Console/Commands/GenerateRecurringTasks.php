<?php

namespace App\Console\Commands;

use App\Services\RecurringTaskService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateRecurringTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:generate-recurring 
                          {--days=30 : Number of days ahead to generate tasks for}
                          {--force : Force generation even if tasks already exist}
                          {--cleanup : Clean up old completed instances}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate upcoming recurring task instances';

    protected RecurringTaskService $recurringTaskService;

    /**
     * Create a new command instance.
     */
    public function __construct(RecurringTaskService $recurringTaskService)
    {
        parent::__construct();
        $this->recurringTaskService = $recurringTaskService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $force = $this->option('force');
        $cleanup = $this->option('cleanup');

        $this->info("Starting recurring task generation...");
        $this->info("Generating tasks for the next {$days} days");

        try {
            // Clean up old instances if requested
            if ($cleanup) {
                $this->info("Cleaning up old completed task instances...");
                $deletedCount = $this->recurringTaskService->cleanupOldInstances();
                $this->info("Cleaned up {$deletedCount} old task instances");
            }

            // Generate upcoming tasks
            $this->info("Generating upcoming recurring task instances...");
            
            $progressBar = $this->output->createProgressBar();
            $progressBar->start();

            $generatedTasks = $this->recurringTaskService->generateUpcomingTasks($days);
            
            $progressBar->finish();
            $this->newLine();

            $this->info("Generated " . count($generatedTasks) . " recurring task instances");

            // Display statistics
            $this->displayStatistics();

            // Log the operation
            Log::info("Recurring tasks generated via command", [
                'generated_count' => count($generatedTasks),
                'days_ahead' => $days,
                'cleanup_performed' => $cleanup
            ]);

            $this->info("Recurring task generation completed successfully!");
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("Error generating recurring tasks: " . $e->getMessage());
            Log::error("Failed to generate recurring tasks", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }

    /**
     * Display recurring task statistics
     */
    protected function displayStatistics()
    {
        $this->newLine();
        $this->info("=== Recurring Task Statistics ===");

        $stats = $this->recurringTaskService->getRecurringTaskStats();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Active Recurring Tasks', $stats['active_recurring_tasks']],
                ['Total Task Instances', $stats['total_instances']],
                ['Upcoming Instances', $stats['upcoming_instances']],
                ['Overdue Instances', $stats['overdue_instances']],
            ]
        );

        // Show upcoming instances in next 7 days
        $upcomingTasks = $this->recurringTaskService->getUpcomingInstances(7);
        
        if ($upcomingTasks->isNotEmpty()) {
            $this->newLine();
            $this->info("=== Upcoming Tasks (Next 7 Days) ===");
            
            $this->table(
                ['Title', 'Start Date', 'Status', 'Parent Task'],
                $upcomingTasks->map(function ($task) {
                    return [
                        $task->title,
                        $task->start_date->format('Y-m-d'),
                        $task->status_label,
                        $task->parentTask ? $task->parentTask->title : 'N/A'
                    ];
                })->toArray()
            );
        }
    }
}