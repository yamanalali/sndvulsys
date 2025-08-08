<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer-request_id',
        'assigned_to',
        'reviewed_by',
        'status',
        'priority',
        'due_date',
        'reviewed_at',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * العلاقة مع طلب التطوع
     */
    public function volunteerRequest(): BelongsTo
    {
        return $this->belongsTo(VolunteerRequest::class, 'volunteer-request_id');
    }

    /**
     * العلاقة مع المستخدم المعين
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * العلاقة مع المراجع
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * العلاقة مع منشئ الإرسال
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع المرفقات
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(SubmissionAttachment::class, 'submission_id');
    }

    /**
     * العلاقة مع التعليقات
     */
    public function comments(): HasMany
    {
        return $this->hasMany(SubmissionComment::class, 'submission_id');
    }

    /**
     * العلاقة مع سير المراجعة
     */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'volunteer-request_id', 'volunteer-request_id');
    }

    /**
     * نطاق للإرسالات المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * نطاق للإرسالات قيد المراجعة
     */
    public function scopeInReview($query)
    {
        return $query->where('status', 'in_review');
    }

    /**
     * نطاق للإرسالات الموافق عليها
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * نطاق للإرسالات المرفوضة
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * نطاق للإرسالات المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * نطاق للإرسالات المتأخرة
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'completed');
    }

    /**
     * نطاق حسب الأولوية
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * نطاق حسب الحالة
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * الحصول على حالات الإرسال
     */
    public static function getStatuses()
    {
        return [
            'pending' => 'معلق',
            'in_review' => 'قيد المراجعة',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'needs_revision' => 'يحتاج مراجعة',
            'completed' => 'مكتمل'
        ];
    }

    /**
     * الحصول على أولويات الإرسال
     */
    public static function getPriorities()
    {
        return [
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'عالية',
            'urgent' => 'عاجلة'
        ];
    }

    /**
     * التحقق من كون الإرسال متأخر
     */
    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    /**
     * التحقق من إمكانية المراجعة
     */
    public function canBeReviewed()
    {
        return in_array($this->status, ['pending', 'in_review']);
    }

    /**
     * الحصول على الوقت المتبقي
     */
    public function getRemainingTime()
    {
        if (!$this->due_date) {
            return null;
        }

        return now()->diffForHumans($this->due_date, true);
    }

    /**
     * الحصول على مدة المراجعة
     */
    public function getReviewDuration()
    {
        if (!$this->reviewed_at || !$this->created_at) {
            return null;
        }

        return $this->created_at->diffInHours($this->reviewed_at);
    }

    /**
     * الحصول على حالة الإرسال باللغة العربية
     */
    public function getStatusTextAttribute()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status] ?? 'غير محدد';
    }

    /**
     * الحصول على الأولوية باللغة العربية
     */
    public function getPriorityTextAttribute()
    {
        $priorities = self::getPriorities();
        return $priorities[$this->priority] ?? 'غير محدد';
    }

    /**
     * الحصول على لون الحالة
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'in_review' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            'needs_revision' => 'warning',
            'completed' => 'success'
        ];

        return $colors[$this->status] ?? 'secondary';
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
} 