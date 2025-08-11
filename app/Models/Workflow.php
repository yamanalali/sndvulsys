<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    use HasFactory;

    protected $fillable = [

        'volunteer-request_id',
        'reviewed_by',
        'status',
        'reviewed_at',
        'notes',
        'step',
        'step_name',
        'is_completed',
        'next_step',
        'priority',
        'estimated_duration',
        'actual_duration',
        'assigned_to',
        'due_date'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'is_completed' => 'boolean',
        'due_date' => 'datetime',
    ];

    public function volunteerRequest()
    {
        return $this->belongsTo(VolunteerRequest::class, 'volunteer-request_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // نطاق للخطوات المكتملة
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    // نطاق للخطوات المعلقة
    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    // نطاق حسب الحالة
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // الحصول على حالات سير العمل
    public static function getStatuses()
    {
        return [
            'pending' => 'معلق',
            'in_review' => 'قيد المراجعة',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'needs_revision' => 'يحتاج مراجعة',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي'
        ];
    }

    // الحصول على خطوات سير العمل
    public static function getSteps()
    {
        return [
            1 => 'استلام الطلب',
            2 => 'مراجعة أولية',
            3 => 'تقييم المهارات',
            4 => 'مقابلة شخصية',
            5 => 'الموافقة النهائية',
            6 => 'التعيين'
        ];
    }

    // الحصول على أولويات سير العمل
    public static function getPriorities()
    {
        return [
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'عالية',
            'urgent' => 'عاجلة'
        ];
    }

    // التحقق من إمكانية الانتقال للخطوة التالية
    public function canProceedToNext()
    {
        return $this->is_completed && $this->status === 'approved';
    }

    // الحصول على الخطوة التالية
    public function getNextStepInfo()
    {
        $steps = self::getSteps();
        $nextStepNumber = $this->step + 1;
        
        return [
            'step' => $nextStepNumber,
            'name' => $steps[$nextStepNumber] ?? 'غير محدد',
            'exists' => isset($steps[$nextStepNumber])
        ];
    }

    // حساب المدة الفعلية
    public function getActualDurationAttribute()
    {
        if ($this->reviewed_at && $this->created_at) {
            return $this->created_at->diffInHours($this->reviewed_at);
        }
        return null;
    }
}