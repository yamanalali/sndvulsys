<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-30 days', '+30 days');
        $deadline = $this->faker->dateTimeBetween($startDate, '+60 days');
        
        return [
            'title' => $this->faker->sentence(3, 6),
            'project_id' => \App\Models\Project::inRandomOrder()->first()?->id ?? 1,
            'description' => $this->faker->paragraph(2, 4),
            'status' => $this->faker->randomElement([
                Task::STATUS_NEW,
                Task::STATUS_IN_PROGRESS,
                Task::STATUS_PENDING,
                Task::STATUS_COMPLETED,
                Task::STATUS_CANCELLED
            ]),
            'priority' => $this->faker->randomElement([
                Task::PRIORITY_URGENT,
                Task::PRIORITY_HIGH,
                Task::PRIORITY_MEDIUM,
                Task::PRIORITY_LOW
            ]),
            'category_id' => Category::factory(),
            'created_by' => User::factory(),
            'assigned_to' => User::factory(),
            'start_date' => $startDate,
            'deadline' => $deadline,
            'completed_at' => null,
            'progress' => $this->faker->numberBetween(0, 100),
            'notes' => $this->faker->optional(0.7)->paragraph(1, 2),
            'is_recurring' => $this->faker->boolean(20),
            'recurrence_pattern' => $this->faker->optional()->randomElement(['daily', 'weekly', 'monthly']),
        ];
    }

    /**
     * Indicate that the task is new.
     */
    public function asNew(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Task::STATUS_NEW,
            'progress' => 0,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the task is in progress.
     */
    public function asInProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Task::STATUS_IN_PROGRESS,
            'progress' => $this->faker->numberBetween(10, 90),
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the task is pending.
     */
    public function asPending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Task::STATUS_PENDING,
            'progress' => $this->faker->numberBetween(0, 50),
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the task is completed.
     */
    public function asCompleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Task::STATUS_COMPLETED,
            'progress' => 100,
            'completed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Indicate that the task is cancelled.
     */
    public function asCancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Task::STATUS_CANCELLED,
            'progress' => 0,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the task is urgent.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => Task::PRIORITY_URGENT,
            'deadline' => $this->faker->dateTimeBetween('now', '+3 days'),
        ]);
    }

    /**
     * Indicate that the task is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => Task::PRIORITY_HIGH,
            'deadline' => $this->faker->dateTimeBetween('now', '+7 days'),
        ]);
    }

    /**
     * Indicate that the task is medium priority.
     */
    public function mediumPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => Task::PRIORITY_MEDIUM,
            'deadline' => $this->faker->dateTimeBetween('now', '+14 days'),
        ]);
    }

    /**
     * Indicate that the task is low priority.
     */
    public function lowPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => Task::PRIORITY_LOW,
            'deadline' => $this->faker->dateTimeBetween('now', '+30 days'),
        ]);
    }

    /**
     * Indicate that the task is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'deadline' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
            'status' => $this->faker->randomElement([
                Task::STATUS_NEW,
                Task::STATUS_IN_PROGRESS,
                Task::STATUS_PENDING
            ]),
        ]);
    }

    /**
     * Indicate that the task is due today.
     */
    public function dueToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'deadline' => now(),
            'status' => $this->faker->randomElement([
                Task::STATUS_NEW,
                Task::STATUS_IN_PROGRESS,
                Task::STATUS_PENDING
            ]),
        ]);
    }

    /**
     * Indicate that the task is due this week.
     */
    public function dueThisWeek(): static
    {
        return $this->state(fn (array $attributes) => [
            'deadline' => $this->faker->dateTimeBetween('now', '+7 days'),
            'status' => $this->faker->randomElement([
                Task::STATUS_NEW,
                Task::STATUS_IN_PROGRESS,
                Task::STATUS_PENDING
            ]),
        ]);
    }

    /**
     * Indicate that the task is recurring.
     */
    public function recurring(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => true,
            'recurrence_pattern' => $this->faker->randomElement(['daily', 'weekly', 'monthly']),
        ]);
    }

    /**
     * Indicate that the task has no deadline.
     */
    public function noDeadline(): static
    {
        return $this->state(fn (array $attributes) => [
            'deadline' => null,
        ]);
    }

    /**
     * Indicate that the task has no assignment.
     */
    public function unassigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_to' => null,
        ]);
    }
} 