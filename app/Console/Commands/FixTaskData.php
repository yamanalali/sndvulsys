<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Assignment;
use App\Models\User;
use App\Models\Category;
use App\Models\Project;

class FixTaskData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:fix-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إصلاح بيانات المهام والتخصيصات';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔧 بدء إصلاح بيانات المهام...');
        
        // التحقق من وجود مستخدمين
        $users = User::all();
        if ($users->isEmpty()) {
            $this->error('لا يوجد مستخدمين في النظام!');
            return 1;
        }
        
        // التحقق من وجود مشاريع
        $projects = Project::all();
        if ($projects->isEmpty()) {
            $this->info('إنشاء مشروع افتراضي...');
            $project = Project::create([
                'name' => 'مشروع عام',
                'description' => 'مشروع افتراضي للمهام العامة',
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addYear(),
            ]);
        } else {
            $project = $projects->first();
        }
        
        // التحقق من وجود فئات
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->info('إنشاء فئة افتراضية...');
            $category = Category::create([
                'name' => 'عام',
                'slug' => 'general',
                'description' => 'فئة عامة للمهام',
                'is_active' => true,
            ]);
        } else {
            $category = $categories->first();
        }
        
        // إصلاح المهام بدون مشروع أو فئة
        $tasksWithoutProject = Task::whereNull('project_id')->get();
        foreach ($tasksWithoutProject as $task) {
            $task->update(['project_id' => $project->id]);
            $this->line("تم إصلاح المهمة {$task->id} - إضافة مشروع");
        }
        
        $tasksWithoutCategory = Task::whereNull('category_id')->get();
        foreach ($tasksWithoutCategory as $task) {
            $task->update(['category_id' => $category->id]);
            $this->line("تم إصلاح المهمة {$task->id} - إضافة فئة");
        }
        
        // إصلاح المهام بدون مكلفين
        $tasksWithoutAssignments = Task::whereDoesntHave('assignments')->get();
        foreach ($tasksWithoutAssignments as $task) {
            $user = $users->first();
            Assignment::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'assigned_at' => now(),
                'status' => 'assigned',
            ]);
            $this->line("تم إصلاح المهمة {$task->id} - إضافة مكلف");
        }
        
        // حذف التخصيصات غير الصحيحة
        $invalidAssignments = Assignment::whereDoesntHave('user')->orWhereDoesntHave('task')->get();
        foreach ($invalidAssignments as $assignment) {
            $assignment->delete();
            $this->line("تم حذف التخصيص غير الصحيح {$assignment->id}");
        }
        
        // إصلاح المهام بدون created_by
        $tasksWithoutCreator = Task::whereNull('created_by')->get();
        foreach ($tasksWithoutCreator as $task) {
            $user = $users->first();
            $task->update(['created_by' => $user->id]);
            $this->line("تم إصلاح المهمة {$task->id} - إضافة منشئ");
        }
        
        $this->info('✅ تم إصلاح بيانات المهام بنجاح!');
        
        // عرض الإحصائيات
        $this->newLine();
        $this->info('📊 إحصائيات بعد الإصلاح:');
        $this->line("المهام: " . Task::count());
        $this->line("التخصيصات: " . Assignment::count());
        $this->line("المستخدمين: " . User::count());
        $this->line("المشاريع: " . Project::count());
        $this->line("الفئات: " . Category::count());
        
        return 0;
    }
}
