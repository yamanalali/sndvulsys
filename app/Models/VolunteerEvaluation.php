<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VolunteerEvaluation extends Model
{
    use HasFactory;
    
    protected $table = 'volunteer-evaluations';

    protected $fillable = [
        'volunteer-request_id',
        'evaluator_id',
        'evaluation_date',
        'overall_score',
        'status',
        'notes',
        'recommendation',
        'interview_score',
        'skills_assessment_score',
        'motivation_score',
        'availability_score',
        'experience_score',
        'communication_score',
        'teamwork_score',
        'reliability_score',
        'adaptability_score',
        'leadership_score',
        'technical_skills_score',
        'cultural_fit_score',
        'commitment_score'
    ];

    protected $casts = [
        'evaluation_date' => 'datetime',
        'overall_score' => 'float',
        'interview_score' => 'float',
        'skills_assessment_score' => 'float',
        'motivation_score' => 'float',
        'availability_score' => 'float',
        'experience_score' => 'float',
        'communication_score' => 'float',
        'teamwork_score' => 'float',
        'reliability_score' => 'float',
        'adaptability_score' => 'float',
        'leadership_score' => 'float',
        'technical_skills_score' => 'float',
        'cultural_fit_score' => 'float',
        'commitment_score' => 'float'
    ];

    // Evaluation Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Recommendation Constants
    const RECOMMENDATION_STRONG_APPROVE = 'strong_approve';
    const RECOMMENDATION_APPROVE = 'approve';
    const RECOMMENDATION_CONDITIONAL = 'conditional';
    const RECOMMENDATION_REJECT = 'reject';
    const RECOMMENDATION_STRONG_REJECT = 'strong_reject';

    public function volunteerRequest()
    {
        return $this->belongsTo(VolunteerRequest::class, 'volunteer-request_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function criteriaEvaluations()
    {
        return $this->hasMany(VolunteerEvaluationCriteria::class, 'evaluation_id');
    }

    /**
     * Calculate overall score based on all criteria
     */
    public function calculateOverallScore(): float
    {
        $scores = [
            $this->interview_score ?? 0,
            $this->skills_assessment_score ?? 0,
            $this->motivation_score ?? 0,
            $this->availability_score ?? 0,
            $this->experience_score ?? 0,
            $this->communication_score ?? 0,
            $this->teamwork_score ?? 0,
            $this->reliability_score ?? 0,
            $this->adaptability_score ?? 0,
            $this->leadership_score ?? 0,
            $this->technical_skills_score ?? 0,
            $this->cultural_fit_score ?? 0,
            $this->commitment_score ?? 0
        ];

        $validScores = array_filter($scores, function($score) {
            return $score > 0;
        });

        if (empty($validScores)) {
            return 0;
        }

        return round(array_sum($validScores) / count($validScores), 2);
    }

    /**
     * Get evaluation status in Arabic
     */
    public function getStatusText(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'في الانتظار',
            self::STATUS_IN_PROGRESS => 'قيد التقييم',
            self::STATUS_COMPLETED => 'مكتمل',
            self::STATUS_APPROVED => 'موافق عليه',
            self::STATUS_REJECTED => 'مرفوض',
            default => 'غير محدد'
        };
    }

    /**
     * Get recommendation text in Arabic
     */
    public function getRecommendationText(): string
    {
        return match($this->recommendation) {
            self::RECOMMENDATION_STRONG_APPROVE => 'موافقة قوية',
            self::RECOMMENDATION_APPROVE => 'موافقة',
            self::RECOMMENDATION_CONDITIONAL => 'موافقة مشروطة',
            self::RECOMMENDATION_REJECT => 'رفض',
            self::RECOMMENDATION_STRONG_REJECT => 'رفض قوي',
            default => 'غير محدد'
        };
    }

    /**
     * Get score level (Excellent, Good, Fair, Poor)
     */
    public function getScoreLevel(): string
    {
        $score = $this->overall_score ?? 0;
        
        if ($score >= 90) return 'ممتاز';
        if ($score >= 80) return 'جيد جداً';
        if ($score >= 70) return 'جيد';
        if ($score >= 60) return 'مقبول';
        return 'ضعيف';
    }

    /**
     * Check if evaluation is complete
     */
    public function isComplete(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_APPROVED, self::STATUS_REJECTED]);
    }

    /**
     * Check if volunteer is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'في الانتظار',
            self::STATUS_IN_PROGRESS => 'قيد التقييم',
            self::STATUS_COMPLETED => 'مكتمل',
            self::STATUS_APPROVED => 'موافق عليه',
            self::STATUS_REJECTED => 'مرفوض'
        ];
    }

    /**
     * Get all available recommendations
     */
    public static function getRecommendations(): array
    {
        return [
            self::RECOMMENDATION_STRONG_APPROVE => 'موافقة قوية',
            self::RECOMMENDATION_APPROVE => 'موافقة',
            self::RECOMMENDATION_CONDITIONAL => 'موافقة مشروطة',
            self::RECOMMENDATION_REJECT => 'رفض',
            self::RECOMMENDATION_STRONG_REJECT => 'رفض قوي'
        ];
    }
} 