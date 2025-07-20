<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['manager', 'tasks'])->get();
        
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $users = User::all();
        return view('projects.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,completed,on_hold,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        try {
            $project = Project::create($validated);
            
            Log::info('Project created', ['project_id' => $project->id, 'user_id' => auth()->id()]);
            
            return redirect()->route('projects.index')
                           ->with('success', 'تم إنشاء المشروع بنجاح');
        } catch (\Exception $e) {
            Log::error('Failed to create project', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء المشروع');
        }
    }

    public function show(Project $project)
    {
        $project->load(['manager', 'tasks.assignments.user', 'teamMembers']);
        
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $users = User::all();
        return view('projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,completed,on_hold,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        try {
            $project->update($validated);
            
            Log::info('Project updated', ['project_id' => $project->id, 'user_id' => auth()->id()]);
            
            return redirect()->route('projects.show', $project)
                           ->with('success', 'تم تحديث المشروع بنجاح');
        } catch (\Exception $e) {
            Log::error('Failed to update project', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث المشروع');
        }
    }

    public function destroy(Project $project)
    {
        try {
            $project->delete();
            
            Log::info('Project deleted', ['project_id' => $project->id, 'user_id' => auth()->id()]);
            
            return redirect()->route('projects.index')
                           ->with('success', 'تم حذف المشروع بنجاح');
        } catch (\Exception $e) {
            Log::error('Failed to delete project', ['error' => $e->getMessage()]);
            return back()->with('error', 'حدث خطأ أثناء حذف المشروع');
        }
    }

    public function myProjects()
    {
        $userId = auth()->id();
        
        // المشاريع التي يديرها المستخدم
        $managedProjects = Project::where('manager_id', $userId)
                                 ->with(['tasks', 'teamMembers'])
                                 ->get();
        
        // المشاريع التي يشارك فيها المستخدم كعضو فريق
        $teamProjects = Project::whereHas('teamMembers', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['tasks', 'teamMembers'])->get();
        
        // المشاريع التي تحتوي على مهام مخصصة للمستخدم
        $assignedProjects = Project::whereHas('tasks.assignments', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['tasks', 'teamMembers'])->get();
        
        return view('projects.my-projects', compact('managedProjects', 'teamProjects', 'assignedProjects'));
    }

    public function teamTasks()
    {
        $userId = auth()->id();
        
        // الحصول على المشاريع التي يشارك فيها المستخدم
        $userProjects = Project::where(function($query) use ($userId) {
            $query->where('manager_id', $userId)
                  ->orWhereHas('teamMembers', function($q) use ($userId) {
                      $q->where('user_id', $userId);
                  });
        })->pluck('id');
        
        // المهام في مشاريع الفريق
        $teamTasks = Task::whereIn('project_id', $userProjects)
                        ->with(['project', 'assignments.user', 'category'])
                        ->get();
        
        return view('projects.team-tasks', compact('teamTasks'));
    }
}
