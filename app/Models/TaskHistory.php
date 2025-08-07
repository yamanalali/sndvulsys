<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TaskHistory extends Model
{
    use HasFactory;

    protected $table = 'task_history';

    protected $fillable = [
        'task_id',
        'user_id',
        'action_type',
        'field_name',
        'old_value',
        'new_value',
        'description',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Action Types
    const ACTION_CREATED = 'created';
    const ACTION_UPDATED = 'updated';
    const ACTION_STATUS_CHANGED = 'status_changed';
    const ACTION_PRIORITY_CHANGED = 'priority_changed';
    const ACTION_ASSIGNED = 'assigned';
    const ACTION_UNASSIGNED = 'unassigned';
    const ACTION_COMPLETED = 'completed';
    const ACTION_PROGRESS_UPDATED = 'progress_updated';
    const ACTION_DEADLINE_CHANGED = 'deadline_changed';
    const ACTION_ARCHIVED = 'archived';
    const ACTION_RESTORED = 'restored';

    /**
     * Get the task that owns the history record
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who made the change
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted action description
     */
    public function getActionDescriptionAttribute(): string
    {
        $descriptions = [
            self::ACTION_CREATED => 'تم إنشاء المهمة',
            self::ACTION_UPDATED => 'تم تحديث المهمة',
            self::ACTION_STATUS_CHANGED => 'تم تغيير حالة المهمة',
            self::ACTION_PRIORITY_CHANGED => 'تم تغيير أولوية المهمة',
            self::ACTION_ASSIGNED => 'تم تعيين المهمة',
            self::ACTION_UNASSIGNED => 'تم إلغاء تعيين المهمة',
            self::ACTION_COMPLETED => 'تم إنجاز المهمة',
            self::ACTION_PROGRESS_UPDATED => 'تم تحديث التقدم',
            self::ACTION_DEADLINE_CHANGED => 'تم تغيير الموعد النهائي',
            self::ACTION_ARCHIVED => 'تم أرشفة المهمة',
            self::ACTION_RESTORED => 'تم استعادة المهمة',
        ];

        return $descriptions[$this->action_type] ?? 'تم تحديث المهمة';
    }

    /**
     * Get formatted field name
     */
    public function getFieldNameAttribute($value): string
    {
        $fieldNames = [
            'title' => 'العنوان',
            'description' => 'الوصف',
            'status' => 'الحالة',
            'priority' => 'الأولوية',
            'deadline' => 'الموعد النهائي',
            'start_date' => 'تاريخ البدء',
            'progress' => 'التقدم',
            'notes' => 'الملاحظات',
            'assigned_to' => 'المكلف',
        ];

        return $fieldNames[$value] ?? $value;
    }

    /**
     * Get formatted old value
     */
    public function getFormattedOldValueAttribute(): string
    {
        if ($this->field_name === 'status') {
            $statusLabels = [
                'new' => 'جديدة',
                'in_progress' => 'قيد التنفيذ',
                'pending' => 'معلقة',
                'completed' => 'منجزة',
                'cancelled' => 'ملغاة',
            ];
            return $statusLabels[$this->old_value] ?? $this->old_value;
        }

        if ($this->field_name === 'priority') {
            $priorityLabels = [
                'urgent' => 'عاجلة',
                'high' => 'عالية',
                'medium' => 'متوسطة',
                'low' => 'منخفضة',
            ];
            return $priorityLabels[$this->old_value] ?? $this->old_value;
        }

        if ($this->field_name === 'deadline' || $this->field_name === 'start_date') {
            return $this->old_value ? Carbon::parse($this->old_value)->format('Y-m-d') : 'غير محدد';
        }

        if ($this->field_name === 'progress') {
            return $this->old_value ? $this->old_value . '%' : '0%';
        }

        return $this->old_value ?? 'غير محدد';
    }

    /**
     * Get formatted new value
     */
    public function getFormattedNewValueAttribute(): string
    {
        if ($this->field_name === 'status') {
            $statusLabels = [
                'new' => 'جديدة',
                'in_progress' => 'قيد التنفيذ',
                'pending' => 'معلقة',
                'completed' => 'منجزة',
                'cancelled' => 'ملغاة',
            ];
            return $statusLabels[$this->new_value] ?? $this->new_value;
        }

        if ($this->field_name === 'priority') {
            $priorityLabels = [
                'urgent' => 'عاجلة',
                'high' => 'عالية',
                'medium' => 'متوسطة',
                'low' => 'منخفضة',
            ];
            return $priorityLabels[$this->new_value] ?? $this->new_value;
        }

        if ($this->field_name === 'deadline' || $this->field_name === 'start_date') {
            return $this->new_value ? Carbon::parse($this->new_value)->format('Y-m-d') : 'غير محدد';
        }

        if ($this->field_name === 'progress') {
            return $this->new_value ? $this->new_value . '%' : '0%';
        }

        return $this->new_value ?? 'غير محدد';
    }

    /**
     * Get time ago
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get icon for action type
     */
    public function getActionIconAttribute(): string
    {
        $icons = [
            self::ACTION_CREATED => 'fas fa-plus-circle',
            self::ACTION_UPDATED => 'fas fa-edit',
            self::ACTION_STATUS_CHANGED => 'fas fa-exchange-alt',
            self::ACTION_PRIORITY_CHANGED => 'fas fa-flag',
            self::ACTION_ASSIGNED => 'fas fa-user-plus',
            self::ACTION_UNASSIGNED => 'fas fa-user-minus',
            self::ACTION_COMPLETED => 'fas fa-check-circle',
            self::ACTION_PROGRESS_UPDATED => 'fas fa-chart-line',
            self::ACTION_DEADLINE_CHANGED => 'fas fa-calendar-alt',
            self::ACTION_ARCHIVED => 'fas fa-archive',
            self::ACTION_RESTORED => 'fas fa-undo',
        ];

        return $icons[$this->action_type] ?? 'fas fa-info-circle';
    }

    /**
     * Get color for action type
     */
    public function getActionColorAttribute(): string
    {
        $colors = [
            self::ACTION_CREATED => 'success',
            self::ACTION_UPDATED => 'info',
            self::ACTION_STATUS_CHANGED => 'warning',
            self::ACTION_PRIORITY_CHANGED => 'primary',
            self::ACTION_ASSIGNED => 'success',
            self::ACTION_UNASSIGNED => 'danger',
            self::ACTION_COMPLETED => 'success',
            self::ACTION_PROGRESS_UPDATED => 'info',
            self::ACTION_DEADLINE_CHANGED => 'warning',
            self::ACTION_ARCHIVED => 'secondary',
            self::ACTION_RESTORED => 'info',
        ];

        return $colors[$this->action_type] ?? 'secondary';
    }
} 