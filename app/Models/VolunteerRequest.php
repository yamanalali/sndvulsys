<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerRequest extends Model
{
    use HasFactory;
    
    protected $table = 'volunteer-requests';
    
    protected $casts = [
        'birth_date' => 'date',
        'reviewed_at' => 'datetime',
        'due_date' => 'datetime',
    ];
    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'national_id',
        'birth_date',
        'gender',
        'social_status',
        'address',
        'city',
        'country',
        'education_level',
        'field_of_study',
        'occupation',
        'skills',
        'languages',
        'motivation',
        'previous_experience',
        'preferred_area',
        'availability',
        'has_previous_volunteering',
        'preferred_organization_type',
        'cv',
      
        'status',
        'priority',
        'due_date',
        'assigned_to',
        'reviewed_at',
        'reviewed_by',
        'admin_notes',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function skills()
{
    return $this->belongsToMany(Skill::class, 'skill_volunteer-request', 'volunteer-request_id', 'skill_id')
                ->withPivot('level', 'years_experience');
}
public function availabilities()
{
    return $this->hasMany(Availability::class, 'volunteer-request_id');
}
public function workflows()
{
    return $this->hasMany(Workflow::class, 'volunteer-request_id');
}

public function previousExperiences()
{
    return $this->hasMany(PreviousExperience::class, 'volunteer-request_id');
}

public function evaluations()
{
    return $this->hasMany(VolunteerEvaluation::class, 'volunteer-request_id');
}

public function submissions()
{
    return $this->hasMany(Submission::class, 'volunteer-request_id');
}

public function approvalDecision()
{
    return $this->hasOne(ApprovalDecision::class, 'volunteer-request_id');
}

public function caseStatuses()
{
    return $this->hasMany(CaseStatus::class, 'volunteer-request_id');
}

public function notes()
{
    return $this->hasMany(CaseNote::class, 'volunteer-request_id');
}

public function assignedTo()
{
    return $this->belongsTo(User::class, 'assigned_to');
}

// نطاق للطلبات المعلقة
public function scopePending($query)
{
    return $query->where('status', 'pending');
}

// نطاق للطلبات الموافق عليها
public function scopeApproved($query)
{
    return $query->where('status', 'approved');
}

// نطاق للطلبات المرفوضة
public function scopeRejected($query)
{
    return $query->where('status', 'rejected');
}

// نطاق للطلبات المتأخرة
public function scopeOverdue($query)
{
    return $query->where('due_date', '<', now())
                ->where('status', '!=', 'approved');
}

// نطاق حسب الأولوية
public function scopeByPriority($query, $priority)
{
    return $query->where('priority', $priority);
}

/**
 * التحقق من كون الطلب متأخر
 */
public function isOverdue()
{
    return $this->due_date && $this->due_date->isPast() && $this->status !== 'approved';
}

/**
 * الحصول على الأولوية باللغة العربية
 */
public function getPriorityTextAttribute()
{
    $priorities = [
        'low' => 'منخفضة',
        'medium' => 'متوسطة',
        'high' => 'عالية',
        'urgent' => 'عاجلة'
    ];
    
    return $priorities[$this->priority] ?? 'غير محدد';
}

/**
 * الحصول على لون الأولوية
 */
public function getPriorityColorAttribute()
{
    $colors = [
        'low' => 'success',
        'medium' => 'info',
        'high' => 'warning',
        'urgent' => 'danger'
    ];
    
    return $colors[$this->priority] ?? 'secondary';
}

public function latestEvaluation()
{
    return $this->hasOne(VolunteerEvaluation::class, 'volunteer-request_id')->latest();
}

public function approvedEvaluation()
{
    return $this->hasOne(VolunteerEvaluation::class, 'volunteer-request_id')->where('status', VolunteerEvaluation::STATUS_APPROVED);
}

/**
 * Get overall evaluation score
 */
public function getOverallEvaluationScore(): float
{
    $evaluation = $this->latestEvaluation;
    return $evaluation ? $evaluation->overall_score : 0;
}

/**
 * Check if volunteer request has been evaluated
 */
public function hasEvaluation(): bool
{
    return $this->evaluations()->exists();
}

/**
 * Check if volunteer request is approved
 */
public function isApproved(): bool
{
    return $this->evaluations()->where('status', VolunteerEvaluation::STATUS_APPROVED)->exists();
}

/**
 * Get evaluation status
 */
public function getEvaluationStatus(): string
{
    $evaluation = $this->latestEvaluation;
    return $evaluation ? $evaluation->getStatusText() : 'لم يتم التقييم';
}

/**
 * الحصول على حالة الطلب باللغة العربية
 */
public function getStatusTextAttribute(): string
{
    $statuses = [
        'pending' => 'معلق',
        'in_progress' => 'قيد التقدم',
        'under_review' => 'قيد المراجعة',
        'approved' => 'موافق عليه',
        'rejected' => 'مرفوض',
        'needs_revision' => 'يحتاج مراجعة',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغي',
        'withdrawn' => 'مسحوب'
    ];
    
    return $statuses[$this->status] ?? 'غير محدد';
}

/**
 * الحصول على لون الحالة
 */
public function getStatusColorAttribute(): string
{
    $colors = [
        'pending' => 'warning',
        'in_progress' => 'info',
        'under_review' => 'primary',
        'approved' => 'success',
        'rejected' => 'danger',
        'needs_revision' => 'warning',
        'completed' => 'success',
        'cancelled' => 'secondary',
        'withdrawn' => 'dark'
    ];
    
    return $colors[$this->status] ?? 'secondary';
}



}

