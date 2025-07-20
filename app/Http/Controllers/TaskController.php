<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Task::with(['project', 'assignments.user', 'category']);
        
        // Filter by project if specified
        if (request('project')) {
            $query->where('project_id', request('project'));
        }
        
        $tasks = $query->orderByDesc('created_at')->get();
        $projects = \App\Models\Project::all();
        return view('tasks.index', compact('tasks', 'projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:new,in_progress,pending,completed,cancelled',
                'deadline' => 'required|date',
            ]);
            $validated['project_id'] = $request->input('project_id');
            $validated['category_id'] = 1;
            $task = Task::create($validated);
            // حفظ المكلفين
            if ($request->has('user_ids')) {
                foreach ($request->user_ids as $userId) {
                    $task->assignments()->create([
                        'user_id' => $userId,
                        'assigned_at' => now(),
                        'status' => 'assigned',
                    ]);
                }
            }
            Toastr::success('تمت إضافة المهمة بنجاح', 'نجاح');
            return redirect()->route('tasks.index');
        } catch (\Exception $e) {
            Toastr::error('حدث خطأ أثناء إضافة المهمة', 'خطأ');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::findOrFail($id);
        $allTasks = Task::where('id', '!=', $task->id)->get();
        $task->load(['dependenciesRaw.prerequisiteTask']);
        return view('tasks.show', compact('task', 'allTasks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $task = Task::findOrFail($id);
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:new,in_progress,pending,completed,cancelled',
                'deadline' => 'required|date',
            ]);
            $task->update($validated);
            // تحديث المكلفين
            if ($request->has('user_ids')) {
                // حذف المكلفين غير المختارين
                $task->assignments()->whereNotIn('user_id', $request->user_ids)->delete();
                // إضافة أو تحديث المكلفين الجدد
                foreach ($request->user_ids as $userId) {
                    $task->assignments()->firstOrCreate([
                        'user_id' => $userId
                    ], [
                        'assigned_at' => now(),
                        'status' => 'assigned',
                    ]);
                }
            }
            Toastr::success('تم تعديل المهمة بنجاح', 'نجاح');
            return redirect()->route('dashboard.home');
        } catch (\Exception $e) {
            Toastr::error('حدث خطأ أثناء تعديل المهمة', 'خطأ');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();
            Toastr::success('تم حذف المهمة بنجاح', 'نجاح');
            return redirect()->route('dashboard.home');
        } catch (\Exception $e) {
            Toastr::error('حدث خطأ أثناء حذف المهمة', 'خطأ');
            return redirect()->back();
        }
    }

    public function updateStatus(Request $request, Task $task)
{
    $newStatus = $request->input('status');

    
    $allowedTransitions = [
        'new' => ['in_progress', 'cancelled'],
        'in_progress' => ['pending_review', 'on_hold', 'cancelled'],
        'pending_review' => ['approved', 'rejected'],
        'awaiting_approval' => ['approved', 'rejected'],
        'approved' => ['completed'],
        'rejected' => [],
        'on_hold' => ['in_progress'],
        'completed' => ['archived'],
        'cancelled' => [],
        'archived' => [],
    ];
 

    if (!in_array($newStatus, $allowedTransitions[$task->status])) {
        return response()->json([
            'message' => 'Invalid status transition from ' . $task->status . ' to ' . $newStatus
        ], 400);
    }

    
    $task->status = $newStatus;
    $task->save();

    return response()->json([
        'message' => 'Task status updated successfully.',
        'task' => $task
    ]);
}

public function showDependenciesForm(Task $task)
{
    $allTasks = Task::where('id', '!=', $task->id)->get(); 
    return view('tasks.dependencies', compact('task', 'allTasks'));
}


public function storeDependency(Request $request, Task $task)
{
    try {
        $request->validate([
            'depends_on_id' => 'required|exists:tasks,id',
        ]);
        $dependsOnId = $request->input('depends_on_id');
        // منع تكرار التبعية
        if (!$task->dependencies()->where('depends_on_task_id', $dependsOnId)->exists()) {
            $task->dependencies()->attach($dependsOnId);
            Toastr::success('تمت إضافة التبعية بنجاح', 'نجاح');
        } else {
            Toastr::error('هذه التبعية موجودة بالفعل', 'خطأ');
        }
        return redirect()->back();
    } catch (\Exception $e) {
        Toastr::error('حدث خطأ أثناء إضافة التبعية', 'خطأ');
        return redirect()->back();
    }
}

  
}
