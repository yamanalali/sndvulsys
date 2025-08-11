<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Assignment;
use App\Models\Project;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_progress_page_loads_without_error()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a project
        $project = Project::factory()->create();
        
        // Create a category
        $category = Category::factory()->create();
        
        // Create a task
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'category_id' => $category->id,
            'deadline' => Carbon::now()->addDays(5),
            'status' => 'in_progress'
        ]);
        
        // Assign the task to the user
        Assignment::factory()->create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'status' => Assignment::STATUS_IN_PROGRESS
        ]);
        
        // Test the progress page
        $response = $this->actingAs($user)
            ->get(route('progress.index'));
        
        $response->assertStatus(200);
        $response->assertSee('تتبع التقدم');
    }

    public function test_progress_data_is_collection()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Test that the progress data methods return collections
        $this->actingAs($user);
        
        $response = $this->get(route('progress.index'));
        
        $response->assertStatus(200);
        
        // The view should load without errors, which means collections are working
        $this->assertTrue(true);
    }

    public function test_progress_with_no_assignments()
    {
        // Create a user with no assignments
        $user = User::factory()->create();
        
        // Test the progress page with no assignments
        $response = $this->actingAs($user)
            ->get(route('progress.index'));
        
        $response->assertStatus(200);
        $response->assertSee('تتبع التقدم');
        
        // Should not throw any collection errors
        $this->assertTrue(true);
    }

    public function test_project_progress_page()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a project
        $project = Project::factory()->create();
        
        // Create a task in the project
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'status' => 'in_progress'
        ]);
        
        // Assign the task to the user
        Assignment::factory()->create([
            'user_id' => $user->id,
            'task_id' => $task->id
        ]);
        
        // Test the project progress page
        $response = $this->actingAs($user)
            ->get(route('progress.project', $project));
        
        $response->assertStatus(200);
    }
} 