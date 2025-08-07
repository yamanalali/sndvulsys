<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Category;
use App\Models\Task;
use App\Models\Assignment;
use App\Models\RecurringTaskException;
use Carbon\Carbon;

class DataIntegrityTest extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔍 بدء اختبار سلامة البيانات...');
        
        // اختبار المستخدمين
        $this->testUsers();
        
        // اختبار المشاريع
        $this->testProjects();
        
        // اختبار الفئات
        $this->testCategories();
        
        // اختبار المهام
        $this->testTasks();
        
        // اختبار التكليفات
        $this->testAssignments();
        
        // اختبار المهام المتكررة
        $this->testRecurringTasks();
        
        // اختبار استثناءات المهام المتكررة
        $this->testRecurringExceptions();
        
        // اختبار العلاقات
        $this->testRelationships();
        
        $this->command->info('✅ تم الانتهاء من اختبار سلامة البيانات بنجاح!');
    }
    
    private function testUsers()
    {
        $userCount = User::count();
        $this->command->info("👥 المستخدمين: {$userCount}");
        
        $admin = User::where('role_name', 'admin')->first();
        if ($admin) {
            $this->command->info("✅ مدير النظام: {$admin->name} ({$admin->email})");
        }
        
        $volunteers = User::where('role_name', 'volunteer')->count();
        $this->command->info("🙋 المتطوعين: {$volunteers}");
    }
    
    private function testProjects()
    {
        $projectCount = Project::count();
        $this->command->info("📂 المشاريع: {$projectCount}");
        
        $activeProjects = Project::where('status', 'active')->count();
        $this->command->info("🟢 المشاريع النشطة: {$activeProjects}");
        
        foreach (Project::all() as $project) {
            $taskCount = $project->tasks()->count();
            $this->command->info("  - {$project->name}: {$taskCount} مهمة");
        }
    }
    
    private function testCategories()
    {
        $categoryCount = Category::count();
        $this->command->info("📋 الفئات: {$categoryCount}");
        
        foreach (Category::all() as $category) {
            $taskCount = $category->tasks()->count();
            $this->command->info("  - {$category->name}: {$taskCount} مهمة");
        }
    }
    
    private function testTasks()
    {
        $totalTasks = Task::count();
        $this->command->info("✅ إجمالي المهام: {$totalTasks}");
        
        // المهام حسب الحالة
        $statuses = ['new', 'in_progress', 'pending', 'completed', 'cancelled'];
        foreach ($statuses as $status) {
            $count = Task::where('status', $status)->count();
            $this->command->info("  - {$status}: {$count}");
        }
        
        // المهام حسب الأولوية
        $priorities = ['low', 'medium', 'high', 'critical'];
        foreach ($priorities as $priority) {
            $count = Task::where('priority', $priority)->count();
            $this->command->info("  - الأولوية {$priority}: {$count}");
        }
        
        // المهام المتأخرة
        $overdueTasks = Task::where('deadline', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
        $this->command->info("⚠️ المهام المتأخرة: {$overdueTasks}");
        
        // المهام القريبة (خلال 7 أيام)
        $upcomingTasks = Task::whereBetween('deadline', [now(), now()->addDays(7)])
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
        $this->command->info("📅 المهام القريبة (7 أيام): {$upcomingTasks}");
    }
    
    private function testAssignments()
    {
        $assignmentCount = Assignment::count();
        $this->command->info("👤 التكليفات: {$assignmentCount}");
        
        // التكليفات حسب الحالة
        $statuses = ['assigned', 'in_progress', 'submitted', 'completed', 'overdue', 'cancelled'];
        foreach ($statuses as $status) {
            $count = Assignment::where('status', $status)->count();
            if ($count > 0) {
                $this->command->info("  - {$status}: {$count}");
            }
        }
        
        // المستخدمين المكلفين
        $assignedUsers = Assignment::distinct('user_id')->count('user_id');
        $this->command->info("👥 المستخدمين المكلفين: {$assignedUsers}");
    }
    
    private function testRecurringTasks()
    {
        $recurringCount = Task::where('is_recurring', true)->count();
        $this->command->info("🔄 المهام المتكررة: {$recurringCount}");
        
        $activeRecurring = Task::where('is_recurring', true)
            ->where('recurring_active', true)
            ->count();
        $this->command->info("🔄 المهام المتكررة النشطة: {$activeRecurring}");
        
        // المهام المتكررة حسب النمط
        $patterns = ['daily', 'weekly', 'monthly', 'yearly'];
        foreach ($patterns as $pattern) {
            $count = Task::where('is_recurring', true)
                ->where('recurrence_pattern', $pattern)
                ->count();
            if ($count > 0) {
                $this->command->info("  - {$pattern}: {$count}");
            }
        }
        
        // نسخ المهام المتكررة
        $instanceCount = Task::where('is_recurring_instance', true)->count();
        $this->command->info("📋 نسخ المهام المتكررة: {$instanceCount}");
    }
    
    private function testRecurringExceptions()
    {
        $exceptionCount = RecurringTaskException::count();
        $this->command->info("⚠️ استثناءات المهام المتكررة: {$exceptionCount}");
        
        if ($exceptionCount > 0) {
            $types = ['skip', 'reschedule', 'modify'];
            foreach ($types as $type) {
                $count = RecurringTaskException::where('exception_type', $type)->count();
                if ($count > 0) {
                    $this->command->info("  - {$type}: {$count}");
                }
            }
        }
    }
    
    private function testRelationships()
    {
        $this->command->info("🔗 اختبار العلاقات:");
        
        // المهام بدون مشروع
        $tasksWithoutProject = Task::whereNull('project_id')->count();
        if ($tasksWithoutProject > 0) {
            $this->command->warn("⚠️ مهام بدون مشروع: {$tasksWithoutProject}");
        } else {
            $this->command->info("✅ جميع المهام مرتبطة بمشاريع");
        }
        
        // المهام بدون فئة
        $tasksWithoutCategory = Task::whereNull('category_id')->count();
        if ($tasksWithoutCategory > 0) {
            $this->command->warn("⚠️ مهام بدون فئة: {$tasksWithoutCategory}");
        } else {
            $this->command->info("✅ جميع المهام مرتبطة بفئات");
        }
        
        // المهام بدون تكليف
        $tasksWithoutAssignment = Task::whereDoesntHave('assignments')->count();
        if ($tasksWithoutAssignment > 0) {
            $this->command->warn("⚠️ مهام بدون تكليف: {$tasksWithoutAssignment}");
        } else {
            $this->command->info("✅ جميع المهام مكلفة");
        }
        
        // التكليفات المكسورة
        $brokenAssignments = Assignment::whereDoesntHave('task')->count();
        if ($brokenAssignments > 0) {
            $this->command->error("❌ تكليفات مكسورة: {$brokenAssignments}");
        } else {
            $this->command->info("✅ جميع التكليفات سليمة");
        }
    }
}