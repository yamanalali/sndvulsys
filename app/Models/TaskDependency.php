<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskDependency extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'depends_on_task_id',
        'dependency_type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Dependency Types
    const TYPE_FINISH_TO_START = 'finish_to_start';
    const TYPE_START_TO_START = 'start_to_start';
    const TYPE_FINISH_TO_FINISH = 'finish_to_finish';
    const TYPE_START_TO_FINISH = 'start_to_finish';

    /**
     * Get all available dependency types
     */
    public static function getDependencyTypes(): array
    {
        return [
            self::TYPE_FINISH_TO_START => 'انتهاء إلى بداية',
            self::TYPE_START_TO_START => 'بداية إلى بداية',
            self::TYPE_FINISH_TO_FINISH => 'انتهاء إلى انتهاء',
            self::TYPE_START_TO_FINISH => 'بداية إلى انتهاء'
        ];
    }

    /**
     * Check if dependency is satisfied
     */
    public function isSatisfied(): bool
    {
        if (!$this->is_active) {
            return true;
        }

        $dependentTask = $this->dependentTask;
        $prerequisiteTask = $this->prerequisiteTask;

        if (!$dependentTask || !$prerequisiteTask) {
            return false;
        }

        switch ($this->dependency_type) {
            case self::TYPE_FINISH_TO_START:
                return $prerequisiteTask->isCompleted();
            
            case self::TYPE_START_TO_START:
                return $prerequisiteTask->status === Task::STATUS_IN_PROGRESS || 
                       $prerequisiteTask->isCompleted();
            
            case self::TYPE_FINISH_TO_FINISH:
                return $prerequisiteTask->isCompleted();
            
            case self::TYPE_START_TO_FINISH:
                return $prerequisiteTask->status === Task::STATUS_IN_PROGRESS || 
                       $prerequisiteTask->isCompleted();
            
            default:
                return false;
        }
    }

    /**
     * Check if dependency creates a circular reference
     */
    public static function wouldCreateCircularDependency($taskId, $dependsOnTaskId): bool
    {
        // Check if the dependent task is already a prerequisite of the task it depends on
        $existingDependency = self::where('task_id', $dependsOnTaskId)
                                 ->where('depends_on_task_id', $taskId)
                                 ->first();

        if ($existingDependency) {
            return true;
        }

        // Check for transitive dependencies
        $prerequisites = self::where('task_id', $dependsOnTaskId)->get();
        
        foreach ($prerequisites as $prerequisite) {
            if (self::wouldCreateCircularDependency($taskId, $prerequisite->depends_on_task_id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all prerequisites for a task
     */
    public static function getPrerequisitesForTask($taskId)
    {
        return self::where('task_id', $taskId)
                   ->where('is_active', true)
                   ->with('prerequisiteTask')
                   ->get();
    }

    /**
     * Get all dependents for a task
     */
    public static function getDependentsForTask($taskId)
    {
        return self::where('depends_on_task_id', $taskId)
                   ->where('is_active', true)
                   ->with('dependentTask')
                   ->get();
    }

    /**
     * Check if task can be started based on dependencies
     */
    public static function canTaskBeStarted($taskId): bool
    {
        $dependencies = self::getPrerequisitesForTask($taskId);
        
        foreach ($dependencies as $dependency) {
            if (!$dependency->isSatisfied()) {
                return false;
            }
        }
        
        return true;
    }

    // Relationships
    public function dependentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function prerequisiteTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'depends_on_task_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('dependency_type', $type);
    }

    public function scopeByTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    public function scopeByPrerequisite($query, $prerequisiteTaskId)
    {
        return $query->where('depends_on_task_id', $prerequisiteTaskId);
    }
} 