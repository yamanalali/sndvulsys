<?php

namespace Database\Seeders;

use App\Models\VolunteerEvaluation;
use App\Models\VolunteerRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class VolunteerEvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مستخدمين للمقيمين
        $evaluators = User::factory(5)->create();

        // الحصول على طلبات التطوع الموجودة
        $volunteerRequests = VolunteerRequest::all();

        if ($volunteerRequests->isEmpty()) {
            $this->command->info('لا توجد طلبات تطوع متاحة. يرجى تشغيل VolunteerRequestSeeder أولاً.');
            return;
        }

        // إنشاء تقييمات متنوعة
        foreach ($volunteerRequests as $request) {
            // تقييمات موافق عليها (30%)
            if (rand(1, 100) <= 30) {
                VolunteerEvaluation::factory()
                    ->approved()
                    ->create([
                        'volunteer_request_id' => $request->id,
                        'evaluator_id' => $evaluators->random()->id,
                    ]);
            }
            // تقييمات مرفوضة (20%)
            elseif (rand(1, 100) <= 20) {
                VolunteerEvaluation::factory()
                    ->rejected()
                    ->create([
                        'volunteer_request_id' => $request->id,
                        'evaluator_id' => $evaluators->random()->id,
                    ]);
            }
            // تقييمات معلقة (20%)
            elseif (rand(1, 100) <= 20) {
                VolunteerEvaluation::factory()
                    ->pending()
                    ->create([
                        'volunteer_request_id' => $request->id,
                        'evaluator_id' => $evaluators->random()->id,
                    ]);
            }
            // باقي الطلبات بدون تقييم (30%)
        }

        $this->command->info('تم إنشاء تقييمات المتطوعين بنجاح!');
    }
} 