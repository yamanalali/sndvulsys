<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $assignedAt = $this->faker->dateTimeBetween('-30 days', 'now');
        $dueAt = $this->faker->dateTimeBetween($assignedAt, '+30 days');
        
        return [
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
            'assigned_at' => $assignedAt,
            'due_at' => $dueAt,
            'completed_at' => null,
            'status' => $this->faker->randomElement([
                Assignment::STATUS_ASSIGNED,
                Assignment::STATUS_IN_PROGRESS,
                Assignment::STATUS_SUBMITTED,
                Assignment::STATUS_COMPLETED,
                Assignment::STATUS_OVERDUE,
                Assignment::STATUS_CANCELLED
            ]),
            'notes' => $this->faker->optional(0.6)->paragraph(1, 2),
            'progress' => $this->faker->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the assignment is assigned.
     */
    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Assignment::STATUS_ASSIGNED,
            'progress' => 0,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the assignment is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Assignment::STATUS_IN_PROGRESS,
            'progress' => $this->faker->numberBetween(10, 90),
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the assignment is submitted.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Assignment::STATUS_SUBMITTED,
            'progress' => $this->faker->numberBetween(90, 99),
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the assignment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Assignment::STATUS_COMPLETED,
            'progress' => 100,
            'completed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Indicate that the assignment is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Assignment::STATUS_OVERDUE,
            'due_at' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the assignment is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Assignment::STATUS_CANCELLED,
            'progress' => 0,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the assignment is due today.
     */
    public function dueToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_at' => now(),
            'status' => $this->faker->randomElement([
                Assignment::STATUS_ASSIGNED,
                Assignment::STATUS_IN_PROGRESS,
                Assignment::STATUS_SUBMITTED
            ]),
        ]);
    }

    /**
     * Indicate that the assignment is due this week.
     */
    public function dueThisWeek(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_at' => $this->faker->dateTimeBetween('now', '+7 days'),
            'status' => $this->faker->randomElement([
                Assignment::STATUS_ASSIGNED,
                Assignment::STATUS_IN_PROGRESS,
                Assignment::STATUS_SUBMITTED
            ]),
        ]);
    }

    /**
     * Indicate that the assignment has no deadline.
     */
    public function noDeadline(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_at' => null,
        ]);
    }

    /**
     * Indicate that the assignment has high progress.
     */
    public function highProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'progress' => $this->faker->numberBetween(70, 100),
        ]);
    }

    /**
     * Indicate that the assignment has medium progress.
     */
    public function mediumProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'progress' => $this->faker->numberBetween(30, 70),
        ]);
    }

    /**
     * Indicate that the assignment has low progress.
     */
    public function lowProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'progress' => $this->faker->numberBetween(0, 30),
        ]);
    }

    /**
     * Create assignment for a specific task.
     */
    public function forTask(Task $task): static
    {
        return $this->state(fn (array $attributes) => [
            'task_id' => $task->id,
        ]);
    }

    /**
     * Create assignment for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create assignment with specific task and user.
     */
    public function forTaskAndUser(Task $task, User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'task_id' => $task->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create multiple assignments for a task.
     */
    public function createMultipleForTask(Task $task, int $count = 3): \Illuminate\Database\Eloquent\Collection
    {
        return Assignment::factory()
            ->count($count)
            ->forTask($task)
            ->create();
    }

    /**
     * Create assignments for multiple users on a task.
     */
    public function createForMultipleUsers(Task $task, array $userIds): \Illuminate\Database\Eloquent\Collection
    {
        $assignments = collect();
        
        foreach ($userIds as $userId) {
            $assignments->push(
                Assignment::factory()
                    ->forTask($task)
                    ->state(['user_id' => $userId])
                    ->create()
            );
        }
        
        return $assignments;
    }
} 