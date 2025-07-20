<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'assigned_at',
        'due_at',
        'completed_at',
        'status',
        'notes',
        'progress'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress' => 'integer'
    ];

    // Assignment Status Constants
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_COMPLETED = 'completed';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get all available assignment statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_ASSIGNED => 'مخصصة',
            self::STATUS_IN_PROGRESS => 'قيد التنفيذ',
            self::STATUS_SUBMITTED => 'مقدمة',
            self::STATUS_COMPLETED => 'مكتملة',
            self::STATUS_OVERDUE => 'متأخرة',
            self::STATUS_CANCELLED => 'ملغاة'
        ];
    }

    /**
     * Check if assignment is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_at && $this->due_at->isPast() && $this->status !== self::STATUS_COMPLETED;
    }

    /**
     * Check if assignment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Mark assignment as completed
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
     * Update assignment progress
     */
    public function updateProgress(int $progress): void
    {
        $this->update(['progress' => max(0, min(100, $progress))]);
        
        if ($this->progress >= 100) {
            $this->markAsCompleted();
        }
    }

    /**
     * Get assignment duration in days
     */
    public function getDurationInDays(): ?int
    {
        if (!$this->assigned_at || !$this->due_at) {
            return null;
        }

        return $this->assigned_at->diffInDays($this->due_at);
    }

    /**
     * Get remaining days until due date
     */
    public function getRemainingDays(): ?int
    {
        if (!$this->due_at) {
            return null;
        }

        return now()->diffInDays($this->due_at, false);
    }

    /**
     * Get assignment efficiency (completed vs expected time)
     */
    public function getEfficiency(): ?float
    {
        if (!$this->assigned_at || !$this->completed_at || !$this->due_at) {
            return null;
        }

        $actualDuration = $this->assigned_at->diffInDays($this->completed_at);
        $expectedDuration = $this->assigned_at->diffInDays($this->due_at);

        if ($expectedDuration === 0) {
            return null;
        }

        return ($expectedDuration / $actualDuration) * 100;
    }

    // Relationships
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_at', '<', now())
                    ->where('status', '!=', self::STATUS_COMPLETED);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_at', today())
                    ->where('status', '!=', self::STATUS_COMPLETED);
    }

    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('due_at', [now(), now()->addWeek()])
                    ->where('status', '!=', self::STATUS_COMPLETED);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    // Boot method to set default values
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($assignment) {
            if (!$assignment->assigned_at) {
                $assignment->assigned_at = now();
            }
            if (!$assignment->status) {
                $assignment->status = self::STATUS_ASSIGNED;
            }
        });
    }
}
