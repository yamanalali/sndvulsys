<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Category;
use App\Models\Task;
use App\Models\Assignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class BasicTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 بدء إنشاء البيانات التجريبية...');
        
        // إنشاء مستخدمين
        $admin = User::firstOrCreate([
            'email' => 'admin@test.com'
        ], [
            'name' => 'مدير النظام',
            'user_id' => 'ADMIN_001',
            'password' => Hash::make('password'),
            'role_name' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        
        $user1 = User::firstOrCreate([
            'email' => 'user1@test.com'
        ], [
            'name' => 'أحمد محمد',
            'user_id' => 'USR_001',
            'password' => Hash::make('password'),
            'role_name' => 'volunteer',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        
        $user2 = User::firstOrCreate([
            'email' => 'user2@test.com'
        ], [
            'name' => 'فاطمة علي',
            'user_id' => 'USR_002',
            'password' => Hash::make('password'),
            'role_name' => 'volunteer',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        
        // إنشاء فئات
        $category1 = Category::firstOrCreate(['name' => 'تطوير البرمجيات'], [
            'description' => 'مهام تطوير وبرمجة التطبيقات'
        ]);
        
        $category2 = Category::firstOrCreate(['name' => 'التسويق الرقمي'], [
            'description' => 'أنشطة التسويق والترويج'
        ]);
        
        // إنشاء مشاريع
        $project1 = Project::firstOrCreate(['name' => 'نظام إدارة المهام'], [
            'description' => 'تطوير نظام شامل لإدارة المهام والمشاريع',
            'status' => 'active',
            'start_date' => now()->subDays(30),
            'end_date' => now()->addDays(60),
            'manager_id' => $admin->id,
        ]);
        
        $project2 = Project::firstOrCreate(['name' => 'موقع الشركة'], [
            'description' => 'تصميم وتطوير موقع الشركة الرسمي',
            'status' => 'active',
            'start_date' => now()->subDays(15),
            'end_date' => now()->addDays(45),
            'manager_id' => $admin->id,
        ]);
        
        // إنشاء مهام
        $this->createTask('تحليل متطلبات النظام', 'completed', 'high', -20, $project1, $category1, $admin, $user1);
        $this->createTask('تطوير واجهة المستخدم', 'in_progress', 'medium', -10, $project1, $category1, $admin, $user1);
        $this->createTask('اختبار النظام', 'new', 'medium', 5, $project1, $category1, $admin, $user2);
        $this->createTask('إعداد استراتيجية التسويق', 'completed', 'high', -25, $project2, $category2, $admin, $user2);
        $this->createTask('تصميم المواد الإعلانية', 'in_progress', 'medium', -8, $project2, $category2, $admin, $user1);
        $this->createTask('إطلاق حملة التسويق', 'new', 'medium', 2, $project2, $category2, $admin, $user2);
        $this->createTask('مهمة متأخرة - مراجعة الكود', 'in_progress', 'high', -5, $project1, $category1, $admin, $user1);
        
        // إنشاء مهام متكررة
        $this->createRecurringTask('تقرير الحالة اليومي', 'daily', ['interval' => 1], 'medium', $project1, $category1, $admin, $user1);
        $this->createRecurringTask('اجتماع الفريق الأسبوعي', 'weekly', ['interval' => 1, 'days_of_week' => [1]], 'medium', $project1, $category1, $admin, $user2);
        $this->createRecurringTask('التقرير الشهري', 'monthly', ['interval' => 1, 'day_of_month' => 1], 'high', $project1, $category1, $admin, $user1);
        
        $this->command->info('✅ تم إنشاء البيانات التجريبية بنجاح!');
        $this->command->info('👥 المستخدمين: 3');
        $this->command->info('📂 المشاريع: 2');
        $this->command->info('📋 الفئات: 2');
        $this->command->info('✅ المهام العادية: 7');
        $this->command->info('🔄 المهام المتكررة: 3');
    }
    
    private function createTask($title, $status, $priority, $daysOffset, $project, $category, $creator, $assignee)
    {
        $startDate = now()->addDays($daysOffset - 7);
        $deadline = now()->addDays($daysOffset);
        
        $task = Task::create([
            'title' => $title,
            'description' => 'وصف تفصيلي لمهمة: ' . $title,
            'status' => $status,
            'priority' => $priority,
            'project_id' => $project->id,
            'category_id' => $category->id,
            'created_by' => $creator->id,
            'start_date' => $startDate,
            'deadline' => $deadline,
            'progress' => $this->getProgressByStatus($status),
            'completed_at' => $status === 'completed' ? $deadline : null,
        ]);
        
        // تكليف المهمة
        Assignment::create([
            'task_id' => $task->id,
            'user_id' => $assignee->id,
            'assigned_at' => now()->subDays(rand(1, 3)),
            'status' => 'assigned',
            'progress' => $this->getProgressByStatus($status),
        ]);
        
        return $task;
    }
    
    private function createRecurringTask($title, $pattern, $config, $priority, $project, $category, $creator, $assignee)
    {
        $startDate = now()->startOfDay();
        
        $task = Task::create([
            'title' => $title,
            'description' => 'مهمة متكررة: ' . $title,
            'status' => 'new',
            'priority' => $priority,
            'project_id' => $project->id,
            'category_id' => $category->id,
            'created_by' => $creator->id,
            'start_date' => $startDate,
            'deadline' => $startDate->copy()->addDays(1),
            'is_recurring' => true,
            'recurrence_pattern' => $pattern,
            'recurrence_config' => $config,
            'recurrence_start_date' => $startDate,
            'recurrence_end_date' => now()->addMonths(6),
            'recurrence_max_occurrences' => 30,
            'recurrence_current_count' => 0,
            'recurring_active' => true,
        ]);
        
        // تكليف المهمة
        Assignment::create([
            'task_id' => $task->id,
            'user_id' => $assignee->id,
            'assigned_at' => now(),
            'status' => 'assigned',
            'progress' => 0,
        ]);
        
        // إنشاء نسخة واحدة من المهمة المتكررة
        $instance = Task::create([
            'title' => $task->title . ' - ' . now()->format('Y-m-d'),
            'description' => $task->description,
            'status' => 'completed',
            'priority' => $task->priority,
            'project_id' => $task->project_id,
            'category_id' => $task->category_id,
            'created_by' => $task->created_by,
            'start_date' => now(),
            'deadline' => now()->addHours(2),
            'parent_task_id' => $task->id,
            'is_recurring_instance' => true,
            'progress' => 100,
            'completed_at' => now()->addHours(1),
        ]);
        
        Assignment::create([
            'task_id' => $instance->id,
            'user_id' => $assignee->id,
            'assigned_at' => now(),
            'status' => 'completed',
            'progress' => 100,
            'completed_at' => now()->addHours(1),
        ]);
        
        return $task;
    }
    
    private function getProgressByStatus($status)
    {
        return match($status) {
            'completed' => 100,
            'in_progress' => rand(25, 75),
            'pending' => rand(10, 30),
            default => 0
        };
    }
}