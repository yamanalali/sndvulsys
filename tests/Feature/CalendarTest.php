<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Assignment;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_page_loads_without_error()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a project
        $project = Project::factory()->create();
        
        // Create a task with deadline
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'deadline' => Carbon::now()->addDays(5),
            'status' => 'in_progress'
        ]);
        
        // Assign the task to the user
        Assignment::factory()->create([
            'user_id' => $user->id,
            'task_id' => $task->id
        ]);
        
        // Act as the user and visit the calendar page
        $response = $this->actingAs($user)
                        ->get(route('progress.calendar'));
        
        // Assert the page loads successfully
        $response->assertStatus(200);
        $response->assertViewIs('progress.calendar');
        $response->assertViewHas('calendarData');
        $response->assertViewHas('month');
    }

    public function test_calendar_data_is_collection()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a project
        $project = Project::factory()->create();
        
        // Create a task with deadline
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'deadline' => Carbon::now()->addDays(5),
            'status' => 'in_progress'
        ]);
        
        // Assign the task to the user
        Assignment::factory()->create([
            'user_id' => $user->id,
            'task_id' => $task->id
        ]);
        
        // Act as the user and visit the calendar page
        $response = $this->actingAs($user)
                        ->get(route('progress.calendar'));
        
        // Get the calendar data from the view
        $calendarData = $response->viewData('calendarData');
        
        // Assert that calendarData is a collection
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $calendarData);
        
        // Assert that the collection has data
        $this->assertGreaterThan(0, $calendarData->count());
    }

    public function test_calendar_with_specific_month()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a project
        $project = Project::factory()->create();
        
        // Create a task with deadline in a specific month
        $specificDate = Carbon::create(2024, 6, 15); // June 15, 2024
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'deadline' => $specificDate,
            'status' => 'in_progress'
        ]);
        
        // Assign the task to the user
        Assignment::factory()->create([
            'user_id' => $user->id,
            'task_id' => $task->id
        ]);
        
        // Act as the user and visit the calendar page with specific month
        $response = $this->actingAs($user)
                        ->get(route('progress.calendar', ['month' => '2024-06']));
        
        // Assert the page loads successfully
        $response->assertStatus(200);
        $response->assertViewIs('progress.calendar');
        
        // Get the calendar data from the view
        $calendarData = $response->viewData('calendarData');
        
        // Assert that calendarData is a collection
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $calendarData);
        
        // Find the specific date in the calendar data
        $dayData = $calendarData->where('date', '2024-06-15')->first();
        
        // Assert that the day has tasks
        $this->assertNotNull($dayData);
        $this->assertGreaterThan(0, $dayData['tasks']->count());
    }
} 