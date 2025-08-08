<?php

namespace Database\Factories;

use App\Models\AdvancedSearch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdvancedSearch>
 */
class AdvancedSearchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $searchTypes = array_keys(AdvancedSearch::getSearchTypes());
        $searchType = $this->faker->randomElement($searchTypes);
        
        $filters = [];
        if ($this->faker->boolean(70)) {
            $filters['status'] = $this->faker->randomElement(['pending', 'in_review', 'approved', 'rejected']);
        }
        if ($this->faker->boolean(50)) {
            $filters['priority'] = $this->faker->randomElement(['low', 'medium', 'high', 'urgent']);
        }
        if ($this->faker->boolean(30)) {
            $filters['date_range'] = $this->faker->randomElement(['today', 'this_week', 'this_month']);
        }
        
        $sortOptions = [];
        if ($this->faker->boolean(80)) {
            $sortOptions['primary'] = $this->faker->randomElement(['created_at_desc', 'created_at_asc', 'updated_at_desc']);
        }
        if ($this->faker->boolean(40)) {
            $sortOptions['secondary'] = $this->faker->randomElement(['status_asc', 'priority_desc', 'priority_asc']);
        }
        
        return [
            'search_term' => $this->faker->optional(0.8)->words(2, true),
            'search_type' => $searchType,
            'filters' => $filters,
            'sort_options' => $sortOptions,
            'user_id' => User::factory(),
            'session_id' => $this->faker->uuid,
            'ip_address' => $this->faker->ipv4,
            'total_results' => $this->faker->numberBetween(0, 1000),
            'search_results' => $this->faker->optional()->randomElements(range(1, 100), $this->faker->numberBetween(5, 20)),
            'executed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'execution_time_ms' => $this->faker->numberBetween(10, 5000),
            'is_saved' => $this->faker->boolean(20),
            'saved_name' => $this->faker->optional(0.2)->sentence(3),
            'notes' => $this->faker->optional(0.3)->paragraph,
            'is_public' => $this->faker->boolean(10),
            'sharing_options' => $this->faker->optional()->randomElements(['email', 'link', 'embed'], $this->faker->numberBetween(1, 3)),
        ];
    }

    /**
     * Indicate that the search is saved.
     */
    public function saved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_saved' => true,
            'saved_name' => $this->faker->sentence(3),
        ]);
    }

    /**
     * Indicate that the search is public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }

    /**
     * Indicate that the search has high priority filters.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'filters' => array_merge($attributes['filters'] ?? [], [
                'priority' => 'high'
            ]),
        ]);
    }

    /**
     * Indicate that the search is for volunteer requests.
     */
    public function volunteerRequests(): static
    {
        return $this->state(fn (array $attributes) => [
            'search_type' => 'volunteer_requests',
        ]);
    }

    /**
     * Indicate that the search is for submissions.
     */
    public function submissions(): static
    {
        return $this->state(fn (array $attributes) => [
            'search_type' => 'submissions',
        ]);
    }

    /**
     * Indicate that the search is for workflows.
     */
    public function workflows(): static
    {
        return $this->state(fn (array $attributes) => [
            'search_type' => 'workflows',
        ]);
    }

    /**
     * Indicate that the search has many results.
     */
    public function manyResults(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_results' => $this->faker->numberBetween(100, 1000),
        ]);
    }

    /**
     * Indicate that the search has few results.
     */
    public function fewResults(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_results' => $this->faker->numberBetween(0, 10),
        ]);
    }

    /**
     * Indicate that the search is fast.
     */
    public function fast(): static
    {
        return $this->state(fn (array $attributes) => [
            'execution_time_ms' => $this->faker->numberBetween(10, 100),
        ]);
    }

    /**
     * Indicate that the search is slow.
     */
    public function slow(): static
    {
        return $this->state(fn (array $attributes) => [
            'execution_time_ms' => $this->faker->numberBetween(2000, 10000),
        ]);
    }
} 