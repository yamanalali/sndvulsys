<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'project_id',
        'description',
        'status',
        'priority',
        'category_id',
        'created_by',
        'assigned_to',
        'start_date',
        'deadline',
        'completed_at',
        'progress',
        'notes',
        'is_recurring',
        'recurrence_pattern'
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
        'completed_at' => 'datetime',
        'is_recurring' => 'boolean',
        'progress' => 'integer'
    ];

    // Task Status Constants
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Task Priority Constants
    const PRIORITY_URGENT = 'urgent';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_LOW = 'low';

    // Dependency Types
    const DEPENDENCY_FINISH_TO_START = 'finish_to_start';
    const DEPENDENCY_START_TO_START = 'start_to_start';
    const DEPENDENCY_FINISH_TO_FINISH = 'finish_to_finish';
    const DEPENDENCY_START_TO_FINISH = 'start_to_finish';

    /**
     * Get all available task statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW => 'جديدة',
            self::STATUS_IN_PROGRESS => 'قيد التنفيذ',
            self::STATUS_PENDING => 'معلقة',
            self::STATUS_COMPLETED => 'مكتملة',
            self::STATUS_CANCELLED => 'ملغاة'
        ];
    }

    /**
     * Get all available task priorities
     */
    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_URGENT => 'عاجلة',
            self::PRIORITY_HIGH => 'عالية',
            self::PRIORITY_MEDIUM => 'متوسطة',
            self::PRIORITY_LOW => 'منخفضة'
        ];
    }

    /**
     * Get all available dependency types
     */
    public static function getDependencyTypes(): array
    {
        return [
            self::DEPENDENCY_FINISH_TO_START => 'انتهاء إلى بداية',
            self::DEPENDENCY_START_TO_START => 'بداية إلى بداية',
            self::DEPENDENCY_FINISH_TO_FINISH => 'انتهاء إلى انتهاء',
            self::DEPENDENCY_START_TO_FINISH => 'بداية إلى انتهاء'
        ];
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        return $this->deadline && $this->deadline->isPast() && $this->status !== self::STATUS_COMPLETED;
    }

    /**
     * Check if task is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if task can be started (dependencies met)
     */
    public function canBeStarted(): bool
    {
        if ($this->status !== self::STATUS_NEW) {
            return false;
        }

        // Check if all dependencies are completed
        foreach ($this->dependencies as $dependency) {
            if (!$dependency->isCompleted()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Mark task as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'progress' => 100,
            'completed_at' => now()
        ]);
    }

    /**
     * Update task progress
     */
    public function updateProgress(int $progress): void
    {
        $this->update(['progress' => max(0, min(100, $progress))]);
        
        if ($this->progress >= 100) {
            $this->markAsCompleted();
        }
    }

    /**
     * Get task duration in days
     */
    public function getDurationInDays(): ?int
    {
        if (!$this->start_date || !$this->deadline) {
            return null;
        }

        return $this->start_date->diffInDays($this->deadline);
    }

    /**
     * Get remaining days until deadline
     */
    public function getRemainingDays(): ?int
    {
        if (!$this->deadline) {
            return null;
        }

        return now()->diffInDays($this->deadline, false);
    }

    /**
     * قوانين انتقال الحالات بين المهام
     *
     * @return array
     */
    public static function rulesForStatusTransitions(): array
    {
        return [
            'new' => ['in_progress', 'cancelled'],
            'in_progress' => ['pending', 'completed', 'cancelled', 'testing', 'awaiting_feedback'],
            'pending' => ['in_progress', 'completed', 'cancelled'],
            'testing' => ['in_progress', 'completed', 'cancelled'],
            'awaiting_feedback' => ['in_progress', 'completed', 'cancelled'],
            'completed' => ['archived'],
            'cancelled' => [],
            'archived' => [],
        ];
    }

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function dependencies(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'depends_on_task_id')
                    ->withPivot('dependency_type', 'is_active')
                    ->withTimestamps();
    }

    public function dependents(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'depends_on_task_id', 'task_id')
                    ->withPivot('dependency_type', 'is_active')
                    ->withTimestamps();
    }

    public function dependenciesRaw()
    {
        return $this->hasMany(TaskDependency::class, 'task_id');
    }
    public function dependentsRaw()
    {
        return $this->hasMany(TaskDependency::class, 'depends_on_task_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                    ->where('status', '!=', self::STATUS_COMPLETED);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('deadline', today())
                    ->where('status', '!=', self::STATUS_COMPLETED);
    }

    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('deadline', [now(), now()->addWeek()])
                    ->where('status', '!=', self::STATUS_COMPLETED);
    }
}
