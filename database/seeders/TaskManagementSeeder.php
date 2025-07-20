<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Category;
use App\Models\User;
use App\Models\Assignment;
use App\Models\TaskDependency;
use App\Services\TaskWorkflowService;
use App\Services\TaskPriorityService;

class TaskManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Task Management Seeder...');

        // Create categories hierarchy
        $this->command->info('📁 Creating categories...');
        $categories = $this->createCategories();

        // Create users
        $this->command->info('👥 Creating users...');
        $users = $this->createUsers();

        // Create tasks
        $this->command->info('📋 Creating tasks...');
        $tasks = $this->createTasks($categories, $users);

        // Create assignments
        $this->command->info('👤 Creating assignments...');
        $this->createAssignments($tasks, $users);

        // Create task dependencies
        $this->command->info('🔗 Creating task dependencies...');
        $this->createTaskDependencies($tasks);

        // Test workflow service
        $this->command->info('⚙️ Testing workflow service...');
        $this->testWorkflowService($tasks);

        // Test priority service
        $this->command->info('🎯 Testing priority service...');
        $this->testPriorityService();

        $this->command->info('✅ Task Management Seeder completed successfully!');
    }

    /**
     * Create categories hierarchy
     */
    private function createCategories(): array
    {
        $categoryFactory = new \Database\Factories\CategoryFactory();
        return $categoryFactory->createHierarchy();
    }

    /**
     * Create users
     */
    private function createUsers(): \Illuminate\Database\Eloquent\Collection
    {
        $users = new \Illuminate\Database\Eloquent\Collection();

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'مدير النظام',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $users->push($admin);

        // Create team members
        $teamMembers = [
            ['name' => 'أحمد محمد', 'email' => 'ahmed@example.com'],
            ['name' => 'فاطمة علي', 'email' => 'fatima@example.com'],
            ['name' => 'محمد حسن', 'email' => 'mohammed@example.com'],
            ['name' => 'سارة أحمد', 'email' => 'sara@example.com'],
            ['name' => 'علي محمود', 'email' => 'ali@example.com'],
            ['name' => 'نور الدين', 'email' => 'nour@example.com'],
            ['name' => 'ليلى محمد', 'email' => 'layla@example.com'],
            ['name' => 'حسن أحمد', 'email' => 'hassan@example.com'],
        ];

        foreach ($teamMembers as $member) {
            $user = User::factory()->create([
                'name' => $member['name'],
                'email' => $member['email'],
                'password' => bcrypt('password'),
            ]);
            $users->push($user);
        }

        return $users;
    }

    /**
     * Create tasks
     */
    private function createTasks(array $categories, \Illuminate\Database\Eloquent\Collection $users): \Illuminate\Database\Eloquent\Collection
    {
        $tasks = new \Illuminate\Database\Eloquent\Collection();

        // Create urgent tasks
        $urgentTasks = [
            [
                'title' => 'إصلاح خطأ حرج في النظام',
                'description' => 'يجب إصلاح خطأ في قاعدة البيانات يسبب توقف النظام',
                'category' => $categories['backend'],
                'priority' => Task::PRIORITY_URGENT,
                'status' => Task::STATUS_IN_PROGRESS,
                'deadline' => now()->addDays(1),
                'progress' => 60
            ],
            [
                'title' => 'تحديث أمان النظام',
                'description' => 'تطبيق آخر تحديثات الأمان على الخادم',
                'category' => $categories['maintenance'],
                'priority' => Task::PRIORITY_URGENT,
                'status' => Task::STATUS_NEW,
                'deadline' => now()->addDays(2),
                'progress' => 0
            ]
        ];

        foreach ($urgentTasks as $taskData) {
            $task = Task::factory()->create([
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'category_id' => $taskData['category']->id,
                'priority' => $taskData['priority'],
                'status' => $taskData['status'],
                'deadline' => $taskData['deadline'],
                'progress' => $taskData['progress'],
                'created_by' => $users->random()->id,
                'assigned_to' => $users->random()->id,
            ]);
            $tasks->push($task);
        }

        // Create high priority tasks
        $highPriorityTasks = [
            [
                'title' => 'تطوير واجهة المستخدم الجديدة',
                'description' => 'تصميم وتطوير واجهة مستخدم حديثة ومتجاوبة',
                'category' => $categories['frontend'],
                'priority' => Task::PRIORITY_HIGH,
                'status' => Task::STATUS_IN_PROGRESS,
                'deadline' => now()->addDays(7),
                'progress' => 40
            ],
            [
                'title' => 'تحسين أداء قاعدة البيانات',
                'description' => 'تحسين استعلامات قاعدة البيانات لزيادة الأداء',
                'category' => $categories['backend'],
                'priority' => Task::PRIORITY_HIGH,
                'status' => Task::STATUS_PENDING,
                'deadline' => now()->addDays(10),
                'progress' => 20
            ],
            [
                'title' => 'تصميم شعار الشركة الجديد',
                'description' => 'إنشاء شعار جديد يعكس هوية الشركة',
                'category' => $categories['graphic_design'],
                'priority' => Task::PRIORITY_HIGH,
                'status' => Task::STATUS_NEW,
                'deadline' => now()->addDays(5),
                'progress' => 0
            ]
        ];

        foreach ($highPriorityTasks as $taskData) {
            $task = Task::factory()->create([
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'category_id' => $taskData['category']->id,
                'priority' => $taskData['priority'],
                'status' => $taskData['status'],
                'deadline' => $taskData['deadline'],
                'progress' => $taskData['progress'],
                'created_by' => $users->random()->id,
                'assigned_to' => $users->random()->id,
            ]);
            $tasks->push($task);
        }

        // Create medium priority tasks
        $mediumPriorityTasks = [
            [
                'title' => 'كتابة دليل المستخدم',
                'description' => 'إنشاء دليل شامل للمستخدمين النهائيين',
                'category' => $categories['documentation'],
                'priority' => Task::PRIORITY_MEDIUM,
                'status' => Task::STATUS_IN_PROGRESS,
                'deadline' => now()->addDays(14),
                'progress' => 70
            ],
            [
                'title' => 'اختبار وحدات النظام',
                'description' => 'إجراء اختبارات شاملة على جميع وحدات النظام',
                'category' => $categories['testing'],
                'priority' => Task::PRIORITY_MEDIUM,
                'status' => Task::STATUS_NEW,
                'deadline' => now()->addDays(21),
                'progress' => 0
            ],
            [
                'title' => 'تحليل متطلبات المستخدمين',
                'description' => 'جمع وتحليل متطلبات المستخدمين للمشروع الجديد',
                'category' => $categories['research'],
                'priority' => Task::PRIORITY_MEDIUM,
                'status' => Task::STATUS_COMPLETED,
                'deadline' => now()->subDays(5),
                'progress' => 100,
                'completed_at' => now()->subDays(3)
            ]
        ];

        foreach ($mediumPriorityTasks as $taskData) {
            $task = Task::factory()->create([
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'category_id' => $taskData['category']->id,
                'priority' => $taskData['priority'],
                'status' => $taskData['status'],
                'deadline' => $taskData['deadline'],
                'progress' => $taskData['progress'],
                'completed_at' => $taskData['completed_at'] ?? null,
                'created_by' => $users->random()->id,
                'assigned_to' => $users->random()->id,
            ]);
            $tasks->push($task);
        }

        // Create low priority tasks
        $lowPriorityTasks = [
            [
                'title' => 'تنظيف الملفات المؤقتة',
                'description' => 'حذف الملفات المؤقتة والغير مستخدمة',
                'category' => $categories['maintenance'],
                'priority' => Task::PRIORITY_LOW,
                'status' => Task::STATUS_NEW,
                'deadline' => now()->addDays(30),
                'progress' => 0
            ],
            [
                'title' => 'تحديث المكتبات',
                'description' => 'تحديث جميع المكتبات إلى أحدث إصدار',
                'category' => $categories['maintenance'],
                'priority' => Task::PRIORITY_LOW,
                'status' => Task::STATUS_NEW,
                'deadline' => now()->addDays(45),
                'progress' => 0
            ]
        ];

        foreach ($lowPriorityTasks as $taskData) {
            $task = Task::factory()->create([
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'category_id' => $taskData['category']->id,
                'priority' => $taskData['priority'],
                'status' => $taskData['status'],
                'deadline' => $taskData['deadline'],
                'progress' => $taskData['progress'],
                'created_by' => $users->random()->id,
                'assigned_to' => $users->random()->id,
            ]);
            $tasks->push($task);
        }

        // Create some overdue tasks
        $overdueTasks = Task::factory()
            ->count(3)
            ->overdue()
            ->create([
                'category_id' => $categories['support']->id,
                'created_by' => $users->random()->id,
                'assigned_to' => $users->random()->id,
            ]);

        $tasks = $tasks->merge($overdueTasks);

        // Create some recurring tasks
        $recurringTasks = Task::factory()
            ->count(2)
            ->recurring()
            ->create([
                'category_id' => $categories['maintenance']->id,
                'created_by' => $users->random()->id,
                'assigned_to' => $users->random()->id,
            ]);

        $tasks = $tasks->merge($recurringTasks);

        return $tasks;
    }

    /**
     * Create assignments
     */
    private function createAssignments(\Illuminate\Database\Eloquent\Collection $tasks, \Illuminate\Database\Eloquent\Collection $users): void
    {
        foreach ($tasks as $task) {
            // Create primary assignment
            Assignment::factory()->create([
                'task_id' => $task->id,
                'user_id' => $task->assigned_to,
                'status' => $this->getAssignmentStatusFromTaskStatus($task->status),
                'progress' => $task->progress,
                'assigned_at' => $task->created_at,
                'due_at' => $task->deadline,
                'completed_at' => $task->completed_at,
            ]);

            // Create additional assignments for some tasks
            if ($task->priority === Task::PRIORITY_URGENT || $task->priority === Task::PRIORITY_HIGH) {
                $additionalUsers = $users->where('id', '!=', $task->assigned_to)->random(rand(1, 2));
                
                foreach ($additionalUsers as $user) {
                    Assignment::factory()->create([
                        'task_id' => $task->id,
                        'user_id' => $user->id,
                        'status' => Assignment::STATUS_ASSIGNED,
                        'progress' => 0,
                        'assigned_at' => now(),
                        'due_at' => $task->deadline,
                    ]);
                }
            }
        }
    }

    /**
     * Create task dependencies
     */
    private function createTaskDependencies(\Illuminate\Database\Eloquent\Collection $tasks): void
    {
        $taskArray = $tasks->toArray();
        
        // Create some logical dependencies
        for ($i = 0; $i < min(10, count($taskArray) - 1); $i++) {
            $task = $taskArray[$i];
            $dependentTask = $taskArray[$i + 1];
            
            // Only create dependency if tasks are not completed
            if ($task['status'] !== Task::STATUS_COMPLETED && $dependentTask['status'] !== Task::STATUS_COMPLETED) {
                TaskDependency::create([
                    'task_id' => $dependentTask['id'],
                    'depends_on_task_id' => $task['id'],
                    'dependency_type' => TaskDependency::TYPE_FINISH_TO_START,
                    'is_active' => true,
                ]);
            }
        }

        // Create some start-to-start dependencies
        for ($i = 0; $i < min(5, count($taskArray) - 2); $i += 2) {
            $task = $taskArray[$i];
            $dependentTask = $taskArray[$i + 2];
            
            if ($task['status'] !== Task::STATUS_COMPLETED && $dependentTask['status'] !== Task::STATUS_COMPLETED) {
                TaskDependency::create([
                    'task_id' => $dependentTask['id'],
                    'depends_on_task_id' => $task['id'],
                    'dependency_type' => TaskDependency::TYPE_START_TO_START,
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * Test workflow service
     */
    private function testWorkflowService(\Illuminate\Database\Eloquent\Collection $tasks): void
    {
        $workflowService = new TaskWorkflowService();
        
        // Test status transitions
        $newTask = $tasks->where('status', Task::STATUS_NEW)->first();
        if ($newTask) {
            $workflowService->transitionTaskStatus($newTask, Task::STATUS_IN_PROGRESS);
            $this->command->info("✅ Transitioned task {$newTask->id} from NEW to IN_PROGRESS");
        }

        // Test workflow stats
        $inProgressTask = $tasks->where('status', Task::STATUS_IN_PROGRESS)->first();
        if ($inProgressTask) {
            $stats = $workflowService->getTaskWorkflowStats($inProgressTask);
            $this->command->info("✅ Workflow stats for task {$inProgressTask->id}: " . json_encode($stats));
        }
    }

    /**
     * Test priority service
     */
    private function testPriorityService(): void
    {
        $priorityService = new TaskPriorityService();
        
        // Test priority distribution
        $distribution = $priorityService->getPriorityDistribution();
        $this->command->info("✅ Priority distribution: " . json_encode($distribution));
        
        // Test urgent tasks
        $urgentTasks = $priorityService->getUrgentTasks();
        $this->command->info("✅ Found {$urgentTasks->count()} urgent tasks");
        
        // Test overdue tasks
        $overdueTasks = $priorityService->getOverdueTasks();
        $this->command->info("✅ Found {$overdueTasks->count()} overdue tasks");
    }

    /**
     * Get assignment status from task status
     */
    private function getAssignmentStatusFromTaskStatus(string $taskStatus): string
    {
        return match ($taskStatus) {
            Task::STATUS_NEW => Assignment::STATUS_ASSIGNED,
            Task::STATUS_IN_PROGRESS => Assignment::STATUS_IN_PROGRESS,
            Task::STATUS_PENDING => Assignment::STATUS_ASSIGNED,
            Task::STATUS_COMPLETED => Assignment::STATUS_COMPLETED,
            Task::STATUS_CANCELLED => Assignment::STATUS_CANCELLED,
            default => Assignment::STATUS_ASSIGNED,
        };
    }
} 