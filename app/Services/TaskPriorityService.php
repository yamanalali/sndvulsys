<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Assignment;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TaskPriorityService
{
    /**
     * Calculate priority score for a task
     */
    public function calculatePriorityScore(Task $task): float
    {
        $score = 0;

        // Base priority score
        $score += $this->getBasePriorityScore($task->priority);

        // Deadline urgency
        $score += $this->getDeadlineUrgencyScore($task);

        // Dependencies impact
        $score += $this->getDependenciesImpactScore($task);

        // Assignment status impact
        $score += $this->getAssignmentStatusScore($task);

        // Progress impact
        $score += $this->getProgressImpactScore($task);

        return $score;
    }

    /**
     * Get base priority score
     */
    private function getBasePriorityScore(string $priority): float
    {
        return match ($priority) {
            Task::PRIORITY_URGENT => 100,
            Task::PRIORITY_HIGH => 75,
            Task::PRIORITY_MEDIUM => 50,
            Task::PRIORITY_LOW => 25,
            default => 0
        };
    }

    /**
     * Get deadline urgency score
     */
    private function getDeadlineUrgencyScore(Task $task): float
    {
        if (!$task->deadline) {
            return 0;
        }

        $daysUntilDeadline = $task->getRemainingDays();

        if ($daysUntilDeadline < 0) {
            // Overdue - high penalty
            return 50;
        } elseif ($daysUntilDeadline <= 1) {
            // Due today or tomorrow
            return 40;
        } elseif ($daysUntilDeadline <= 3) {
            // Due this week
            return 30;
        } elseif ($daysUntilDeadline <= 7) {
            // Due next week
            return 20;
        } elseif ($daysUntilDeadline <= 14) {
            // Due in two weeks
            return 10;
        }

        return 0;
    }

    /**
     * Get dependencies impact score
     */
    private function getDependenciesImpactScore(Task $task): float
    {
        $dependenciesCount = $task->dependencies()->count();
        $dependentsCount = $task->dependents()->count();

        // Tasks with many dependents get higher priority
        $dependentScore = $dependentsCount * 5;

        // Tasks with many dependencies get slightly higher priority (blocked tasks)
        $dependencyScore = $dependenciesCount * 2;

        return $dependentScore + $dependencyScore;
    }

    /**
     * Get assignment status score
     */
    private function getAssignmentStatusScore(Task $task): float
    {
        $assignments = $task->assignments;

        if ($assignments->isEmpty()) {
            // Unassigned tasks get lower priority
            return -10;
        }

        $score = 0;
        foreach ($assignments as $assignment) {
            switch ($assignment->status) {
                case Assignment::STATUS_COMPLETED:
                    $score += 0; // No impact
                    break;
                case Assignment::STATUS_IN_PROGRESS:
                    $score += 10; // Positive impact
                    break;
                case Assignment::STATUS_OVERDUE:
                    $score += 20; // High impact
                    break;
                case Assignment::STATUS_ASSIGNED:
                    $score += 5; // Slight positive impact
                    break;
                case Assignment::STATUS_CANCELLED:
                    $score -= 5; // Negative impact
                    break;
            }
        }

        return $score;
    }

    /**
     * Get progress impact score
     */
    private function getProgressImpactScore(Task $task): float
    {
        if ($task->progress >= 90) {
            // Nearly completed tasks get higher priority
            return 15;
        } elseif ($task->progress >= 50) {
            // Half-completed tasks get moderate priority
            return 10;
        } elseif ($task->progress > 0) {
            // Started tasks get slight priority
            return 5;
        }

        return 0;
    }

    /**
     * Get tasks sorted by priority
     */
    public function getTasksByPriority(Collection $tasks = null): Collection
    {
        if (!$tasks) {
            $tasks = Task::with(['assignments', 'dependencies', 'dependents'])->get();
        }

        return $tasks->map(function ($task) {
            $task->priority_score = $this->calculatePriorityScore($task);
            return $task;
        })->sortByDesc('priority_score');
    }

    /**
     * Get urgent tasks (high priority + deadline approaching)
     */
    public function getUrgentTasks(): Collection
    {
        return Task::with(['assignments', 'dependencies', 'dependents'])
            ->where(function ($query) {
                $query->where('priority', Task::PRIORITY_URGENT)
                      ->orWhere('priority', Task::PRIORITY_HIGH)
                      ->orWhere('deadline', '<=', now()->addDays(3));
            })
            ->whereNotIn('status', [Task::STATUS_COMPLETED, Task::STATUS_CANCELLED])
            ->get()
            ->map(function ($task) {
                $task->priority_score = $this->calculatePriorityScore($task);
                return $task;
            })
            ->sortByDesc('priority_score');
    }

    /**
     * Get overdue tasks
     */
    public function getOverdueTasks(): Collection
    {
        return Task::with(['assignments', 'dependencies', 'dependents'])
            ->overdue()
            ->get()
            ->map(function ($task) {
                $task->priority_score = $this->calculatePriorityScore($task);
                return $task;
            })
            ->sortByDesc('priority_score');
    }

    /**
     * Get tasks due today
     */
    public function getTasksDueToday(): Collection
    {
        return Task::with(['assignments', 'dependencies', 'dependents'])
            ->dueToday()
            ->get()
            ->map(function ($task) {
                $task->priority_score = $this->calculatePriorityScore($task);
                return $task;
            })
            ->sortByDesc('priority_score');
    }

    /**
     * Get tasks due this week
     */
    public function getTasksDueThisWeek(): Collection
    {
        return Task::with(['assignments', 'dependencies', 'dependents'])
            ->dueThisWeek()
            ->get()
            ->map(function ($task) {
                $task->priority_score = $this->calculatePriorityScore($task);
                return $task;
            })
            ->sortByDesc('priority_score');
    }

    /**
     * Auto-adjust task priorities based on various factors
     */
    public function autoAdjustPriorities(): array
    {
        $adjusted = [];
        $tasks = Task::whereNotIn('status', [Task::STATUS_COMPLETED, Task::STATUS_CANCELLED])->get();

        foreach ($tasks as $task) {
            $oldPriority = $task->priority;
            $newPriority = $this->suggestPriority($task);

            if ($newPriority !== $oldPriority) {
                $task->update(['priority' => $newPriority]);
                $adjusted[] = [
                    'task_id' => $task->id,
                    'title' => $task->title,
                    'old_priority' => $oldPriority,
                    'new_priority' => $newPriority,
                    'reason' => $this->getPriorityAdjustmentReason($task, $newPriority)
                ];
            }
        }

        return $adjusted;
    }

    /**
     * Suggest priority for a task
     */
    private function suggestPriority(Task $task): string
    {
        $score = $this->calculatePriorityScore($task);

        if ($score >= 150) {
            return Task::PRIORITY_URGENT;
        } elseif ($score >= 100) {
            return Task::PRIORITY_HIGH;
        } elseif ($score >= 60) {
            return Task::PRIORITY_MEDIUM;
        } else {
            return Task::PRIORITY_LOW;
        }
    }

    /**
     * Get reason for priority adjustment
     */
    private function getPriorityAdjustmentReason(Task $task, string $newPriority): string
    {
        if ($task->isOverdue()) {
            return 'Task is overdue';
        }

        if ($task->deadline && $task->getRemainingDays() <= 1) {
            return 'Task is due today or tomorrow';
        }

        if ($task->dependents()->count() > 3) {
            return 'Task has many dependents';
        }

        if ($task->assignments()->where('status', Assignment::STATUS_OVERDUE)->count() > 0) {
            return 'Task has overdue assignments';
        }

        return 'Priority adjusted based on overall factors';
    }

    /**
     * Get priority distribution statistics
     */
    public function getPriorityDistribution(): array
    {
        $total = Task::count();
        
        return [
            'urgent' => [
                'count' => Task::byPriority(Task::PRIORITY_URGENT)->count(),
                'percentage' => $total > 0 ? round((Task::byPriority(Task::PRIORITY_URGENT)->count() / $total) * 100, 2) : 0
            ],
            'high' => [
                'count' => Task::byPriority(Task::PRIORITY_HIGH)->count(),
                'percentage' => $total > 0 ? round((Task::byPriority(Task::PRIORITY_HIGH)->count() / $total) * 100, 2) : 0
            ],
            'medium' => [
                'count' => Task::byPriority(Task::PRIORITY_MEDIUM)->count(),
                'percentage' => $total > 0 ? round((Task::byPriority(Task::PRIORITY_MEDIUM)->count() / $total) * 100, 2) : 0
            ],
            'low' => [
                'count' => Task::byPriority(Task::PRIORITY_LOW)->count(),
                'percentage' => $total > 0 ? round((Task::byPriority(Task::PRIORITY_LOW)->count() / $total) * 100, 2) : 0
            ]
        ];
    }
} 