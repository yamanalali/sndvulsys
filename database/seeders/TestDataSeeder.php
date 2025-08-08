<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Category;
use App\Models\Task;
use App\Models\Assignment;
use App\Models\RecurringTaskException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 بدء إنشاء البيانات التجريبية...');
        
        // إنشاء مستخدمين تجريبيين
        $users = $this->createUsers();
        
        // إنشاء فئات المهام
        $categories = $this->createCategories();
        
        // إنشاء مشاريع تجريبية
        $projects = $this->createProjects($users);
        
        // إنشاء مهام عادية
        $tasks = $this->createRegularTasks($users, $projects, $categories);
        
        // إنشاء مهام متكررة
        $recurringTasks = $this->createRecurringTasks($users, $projects, $categories);
        
        // إنشاء تكليفات للمهام
        $this->createAssignments($tasks->merge($recurringTasks), $users);
        
        // إنشاء استثناءات للمهام المتكررة
        $this->createRecurringExceptions($recurringTasks, $users);
        
        $this->command->info('✅ تم إنشاء البيانات التجريبية بنجاح!');
        $this->command->info('👥 المستخدمين: ' . $users->count());
        $this->command->info('📂 المشاريع: ' . $projects->count());
        $this->command->info('📋 الفئات: ' . $categories->count());
        $this->command->info('✅ المهام العادية: ' . $tasks->count());
        $this->command->info('🔄 المهام المتكررة: ' . $recurringTasks->count());
    }
    
    private function createUsers()
    {
        $users = collect();
        
        // مدير النظام
        $admin = User::firstOrCreate([
            'email' => 'admin@test.com'
        ], [
            'name' => 'مدير النظام',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);
        $users->push($admin);
        
        // مستخدمين عاديين
        $userNames = [
            'أحمد محمد', 'فاطمة علي', 'محمد أحمد', 'نور الهدى',
            'خالد سعد', 'مريم حسن', 'عبدالله يوسف', 'هدى محمود'
        ];
        
        foreach ($userNames as $index => $name) {
            $user = User::firstOrCreate([
                'email' => 'user' . ($index + 1) . '@test.com'
            ], [
                'name' => $name,
                'password' => Hash::make('password'),
                'user_type' => 'volunteer',
                'email_verified_at' => now(),
            ]);
            $users->push($user);
        }
        
        return $users;
    }
