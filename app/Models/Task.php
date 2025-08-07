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
        'recurrence_pattern',
        'recurrence_config',
        'recurrence_start_date',
        'recurrence_end_date',
        'recurrence_max_occurrences',
        'recurrence_current_count',
        'parent_task_id',
        'is_recurring_instance',
        'next_occurrence_date',
        'recurring_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
        'completed_at' => 'datetime',
        'is_recurring' => 'boolean',
        'is_recurring_instance' => 'boolean',
        'recurring_active' => 'boolean',
        'recurrence_config' => 'array',
        'recurrence_start_date' => 'date',
        'recurrence_end_date' => 'date',
        'next_occurrence_date' => 'datetime',
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

    // Recurrence Pattern Constants
    const RECURRENCE_DAILY = 'daily';
    const RECURRENCE_WEEKLY = 'weekly';
    const RECURRENCE_MONTHLY = 'monthly';
    const RECURRENCE_YEARLY = 'yearly';
    const RECURRENCE_CUSTOM = 'custom';

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
     * Get all available recurrence patterns
     */
    public static function getRecurrencePatterns(): array
    {
        return [
            self::RECURRENCE_DAILY => 'يومياً',
            self::RECURRENCE_WEEKLY => 'أسبوعياً',
            self::RECURRENCE_MONTHLY => 'شهرياً',
            self::RECURRENCE_YEARLY => 'سنوياً',
            self::RECURRENCE_CUSTOM => 'مخصص'
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
        return $this->hasMany(Assignment::class)->with('user');
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

    public function taskDependencies()
    {
        return $this->hasMany(TaskDependency::class, 'task_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to')->withDefault();
    }

    /**
     * Get task history records
     */
    public function history(): HasMany
    {
        return $this->hasMany(TaskHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get recent history (last 10 records)
     */
    public function recentHistory(): HasMany
    {
        return $this->hasMany(TaskHistory::class)->orderBy('created_at', 'desc')->limit(10);
    }

    /**
     * Record a history entry
     */
    public function recordHistory(string $actionType, string $fieldName = null, $oldValue = null, $newValue = null, string $description = null, array $metadata = []): void
    {
        $this->history()->create([
            'user_id' => auth()->id(),
            'action_type' => $actionType,
            'field_name' => $fieldName,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'description' => $description,
            'metadata' => $metadata
        ]);
    }

    /**
     * Check if task is archived
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Archive the task
     */
    public function archive(): void
    {
        $oldStatus = $this->status;
        $this->update(['status' => 'archived']);
        
        $this->recordHistory(
            TaskHistory::ACTION_ARCHIVED,
            'status',
            $oldStatus,
            'archived',
            'تم أرشفة المهمة'
        );
    }

    /**
     * Restore the task from archive
     */
    public function restore(): void
    {
        $oldStatus = $this->status;
        $this->update(['status' => 'completed']);
        
        $this->recordHistory(
            TaskHistory::ACTION_RESTORED,
            'status',
            $oldStatus,
            'completed',
            'تم استعادة المهمة من الأرشيف'
        );
    }

    /**
     * الحصول على المكلف الرئيسي (أول مكلف)
     */
    public function getMainAssignee()
    {
        return $this->assignments()->with('user')->first()?->user;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the parent recurring task (if this is an instance)
     */
    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    /**
     * Get all recurring instances of this task
     */
    public function recurringInstances(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id')->orderBy('start_date');
    }

    /**
     * Get recurring task exceptions
     */
    public function recurringExceptions(): HasMany
    {
        return $this->hasMany(RecurringTaskException::class, 'parent_task_id');
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

    // Accessors for status and priority labels and colors
    public function getStatusLabelAttribute()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'new' => 'primary',
            'in_progress' => 'info',
            'pending' => 'warning',
            'completed' => 'success',
            'cancelled' => 'secondary',
        ];
        
        return $colors[$this->status] ?? 'primary';
    }

    public function getPriorityLabelAttribute()
    {
        return self::getPriorities()[$this->priority] ?? $this->priority;
    }

    public function getPriorityColorAttribute()
    {
        $colors = [
            'urgent' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'success',
        ];
        
        return $colors[$this->priority] ?? 'primary';
    }

    /**
     * الحصول على أيقونة الأولوية
     */
    public function getPriorityIconAttribute()
    {
        $icons = [
            'urgent' => 'feather-alert-triangle',
            'high' => 'feather-flag',
            'medium' => 'feather-clock',
            'low' => 'feather-check-circle',
        ];
        
        return $icons[$this->priority] ?? 'feather-circle';
    }

    /**
     * الحصول على لون الأولوية المخصص
     */
    public function getPriorityCustomColorAttribute()
    {
        $colors = [
            'urgent' => '#dc3545',
            'high' => '#fd7e14',
            'medium' => '#17a2b8',
            'low' => '#28a745',
        ];
        
        return $colors[$this->priority] ?? '#6c757d';
    }

    /**
     * التحقق من وجود تذكيرات للمهمة
     */
    public function hasReminders(): bool
    {
        return $this->deadline && $this->deadline->isFuture() && $this->status !== self::STATUS_COMPLETED;
    }

    /**
     * الحصول على مستوى التذكير
     */
    public function getReminderLevelAttribute()
    {
        if (!$this->deadline || $this->status === self::STATUS_COMPLETED) {
            return 'none';
        }

        $daysUntilDeadline = $this->getRemainingDays();

        if ($daysUntilDeadline < 0) {
            return 'overdue';
        } elseif ($daysUntilDeadline <= 1) {
            return 'critical';
        } elseif ($daysUntilDeadline <= 3) {
            return 'urgent';
        } elseif ($daysUntilDeadline <= 7) {
            return 'warning';
        } else {
            return 'normal';
        }
    }

    /**
     * الحصول على لون التذكير
     */
    public function getReminderColorAttribute()
    {
        $colors = [
            'overdue' => '#dc3545',
            'critical' => '#dc3545',
            'urgent' => '#fd7e14',
            'warning' => '#ffc107',
            'normal' => '#28a745',
            'none' => '#6c757d',
        ];
        
        return $colors[$this->reminder_level] ?? '#6c757d';
    }

    /**
     * الحصول على أيقونة التذكير
     */
    public function getReminderIconAttribute()
    {
        $icons = [
            'overdue' => 'feather-alert-octagon',
            'critical' => 'feather-alert-triangle',
            'urgent' => 'feather-clock',
            'warning' => 'feather-bell',
            'normal' => 'feather-check',
            'none' => 'feather-circle',
        ];
        
        return $icons[$this->reminder_level] ?? 'feather-circle';
    }

    /**
     * التحقق من إمكانية الإنجاز
     */
    public function canBeCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_NEW, self::STATUS_IN_PROGRESS, self::STATUS_PENDING]);
    }

    /**
     * الحصول على نسبة الإنجاز المئوية
     */
    public function getCompletionPercentageAttribute()
    {
        return $this->progress ?? 0;
    }

    /**
     * الحصول على حالة الإنجاز
     */
    public function getCompletionStatusAttribute()
    {
        if ($this->status === self::STATUS_COMPLETED) {
            return 'completed';
        } elseif ($this->progress >= 90) {
            return 'near_completion';
        } elseif ($this->progress >= 50) {
            return 'half_completed';
        } elseif ($this->progress > 0) {
            return 'started';
        } else {
            return 'not_started';
        }
    }

    /**
     * الحصول على لون حالة الإنجاز
     */
    public function getCompletionColorAttribute()
    {
        $colors = [
            'completed' => '#28a745',
            'near_completion' => '#20c997',
            'half_completed' => '#17a2b8',
            'started' => '#ffc107',
            'not_started' => '#6c757d',
        ];
        
        return $colors[$this->completion_status] ?? '#6c757d';
    }

    // Recurring Task Methods

    /**
     * Check if this task is a recurring master
     */
    public function isRecurringMaster(): bool
    {
        return $this->is_recurring && !$this->is_recurring_instance && $this->parent_task_id === null;
    }

    /**
     * Check if this task is a recurring instance
     */
    public function isRecurringInstance(): bool
    {
        return $this->is_recurring_instance && $this->parent_task_id !== null;
    }

    /**
     * Check if recurring schedule is active
     */
    public function isRecurringActive(): bool
    {
        return $this->recurring_active && $this->isRecurringMaster();
    }

    /**
     * Check if recurring schedule should continue
     */
    public function shouldContinueRecurring(): bool
    {
        if (!$this->isRecurringActive()) {
            return false;
        }

        // Check end date
        if ($this->recurrence_end_date && now()->greaterThan($this->recurrence_end_date)) {
            return false;
        }

        // Check max occurrences
        if ($this->recurrence_max_occurrences && $this->recurrence_current_count >= $this->recurrence_max_occurrences) {
            return false;
        }

        return true;
    }

    /**
     * Get default recurrence configuration
     */
    public function getDefaultRecurrenceConfig(): array
    {
        $configs = [
            self::RECURRENCE_DAILY => [
                'interval' => 1,
                'days_of_week' => null,
                'day_of_month' => null,
                'month_of_year' => null
            ],
            self::RECURRENCE_WEEKLY => [
                'interval' => 1,
                'days_of_week' => [1], // Monday
                'day_of_month' => null,
                'month_of_year' => null
            ],
            self::RECURRENCE_MONTHLY => [
                'interval' => 1,
                'days_of_week' => null,
                'day_of_month' => 1,
                'month_of_year' => null
            ],
            self::RECURRENCE_YEARLY => [
                'interval' => 1,
                'days_of_week' => null,
                'day_of_month' => 1,
                'month_of_year' => 1
            ]
        ];

        return $configs[$this->recurrence_pattern] ?? $configs[self::RECURRENCE_DAILY];
    }

    /**
     * Get merged recurrence configuration
     */
    public function getRecurrenceConfig(): array
    {
        $default = $this->getDefaultRecurrenceConfig();
        $custom = $this->recurrence_config ?? [];
        
        return array_merge($default, $custom);
    }

    /**
     * Calculate next occurrence date based on recurrence pattern
     */
    public function calculateNextOccurrence(\Carbon\Carbon $fromDate = null): ?\Carbon\Carbon
    {
        if (!$this->is_recurring || !$this->recurring_active) {
            return null;
        }

        $fromDate = $fromDate ?? $this->next_occurrence_date ?? $this->recurrence_start_date ?? $this->start_date ?? now();
        $config = $this->getRecurrenceConfig();
        $interval = $config['interval'] ?? 1;

        switch ($this->recurrence_pattern) {
            case self::RECURRENCE_DAILY:
                return $fromDate->copy()->addDays($interval);

            case self::RECURRENCE_WEEKLY:
                $nextDate = $fromDate->copy()->addWeeks($interval);
                if (!empty($config['days_of_week'])) {
                    // Find next occurrence based on days of week
                    $daysOfWeek = $config['days_of_week'];
                    $currentDayOfWeek = $nextDate->dayOfWeek;
                    
                    foreach ($daysOfWeek as $dayOfWeek) {
                        if ($dayOfWeek >= $currentDayOfWeek) {
                            return $nextDate->startOfWeek()->addDays($dayOfWeek);
                        }
                    }
                    
                    // If no day found this week, go to next week
                    return $nextDate->addWeek()->startOfWeek()->addDays($daysOfWeek[0]);
                }
                return $nextDate;

            case self::RECURRENCE_MONTHLY:
                $nextDate = $fromDate->copy()->addMonths($interval);
                if ($config['day_of_month']) {
                    $nextDate->day(min($config['day_of_month'], $nextDate->daysInMonth));
                }
                return $nextDate;

            case self::RECURRENCE_YEARLY:
                $nextDate = $fromDate->copy()->addYears($interval);
                if ($config['month_of_year']) {
                    $nextDate->month($config['month_of_year']);
                }
                if ($config['day_of_month']) {
                    $nextDate->day(min($config['day_of_month'], $nextDate->daysInMonth));
                }
                return $nextDate;

            default:
                return null;
        }
    }

    /**
     * Update next occurrence date
     */
    public function updateNextOccurrence(): void
    {
        if (!$this->isRecurringMaster()) {
            return;
        }

        $nextDate = $this->calculateNextOccurrence();
        $this->update(['next_occurrence_date' => $nextDate]);
    }

    /**
     * Check if there is an exception for a specific date
     */
    public function hasExceptionOnDate(\Carbon\Carbon $date): bool
    {
        return $this->recurringExceptions()
            ->whereDate('exception_date', $date->toDateString())
            ->exists();
    }

    /**
     * Get exception for a specific date
     */
    public function getExceptionForDate(\Carbon\Carbon $date): ?RecurringTaskException
    {
        return $this->recurringExceptions()
            ->whereDate('exception_date', $date->toDateString())
            ->first();
    }

    /**
     * Create a recurring task instance
     */
    public function createRecurringInstance(\Carbon\Carbon $occurrenceDate, array $overrides = []): Task
    {
        $instanceData = $this->toArray();
        
        // Remove fields that shouldn't be copied to instances
        unset($instanceData['id'], $instanceData['created_at'], $instanceData['updated_at']);
        
        // Set instance-specific fields
        $instanceData = array_merge($instanceData, [
            'parent_task_id' => $this->id,
            'is_recurring' => true,
            'is_recurring_instance' => true,
            'recurring_active' => false,
            'start_date' => $occurrenceDate,
            'deadline' => $occurrenceDate->copy()->addDays($this->getDurationInDays() ?? 1),
            'status' => self::STATUS_NEW,
            'progress' => 0,
            'completed_at' => null,
            'next_occurrence_date' => null,
            'recurrence_current_count' => 0
        ], $overrides);

        $instance = Task::create($instanceData);
        
        // Copy assignments
        foreach ($this->assignments as $assignment) {
            $instance->assignments()->create([
                'user_id' => $assignment->user_id,
                'assigned_at' => now(),
                'due_at' => $instance->deadline,
                'status' => Assignment::STATUS_ASSIGNED
            ]);
        }

        // Increment occurrence count
        $this->increment('recurrence_current_count');

        return $instance;
    }

    /**
     * Deactivate recurring schedule
     */
    public function deactivateRecurring(): void
    {
        $this->update(['recurring_active' => false]);
    }

    /**
     * Activate recurring schedule
     */
    public function activateRecurring(): void
    {
        if ($this->isRecurringMaster()) {
            $this->update(['recurring_active' => true]);
            $this->updateNextOccurrence();
        }
    }
}
