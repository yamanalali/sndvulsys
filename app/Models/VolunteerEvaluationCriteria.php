<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VolunteerEvaluationCriteria extends Model
{
    use HasFactory;
    
    protected $table = 'volunteer-evaluation_criteria';

    protected $fillable = [
        'evaluation_id',
        'criteria_name',
        'criteria_description',
        'score',
        'max_score',
        'weight',
        'comments',
        'evaluated_at'
    ];

    protected $casts = [
        'score' => 'float',
        'max_score' => 'float',
        'weight' => 'float',
        'evaluated_at' => 'datetime'
    ];

    // Criteria Types
    const CRITERIA_INTERVIEW = 'interview';
    const CRITERIA_SKILLS_ASSESSMENT = 'skills_assessment';
    const CRITERIA_MOTIVATION = 'motivation';
    const CRITERIA_AVAILABILITY = 'availability';
    const CRITERIA_EXPERIENCE = 'experience';
    const CRITERIA_COMMUNICATION = 'communication';
    const CRITERIA_TEAMWORK = 'teamwork';
    const CRITERIA_RELIABILITY = 'reliability';
    const CRITERIA_ADAPTABILITY = 'adaptability';
    const CRITERIA_LEADERSHIP = 'leadership';
    const CRITERIA_TECHNICAL_SKILLS = 'technical_skills';
    const CRITERIA_CULTURAL_FIT = 'cultural_fit';
    const CRITERIA_COMMITMENT = 'commitment';

    public function evaluation()
    {
        return $this->belongsTo(VolunteerEvaluation::class, 'evaluation_id');
    }

    /**
     * Get criteria name in Arabic
     */
    public function getCriteriaNameText(): string
    {
        return match($this->criteria_name) {
            self::CRITERIA_INTERVIEW => 'المقابلة الشخصية',
            self::CRITERIA_SKILLS_ASSESSMENT => 'تقييم المهارات',
            self::CRITERIA_MOTIVATION => 'الدافع والتحفيز',
            self::CRITERIA_AVAILABILITY => 'التوفر والمرونة',
            self::CRITERIA_EXPERIENCE => 'الخبرة السابقة',
            self::CRITERIA_COMMUNICATION => 'مهارات التواصل',
            self::CRITERIA_TEAMWORK => 'العمل الجماعي',
            self::CRITERIA_RELIABILITY => 'الموثوقية',
            self::CRITERIA_ADAPTABILITY => 'القدرة على التكيف',
            self::CRITERIA_LEADERSHIP => 'مهارات القيادة',
            self::CRITERIA_TECHNICAL_SKILLS => 'المهارات التقنية',
            self::CRITERIA_CULTURAL_FIT => 'الملاءمة الثقافية',
            self::CRITERIA_COMMITMENT => 'الالتزام',
            default => $this->criteria_name
        };
    }

    /**
     * Get criteria description in Arabic
     */
    public function getCriteriaDescriptionText(): string
    {
        return match($this->criteria_name) {
            self::CRITERIA_INTERVIEW => 'تقييم الأداء في المقابلة الشخصية والانطباع العام',
            self::CRITERIA_SKILLS_ASSESSMENT => 'تقييم المهارات المطلوبة للمنصب التطوعي',
            self::CRITERIA_MOTIVATION => 'تقييم الدافع الشخصي والرغبة في التطوع',
            self::CRITERIA_AVAILABILITY => 'تقييم مدى التوفر والمرونة في الوقت',
            self::CRITERIA_EXPERIENCE => 'تقييم الخبرات السابقة ذات الصلة',
            self::CRITERIA_COMMUNICATION => 'تقييم مهارات التواصل الشفهي والكتابي',
            self::CRITERIA_TEAMWORK => 'تقييم القدرة على العمل ضمن فريق',
            self::CRITERIA_RELIABILITY => 'تقييم الموثوقية والالتزام بالمواعيد',
            self::CRITERIA_ADAPTABILITY => 'تقييم القدرة على التكيف مع التغييرات',
            self::CRITERIA_LEADERSHIP => 'تقييم مهارات القيادة وإدارة المجموعات',
            self::CRITERIA_TECHNICAL_SKILLS => 'تقييم المهارات التقنية المطلوبة',
            self::CRITERIA_CULTURAL_FIT => 'تقييم الملاءمة مع ثقافة المنظمة',
            self::CRITERIA_COMMITMENT => 'تقييم مستوى الالتزام والاستمرارية',
            default => $this->criteria_description
        };
    }

    /**
     * Calculate weighted score
     */
    public function getWeightedScore(): float
    {
        if ($this->max_score <= 0) {
            return 0;
        }
        
        $percentage = ($this->score / $this->max_score) * 100;
        return round($percentage * ($this->weight / 100), 2);
    }

    /**
     * Get score percentage
     */
    public function getScorePercentage(): float
    {
        if ($this->max_score <= 0) {
            return 0;
        }
        
        return round(($this->score / $this->max_score) * 100, 2);
    }

    /**
     * Get score level
     */
    public function getScoreLevel(): string
    {
        $percentage = $this->getScorePercentage();
        
        if ($percentage >= 90) return 'ممتاز';
        if ($percentage >= 80) return 'جيد جداً';
        if ($percentage >= 70) return 'جيد';
        if ($percentage >= 60) return 'مقبول';
        return 'ضعيف';
    }

    /**
     * Get all available criteria
     */
    public static function getAvailableCriteria(): array
    {
        return [
            self::CRITERIA_INTERVIEW => [
                'name' => 'المقابلة الشخصية',
                'description' => 'تقييم الأداء في المقابلة الشخصية والانطباع العام',
                'max_score' => 100,
                'weight' => 15
            ],
            self::CRITERIA_SKILLS_ASSESSMENT => [
                'name' => 'تقييم المهارات',
                'description' => 'تقييم المهارات المطلوبة للمنصب التطوعي',
                'max_score' => 100,
                'weight' => 20
            ],
            self::CRITERIA_MOTIVATION => [
                'name' => 'الدافع والتحفيز',
                'description' => 'تقييم الدافع الشخصي والرغبة في التطوع',
                'max_score' => 100,
                'weight' => 15
            ],
            self::CRITERIA_AVAILABILITY => [
                'name' => 'التوفر والمرونة',
                'description' => 'تقييم مدى التوفر والمرونة في الوقت',
                'max_score' => 100,
                'weight' => 10
            ],
            self::CRITERIA_EXPERIENCE => [
                'name' => 'الخبرة السابقة',
                'description' => 'تقييم الخبرات السابقة ذات الصلة',
                'max_score' => 100,
                'weight' => 10
            ],
            self::CRITERIA_COMMUNICATION => [
                'name' => 'مهارات التواصل',
                'description' => 'تقييم مهارات التواصل الشفهي والكتابي',
                'max_score' => 100,
                'weight' => 10
            ],
            self::CRITERIA_TEAMWORK => [
                'name' => 'العمل الجماعي',
                'description' => 'تقييم القدرة على العمل ضمن فريق',
                'max_score' => 100,
                'weight' => 8
            ],
            self::CRITERIA_RELIABILITY => [
                'name' => 'الموثوقية',
                'description' => 'تقييم الموثوقية والالتزام بالمواعيد',
                'max_score' => 100,
                'weight' => 8
            ],
            self::CRITERIA_ADAPTABILITY => [
                'name' => 'القدرة على التكيف',
                'description' => 'تقييم القدرة على التكيف مع التغييرات',
                'max_score' => 100,
                'weight' => 5
            ],
            self::CRITERIA_LEADERSHIP => [
                'name' => 'مهارات القيادة',
                'description' => 'تقييم مهارات القيادة وإدارة المجموعات',
                'max_score' => 100,
                'weight' => 5
            ],
            self::CRITERIA_TECHNICAL_SKILLS => [
                'name' => 'المهارات التقنية',
                'description' => 'تقييم المهارات التقنية المطلوبة',
                'max_score' => 100,
                'weight' => 8
            ],
            self::CRITERIA_CULTURAL_FIT => [
                'name' => 'الملاءمة الثقافية',
                'description' => 'تقييم الملاءمة مع ثقافة المنظمة',
                'max_score' => 100,
                'weight' => 6
            ],
            self::CRITERIA_COMMITMENT => [
                'name' => 'الالتزام',
                'description' => 'تقييم مستوى الالتزام والاستمرارية',
                'max_score' => 100,
                'weight' => 10
            ]
        ];
    }
} 