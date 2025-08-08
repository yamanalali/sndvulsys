<?php

namespace Database\Seeders;

use App\Models\AdvancedSearch;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdvancedSearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء بيانات تجريبية للبحث المتقدم
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('لا توجد مستخدمين. سيتم إنشاء مستخدم واحد.');
            $users = User::factory(1)->create();
        }

        // إنشاء بحوث متقدمة متنوعة
        $this->createVolunteerRequestSearches($users);
        $this->createSubmissionSearches($users);
        $this->createWorkflowSearches($users);
        $this->createUserSearches($users);
        $this->createTaskSearches($users);
        $this->createProjectSearches($users);

        $this->command->info('تم إنشاء بيانات البحث المتقدم بنجاح.');
    }

    /**
     * إنشاء بحوث لطلبات التطوع
     */
    private function createVolunteerRequestSearches($users)
    {
        $searches = [
            [
                'search_term' => 'محمد',
                'filters' => ['status' => 'pending'],
                'saved_name' => 'طلبات محمد المعلقة',
                'notes' => 'البحث عن طلبات محمد التي لا تزال معلقة'
            ],
            [
                'search_term' => 'أحمد',
                'filters' => ['status' => 'approved'],
                'saved_name' => 'طلبات أحمد الموافق عليها',
                'notes' => 'جميع طلبات أحمد التي تمت الموافقة عليها'
            ],
            [
                'filters' => ['priority' => 'urgent'],
                'saved_name' => 'الطلبات العاجلة',
                'notes' => 'جميع الطلبات ذات الأولوية العاجلة'
            ],
            [
                'filters' => ['date_range' => 'this_week'],
                'saved_name' => 'طلبات هذا الأسبوع',
                'notes' => 'الطلبات المقدمة خلال هذا الأسبوع'
            ]
        ];

        foreach ($searches as $searchData) {
            AdvancedSearch::factory()
                ->volunteerRequests()
                ->saved()
                ->for($users->random())
                ->create($searchData);
        }

        // إنشاء بحوث غير محفوظة
        AdvancedSearch::factory()
            ->volunteerRequests()
            ->count(10)
            ->for($users->random())
            ->create();
    }

    /**
     * إنشاء بحوث للإرسالات
     */
    private function createSubmissionSearches($users)
    {
        $searches = [
            [
                'filters' => ['status' => 'in_review'],
                'saved_name' => 'الإرسالات قيد المراجعة',
                'notes' => 'جميع الإرسالات التي لا تزال قيد المراجعة'
            ],
            [
                'filters' => ['priority' => 'high'],
                'saved_name' => 'الإرسالات عالية الأولوية',
                'notes' => 'الإرسالات ذات الأولوية العالية'
            ],
            [
                'filters' => ['status' => 'completed'],
                'saved_name' => 'الإرسالات المكتملة',
                'notes' => 'جميع الإرسالات التي تم إكمالها'
            ]
        ];

        foreach ($searches as $searchData) {
            AdvancedSearch::factory()
                ->submissions()
                ->saved()
                ->for($users->random())
                ->create($searchData);
        }

        // إنشاء بحوث غير محفوظة
        AdvancedSearch::factory()
            ->submissions()
            ->count(8)
            ->for($users->random())
            ->create();
    }

    /**
     * إنشاء بحوث لسير المراجعة
     */
    private function createWorkflowSearches($users)
    {
        $searches = [
            [
                'filters' => ['step' => 1],
                'saved_name' => 'الخطوة الأولى',
                'notes' => 'جميع سير المراجعة في الخطوة الأولى'
            ],
            [
                'filters' => ['status' => 'completed'],
                'saved_name' => 'سير المراجعة المكتملة',
                'notes' => 'جميع سير المراجعة التي تم إكمالها'
            ]
        ];

        foreach ($searches as $searchData) {
            AdvancedSearch::factory()
                ->workflows()
                ->saved()
                ->for($users->random())
                ->create($searchData);
        }

        // إنشاء بحوث غير محفوظة
        AdvancedSearch::factory()
            ->workflows()
            ->count(6)
            ->for($users->random())
            ->create();
    }

    /**
     * إنشاء بحوث للمستخدمين
     */
    private function createUserSearches($users)
    {
        $searches = [
            [
                'search_term' => 'admin',
                'saved_name' => 'المديرين',
                'notes' => 'البحث عن جميع المديرين في النظام'
            ],
            [
                'search_term' => 'reviewer',
                'saved_name' => 'المراجعين',
                'notes' => 'جميع المراجعين في النظام'
            ]
        ];

        foreach ($searches as $searchData) {
            AdvancedSearch::factory()
                ->saved()
                ->for($users->random())
                ->create(array_merge($searchData, ['search_type' => 'users']));
        }

        // إنشاء بحوث غير محفوظة
        AdvancedSearch::factory()
            ->count(5)
            ->for($users->random())
            ->create(['search_type' => 'users']);
    }

    /**
     * إنشاء بحوث للمهام
     */
    private function createTaskSearches($users)
    {
        $searches = [
            [
                'filters' => ['status' => 'in_progress'],
                'saved_name' => 'المهام قيد التنفيذ',
                'notes' => 'جميع المهام التي لا تزال قيد التنفيذ'
            ],
            [
                'filters' => ['priority' => 'urgent'],
                'saved_name' => 'المهام العاجلة',
                'notes' => 'المهام ذات الأولوية العاجلة'
            ]
        ];

        foreach ($searches as $searchData) {
            AdvancedSearch::factory()
                ->saved()
                ->for($users->random())
                ->create(array_merge($searchData, ['search_type' => 'tasks']));
        }

        // إنشاء بحوث غير محفوظة
        AdvancedSearch::factory()
            ->count(7)
            ->for($users->random())
            ->create(['search_type' => 'tasks']);
    }

    /**
     * إنشاء بحوث للمشاريع
     */
    private function createProjectSearches($users)
    {
        $searches = [
            [
                'search_term' => 'تطوع',
                'saved_name' => 'مشاريع التطوع',
                'notes' => 'جميع المشاريع المتعلقة بالتطوع'
            ],
            [
                'filters' => ['status' => 'active'],
                'saved_name' => 'المشاريع النشطة',
                'notes' => 'جميع المشاريع النشطة حالياً'
            ]
        ];

        foreach ($searches as $searchData) {
            AdvancedSearch::factory()
                ->saved()
                ->for($users->random())
                ->create(array_merge($searchData, ['search_type' => 'projects']));
        }

        // إنشاء بحوث غير محفوظة
        AdvancedSearch::factory()
            ->count(4)
            ->for($users->random())
            ->create(['search_type' => 'projects']);
    }
} 