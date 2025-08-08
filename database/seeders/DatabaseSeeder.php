<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // إنشاء مستخدم افتراضي للنظام
        User::firstOrCreate(
            ['email' => 'admin@system.com'],
            [
                'name' => 'مدير النظام',
                'password' => bcrypt('password'),
                'role_name' => 'Admin',
                'status' => 'Active',
            ]
        );

        // إنشاء مهارات تجريبية
        $this->createSampleSkills();

        // Call Task Management Seeder
        $this->call([
            TaskManagementSeeder::class,
        ]);
    }

    /**
     * إنشاء مهارات تجريبية وربطها بطلبات التطوع
     */
    private function createSampleSkills()
    {
        // إنشاء مهارات
        $skills = [
            [
                'name' => 'البرمجة',
                'description' => 'مهارات البرمجة والتطوير',
                'category' => 'technical',
                'level' => 'intermediate',
                'is_active' => true
            ],
            [
                'name' => 'التواصل',
                'description' => 'مهارات التواصل الفعال',
                'category' => 'soft_skills',
                'level' => 'advanced',
                'is_active' => true
            ],
            [
                'name' => 'الترجمة',
                'description' => 'مهارات الترجمة واللغات',
                'category' => 'language',
                'level' => 'expert',
                'is_active' => true
            ],
            [
                'name' => 'التصميم',
                'description' => 'مهارات التصميم الإبداعي',
                'category' => 'creative',
                'level' => 'beginner',
                'is_active' => true
            ]
        ];

        foreach ($skills as $skillData) {
            $skill = \App\Models\Skill::firstOrCreate(
                ['name' => $skillData['name']],
                $skillData
            );
        }

        // ربط المهارات بطلبات التطوع الموجودة
        $volunteerRequests = \App\Models\VolunteerRequest::all();
        $skills = \App\Models\Skill::all();

        foreach ($volunteerRequests as $request) {
            // ربط كل طلب بـ 2-3 مهارات عشوائية
            $randomSkills = $skills->random(rand(2, 3));
            foreach ($randomSkills as $skill) {
                $request->skills()->attach($skill->id, [
                    'level' => ['beginner', 'intermediate', 'advanced', 'expert'][rand(0, 3)],
                    'years_experience' => rand(1, 5)
                ]);
            }
        }
    }
}
