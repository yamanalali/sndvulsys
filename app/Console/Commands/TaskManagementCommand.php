<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Category;
use App\Models\User;
use App\Models\Assignment;
use App\Models\TaskDependency;
use App\Services\TaskWorkflowService;
use App\Services\TaskPriorityService;
use Illuminate\Support\Facades\DB;

class TaskManagementCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:manage 
                            {action : Action to perform (list, stats, overdue, urgent, dependencies, priorities, workflow)}
                            {--status= : Filter by status}
                            {--priority= : Filter by priority}
                            {--category= : Filter by category}
                            {--user= : Filter by user}
                            {--limit=10 : Limit number of results}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage and analyze tasks in the system';

    protected TaskWorkflowService $workflowService;
    protected TaskPriorityService $priorityService;

    public function __construct(TaskWorkflowService $workflowService, TaskPriorityService $priorityService)
    {
        parent::__construct();
        $this->workflowService = $workflowService;
        $this->priorityService = $priorityService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list':
                return $this->listTasks();
            case 'stats':
                return $this->showStats();
            case 'overdue':
                return $this->showOverdueTasks();
            case 'urgent':
                return $this->showUrgentTasks();
            case 'dependencies':
                return $this->showDependencies();
            case 'priorities':
                return $this->adjustPriorities();
            case 'workflow':
                return $this->showWorkflowInfo();
            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }
    }

    /**
     * List tasks with filters
     */
    private function listTasks(): int
    {
        $query = Task::with(['category', 'creator', 'assignee', 'assignments']);

        // Apply filters
        if ($status = $this->option('status')) {
            $query->where('status', $status);
        }

        if ($priority = $this->option('priority')) {
            $query->where('priority', $priority);
        }

        if ($category = $this->option('category')) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', 'like', "%{$category}%");
            });
        }

        if ($user = $this->option('user')) {
            $query->whereHas('assignments.user', function ($q) use ($user) {
                $q->where('name', 'like', "%{$user}%");
            });
        }

        $tasks = $query->limit($this->option('limit'))->get();

        if ($tasks->isEmpty()) {
            $this->info('No tasks found matching the criteria.');
            return 0;
        }

        $this->info("Found {$tasks->count()} tasks:");
        $this->newLine();

        $headers = ['ID', 'Title', 'Status', 'Priority', 'Category', 'Assignee', 'Progress', 'Deadline'];
        $rows = [];

        foreach ($tasks as $task) {
            $rows[] = [
                $task->id,
                $task->title,
                $task->status,
                $task->priority,
                $task->category->name ?? 'N/A',
                $task->getMainAssignee()?->name ?? 'Unassigned',
                $task->progress . '%',
                $task->deadline ? $task->deadline->format('Y-m-d') : 'No deadline'
            ];
        }

        $this->table($headers, $rows);
        return 0;
    }

    /**
     * Show task statistics
     */
    private function showStats(): int
    {
        $this->info('📊 Task Management Statistics');
        $this->newLine();

        // Basic stats
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', Task::STATUS_COMPLETED)->count();
        $overdueTasks = Task::overdue()->count();
        $urgentTasks = Task::byPriority(Task::PRIORITY_URGENT)->count();

        $this->info("Total Tasks: {$totalTasks}");
        $this->info("Completed Tasks: {$completedTasks}");
        $this->info("Overdue Tasks: {$overdueTasks}");
        $this->info("Urgent Tasks: {$urgentTasks}");
        $this->newLine();

        // Status distribution
        $this->info('Status Distribution:');
        $statuses = Task::getStatuses();
        foreach ($statuses as $status => $label) {
            $count = Task::where('status', $status)->count();
            $percentage = $totalTasks > 0 ? round(($count / $totalTasks) * 100, 1) : 0;
            $this->line("  {$label}: {$count} ({$percentage}%)");
        }
        $this->newLine();

        // Priority distribution
        $this->info('Priority Distribution:');
        $priorities = Task::getPriorities();
        foreach ($priorities as $priority => $label) {
            $count = Task::where('priority', $priority)->count();
            $percentage = $totalTasks > 0 ? round(($count / $totalTasks) * 100, 1) : 0;
            $this->line("  {$label}: {$count} ({$percentage}%)");
        }
        $this->newLine();

        // Category distribution
        $this->info('Category Distribution:');
        $categories = Category::withCount('tasks')->get();
        foreach ($categories as $category) {
            $percentage = $totalTasks > 0 ? round(($category->tasks_count / $totalTasks) * 100, 1) : 0;
            $this->line("  {$category->name}: {$category->tasks_count} ({$percentage}%)");
        }

        return 0;
    }

    /**
     * Show overdue tasks
     */
    private function showOverdueTasks(): int
    {
        $overdueTasks = Task::overdue()
            ->with(['category', 'assignee', 'assignments'])
            ->limit($this->option('limit'))
            ->get();

        if ($overdueTasks->isEmpty()) {
            $this->info('✅ No overdue tasks found.');
            return 0;
        }

        $this->warn("⚠️  Found {$overdueTasks->count()} overdue tasks:");
        $this->newLine();

        $headers = ['ID', 'Title', 'Days Overdue', 'Priority', 'Assignee', 'Progress'];
        $rows = [];

        foreach ($overdueTasks as $task) {
            $daysOverdue = $task->deadline ? $task->deadline->diffInDays(now()) : 0;
            $rows[] = [
                $task->id,
                $task->title,
                $daysOverdue,
                $task->priority,
                $task->assignee->name ?? 'Unassigned',
                $task->progress . '%'
            ];
        }

        $this->table($headers, $rows);
        return 0;
    }

    /**
     * Show urgent tasks
     */
    private function showUrgentTasks(): int
    {
        $urgentTasks = $this->priorityService->getUrgentTasks()
            ->take($this->option('limit'));

        if ($urgentTasks->isEmpty()) {
            $this->info('✅ No urgent tasks found.');
            return 0;
        }

        $this->info("🚨 Found {$urgentTasks->count()} urgent tasks:");
        $this->newLine();

        $headers = ['ID', 'Title', 'Priority Score', 'Status', 'Deadline', 'Progress'];
        $rows = [];

        foreach ($urgentTasks as $task) {
            $rows[] = [
                $task->id,
                $task->title,
                round($task->priority_score, 1),
                $task->status,
                $task->deadline ? $task->deadline->format('Y-m-d') : 'No deadline',
                $task->progress . '%'
            ];
        }

        $this->table($headers, $rows);
        return 0;
    }

    /**
     * Show task dependencies
     */
    private function showDependencies(): int
    {
        $dependencies = TaskDependency::with(['dependentTask', 'prerequisiteTask'])
            ->limit($this->option('limit'))
            ->get();

        if ($dependencies->isEmpty()) {
            $this->info('✅ No task dependencies found.');
            return 0;
        }

        $this->info("🔗 Found {$dependencies->count()} task dependencies:");
        $this->newLine();

        $headers = ['Dependent Task', 'Depends On', 'Type', 'Active'];
        $rows = [];

        foreach ($dependencies as $dependency) {
            $rows[] = [
                $dependency->dependentTask->title ?? 'N/A',
                $dependency->prerequisiteTask->title ?? 'N/A',
                $dependency->dependency_type,
                $dependency->is_active ? 'Yes' : 'No'
            ];
        }

        $this->table($headers, $rows);
        return 0;
    }

    /**
     * Adjust task priorities
     */
    private function adjustPriorities(): int
    {
        $this->info('🎯 Adjusting task priorities...');
        
        $adjusted = $this->priorityService->autoAdjustPriorities();

        if (empty($adjusted)) {
            $this->info('✅ No priority adjustments needed.');
            return 0;
        }

        $this->info("📝 Adjusted priorities for " . count($adjusted) . " tasks:");
        $this->newLine();

        $headers = ['Task ID', 'Title', 'Old Priority', 'New Priority', 'Reason'];
        $rows = [];

        foreach ($adjusted as $adjustment) {
            $rows[] = [
                $adjustment['task_id'],
                $adjustment['title'],
                $adjustment['old_priority'],
                $adjustment['new_priority'],
                $adjustment['reason']
            ];
        }

        $this->table($headers, $rows);
        return 0;
    }

    /**
     * Show workflow information
     */
    private function showWorkflowInfo(): int
    {
        $this->info('⚙️ Task Workflow Information');
        $this->newLine();

        // Show allowed transitions
        $this->info('Allowed Status Transitions:');
        $statuses = Task::getStatuses();
        
        foreach ($statuses as $status => $label) {
            $allowedTransitions = $this->workflowService->getAllowedTransitions($status);
            $transitionLabels = array_map(function ($transition) use ($statuses) {
                return $statuses[$transition] ?? $transition;
            }, $allowedTransitions);
            
            $this->line("  {$label} → " . implode(', ', $transitionLabels));
        }
        $this->newLine();

        // Show workflow stats for a sample task
        $sampleTask = Task::with(['assignments', 'dependencies', 'dependents'])->first();
        if ($sampleTask) {
            $this->info("Sample Task Workflow Stats (Task #{$sampleTask->id}):");
            $stats = $this->workflowService->getTaskWorkflowStats($sampleTask);
            
            foreach ($stats as $key => $value) {
                if (is_array($value)) {
                    $this->line("  {$key}: " . implode(', ', $value));
                } else {
                    $this->line("  {$key}: {$value}");
                }
            }
        }

        return 0;
    }
} 