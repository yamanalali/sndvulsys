<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Assignment;
use App\Models\Category;
use Carbon\Carbon;

class ProgressTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مستخدم تجريبي
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'مستخدم تجريبي',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // إنشاء فئات
        $categories = [
            'تطوير' => Category::firstOrCreate(['name' => 'تطوير']),
            'تصميم' => Category::firstOrCreate(['name' => 'تصميم']),
            'اختبار' => Category::firstOrCreate(['name' => 'اختبار']),
            'توثيق' => Category::firstOrCreate(['name' => 'توثيق']),
        ];

        // إنشاء مشاريع
        $projects = [
            'مشروع تطوير النظام' => Project::firstOrCreate(
                ['name' => 'مشروع تطوير النظام'],
                [
                    'description' => 'تطوير نظام إدارة المهام',
                    'start_date' => Carbon::now()->subDays(30),
                    'end_date' => Carbon::now()->addDays(60),
                    'status' => 'active',
                ]
            ),
            'مشروع تحسين الواجهة' => Project::firstOrCreate(
                ['name' => 'مشروع تحسين الواجهة'],
                [
                    'description' => 'تحسين واجهة المستخدم',
                    'start_date' => Carbon::now()->subDays(15),
                    'end_date' => Carbon::now()->addDays(30),
                    'status' => 'active',
                ]
            ),
        ];

        // إنشاء مهام متنوعة
        $tasks = [
            // مهام مكتملة
            [
                'title' => 'تصميم قاعدة البيانات',
                'description' => 'تصميم هيكل قاعدة البيانات للنظام',
                'project_id' => $projects['مشروع تطوير النظام']->id,
                'category_id' => $categories['تصميم']->id,
                'status' => 'completed',
                'priority' => 'high',
                'progress' => 100,
                'start_date' => Carbon::now()->subDays(25),
                'deadline' => Carbon::now()->subDays(5),
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'title' => 'تطوير واجهة المستخدم',
                'description' => 'تطوير واجهة المستخدم الرئيسية',
                'project_id' => $projects['مشروع تطوير النظام']->id,
                'category_id' => $categories['تطوير']->id,
                'status' => 'completed',
                'priority' => 'high',
                'progress' => 100,
                'start_date' => Carbon::now()->subDays(20),
                'deadline' => Carbon::now()->subDays(2),
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            
            // مهام قيد التنفيذ
            [
                'title' => 'تطوير نظام المصادقة',
                'description' => 'تطوير نظام تسجيل الدخول والمصادقة',
                'project_id' => $projects['مشروع تطوير النظام']->id,
                'category_id' => $categories['تطوير']->id,
                'status' => 'in_progress',
                'priority' => 'high',
                'progress' => 75,
                'start_date' => Carbon::now()->subDays(10),
                'deadline' => Carbon::now()->addDays(5),
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'title' => 'تصميم واجهة لوحة التحكم',
                'description' => 'تصميم واجهة لوحة التحكم للمديرين',
                'project_id' => $projects['مشروع تحسين الواجهة']->id,
                'category_id' => $categories['تصميم']->id,
                'status' => 'in_progress',
                'priority' => 'medium',
                'progress' => 60,
                'start_date' => Carbon::now()->subDays(8),
                'deadline' => Carbon::now()->addDays(7),
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            
            // مهام معلقة
            [
                'title' => 'اختبار النظام',
                'description' => 'إجراء اختبارات شاملة للنظام',
                'project_id' => $projects['مشروع تطوير النظام']->id,
                'category_id' => $categories['اختبار']->id,
                'status' => 'pending',
                'priority' => 'medium',
                'progress' => 0,
                'start_date' => Carbon::now()->subDays(5),
                'deadline' => Carbon::now()->addDays(10),
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            
            // مهام جديدة
            [
                'title' => 'توثيق النظام',
                'description' => 'كتابة دليل المستخدم والوثائق التقنية',
                'project_id' => $projects['مشروع تطوير النظام']->id,
                'category_id' => $categories['توثيق']->id,
                'status' => 'new',
                'priority' => 'low',
                'progress' => 0,
                'start_date' => Carbon::now()->addDays(5),
                'deadline' => Carbon::now()->addDays(20),
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            
            // مهام متأخرة
            [
                'title' => 'إصلاح الأخطاء البرمجية',
                'description' => 'إصلاح الأخطاء المكتشفة في النظام',
                'project_id' => $projects['مشروع تطوير النظام']->id,
                'category_id' => $categories['تطوير']->id,
                'status' => 'in_progress',
                'priority' => 'high',
                'progress' => 30,
                'start_date' => Carbon::now()->subDays(15),
                'deadline' => Carbon::now()->subDays(3), // متأخرة
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            
            // مهام مستحقة قريباً
            [
                'title' => 'تحسين الأداء',
                'description' => 'تحسين أداء النظام وزيادة السرعة',
                'project_id' => $projects['مشروع تحسين الواجهة']->id,
                'category_id' => $categories['تطوير']->id,
                'status' => 'in_progress',
                'priority' => 'medium',
                'progress' => 40,
                'start_date' => Carbon::now()->subDays(5),
                'deadline' => Carbon::now()->addDays(2), // مستحقة قريباً
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(1),
            ],
        ];

        // إنشاء المهام وتعيينها للمستخدم
        foreach ($tasks as $taskData) {
            $task = Task::firstOrCreate(
                ['title' => $taskData['title'], 'project_id' => $taskData['project_id']],
                $taskData
            );

            // إنشاء تعيين للمستخدم
            Assignment::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                ],
                [
                    'assigned_at' => $task->created_at,
                    'status' => 'assigned',
                ]
            );
        }

        $this->command->info('تم إنشاء بيانات تجريبية لصفحة تتبع التقدم بنجاح!');
        $this->command->info('يمكنك تسجيل الدخول باستخدام: test@example.com / password');
    }
} 