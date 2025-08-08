<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringTaskException extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_task_id',
        'exception_date',
        'exception_type',
        'new_date',
        'modified_data',
        'reason',
        'created_by'
    ];

    protected $casts = [
        'exception_date' => 'date',
        'new_date' => 'date',
        'modified_data' => 'array'
    ];

    // Exception type constants
    const TYPE_SKIP = 'skip';
    const TYPE_RESCHEDULE = 'reschedule';
    const TYPE_MODIFY = 'modify';

    /**
     * Get all available exception types
     */
    public static function getExceptionTypes(): array
    {
        return [
            self::TYPE_SKIP => 'تخطي هذا التكرار',
            self::TYPE_RESCHEDULE => 'إعادة جدولة',
            self::TYPE_MODIFY => 'تعديل المهمة'
        ];
    }

    /**
     * Get the parent recurring task
     */
    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    /**
     * Get the user who created this exception
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if this exception skips the occurrence
     */
    public function isSkip(): bool
    {
        return $this->exception_type === self::TYPE_SKIP;
    }

    /**
     * Check if this exception reschedules the occurrence
     */
    public function isReschedule(): bool
    {
        return $this->exception_type === self::TYPE_RESCHEDULE;
    }

    /**
     * Check if this exception modifies the task
     */
    public function isModify(): bool
    {
        return $this->exception_type === self::TYPE_MODIFY;
    }

    /**
     * Get the effective date for this exception
     */
    public function getEffectiveDate(): ?\Carbon\Carbon
    {
        if ($this->isReschedule() && $this->new_date) {
            return $this->new_date;
        }
        
        return $this->exception_date;
    }

    /**
     * Get the exception type label
     */
    public function getExceptionTypeLabelAttribute(): string
    {
        return self::getExceptionTypes()[$this->exception_type] ?? $this->exception_type;
    }
}