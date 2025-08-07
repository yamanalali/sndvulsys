<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'level', // beginner, intermediate, advanced, expert
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // علاقة مع طلبات التطوع
    public function volunteerRequests()
    {
        return $this->belongsToMany(VolunteerRequest::class, 'skill_volunteer-request', 'skill_id', 'volunteer-request_id')
                    ->withPivot('level', 'years_experience');
    }

    // علاقة مع المستخدمين
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skills')->withPivot('level', 'years_experience');
    }

    // نطاق للمهارات النشطة
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // نطاق حسب الفئة
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // الحصول على مستويات المهارة
    public static function getLevels()
    {
        return [
            'beginner' => 'مبتدئ',
            'intermediate' => 'متوسط',
            'advanced' => 'متقدم',
            'expert' => 'خبير'
        ];
    }

    // الحصول على فئات المهارات
    public static function getCategories()
    {
        return [
            'technical' => 'تقني',
            'soft_skills' => 'مهارات ناعمة',
            'language' => 'لغات',
            'management' => 'إدارة',
            'creative' => 'إبداعي',
            'other' => 'أخرى'
        ];
    }

    // الحصول على عدد المتطوعين لهذه المهارة
    public function getVolunteerCountAttribute()
    {
        return $this->volunteerRequests()->count();
    }

    // الحصول على المتطوعين النشطين فقط
    public function getActiveVolunteerCountAttribute()
    {
        return $this->volunteerRequests()->where('status', 'approved')->count();
    }
}


