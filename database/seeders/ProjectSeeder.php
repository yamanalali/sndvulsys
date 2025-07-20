<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مستخدمين إذا لم يكونوا موجودين
        $user1 = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'مدير النظام',
                'password' => bcrypt('password'),
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'مستخدم عادي',
                'password' => bcrypt('password'),
            ]
        );

        // إنشاء مشاريع تجريبية
        $projects = [
            [
                'name' => 'تطوير موقع الويب',
                'description' => 'تطوير موقع ويب حديث ومتجاوب للشركة',
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addMonths(3),
                'manager_id' => $user1->id,
            ],
            [
                'name' => 'تطبيق الهاتف المحمول',
                'description' => 'تطوير تطبيق iOS و Android للعملاء',
                'status' => 'active',
                'start_date' => now()->subMonth(),
                'end_date' => now()->addMonths(6),
                'manager_id' => $user1->id,
            ],
            [
                'name' => 'نظام إدارة المحتوى',
                'description' => 'بناء نظام إدارة محتوى مخصص',
                'status' => 'on_hold',
                'start_date' => now()->subWeeks(2),
                'end_date' => now()->addMonths(2),
                'manager_id' => $user2->id,
            ],
            [
                'name' => 'تحديث قاعدة البيانات',
                'description' => 'تحديث وتحسين قاعدة البيانات الحالية',
                'status' => 'completed',
                'start_date' => now()->subMonths(2),
                'end_date' => now()->subWeek(),
                'manager_id' => $user1->id,
            ],
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }
    }
}
