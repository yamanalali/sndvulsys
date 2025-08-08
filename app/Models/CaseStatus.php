<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer-request_id',
        'status',
        'assigned_to',
        'updated_by',
        'notes',
        'due_date',
        'priority'
    ];

    protected $casts = [
        'due_date' => 'datetime',
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
     * العلاقة مع المستخدم الذي قام بالتحديث
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * العلاقة مع الملاحظات
     */
    public function notes(): HasMany
    {
        return $this->hasMany(CaseNote::class, 'case_status_id');
    }

    /**
     * نطاق للحالات المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * نطاق للحالات قيد التقدم
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * نطاق للحالات المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * نطاق للحالات الملغية
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * نطاق للحالات المتأخرة
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
     * الحصول على حالات الحالات
     */
    public static function getStatuses()
    {
        return [
            'pending' => 'معلق',
            'in_progress' => 'قيد التقدم',
            'under_review' => 'قيد المراجعة',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'needs_revision' => 'يحتاج مراجعة',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي'
        ];
    }

    /**
     * الحصول على أولويات الحالات
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
     * التحقق من كون الحالة متأخرة
     */
    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    /**
     * التحقق من إمكانية التحديث
     */
    public function canBeUpdated()
    {
        return !in_array($this->status, ['completed', 'cancelled']);
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
     * الحصول على حالة الحالة باللغة العربية
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
            'in_progress' => 'info',
            'under_review' => 'primary',
            'approved' => 'success',
            'rejected' => 'danger',
            'needs_revision' => 'warning',
            'completed' => 'success',
            'cancelled' => 'secondary'
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