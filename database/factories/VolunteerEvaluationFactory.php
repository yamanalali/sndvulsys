<?php

namespace Database\Factories;

use App\Models\VolunteerEvaluation;
use App\Models\VolunteerRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VolunteerEvaluationFactory extends Factory
{
    protected $model = VolunteerEvaluation::class;

    public function definition()
    {
        return [
            'volunteer_request_id' => VolunteerRequest::factory(),
            'evaluator_id' => User::factory(),
            'evaluation_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'overall_score' => $this->faker->randomFloat(2, 50, 100),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'approved', 'rejected']),
            'notes' => $this->faker->paragraph(),
            'recommendation' => $this->faker->randomElement(['strong_approve', 'approve', 'conditional', 'reject', 'strong_reject']),
            
            // Detailed scores
            'interview_score' => $this->faker->randomFloat(2, 50, 100),
            'skills_assessment_score' => $this->faker->randomFloat(2, 50, 100),
            'motivation_score' => $this->faker->randomFloat(2, 50, 100),
            'availability_score' => $this->faker->randomFloat(2, 50, 100),
            'experience_score' => $this->faker->randomFloat(2, 50, 100),
            'communication_score' => $this->faker->randomFloat(2, 50, 100),
            'teamwork_score' => $this->faker->randomFloat(2, 50, 100),
            'reliability_score' => $this->faker->randomFloat(2, 50, 100),
            'adaptability_score' => $this->faker->randomFloat(2, 50, 100),
            'leadership_score' => $this->faker->randomFloat(2, 50, 100),
            'technical_skills_score' => $this->faker->randomFloat(2, 50, 100),
            'cultural_fit_score' => $this->faker->randomFloat(2, 50, 100),
            'commitment_score' => $this->faker->randomFloat(2, 50, 100),
        ];
    }

    /**
     * Indicate that the evaluation is approved
     */
    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'recommendation' => $this->faker->randomElement(['strong_approve', 'approve']),
                'overall_score' => $this->faker->randomFloat(2, 80, 100),
            ];
        });
    }

    /**
     * Indicate that the evaluation is rejected
     */
    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
                'recommendation' => $this->faker->randomElement(['reject', 'strong_reject']),
                'overall_score' => $this->faker->randomFloat(2, 30, 60),
            ];
        });
    }

    /**
     * Indicate that the evaluation is pending
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'recommendation' => 'conditional',
                'overall_score' => $this->faker->randomFloat(2, 60, 80),
            ];
        });
    }
} 