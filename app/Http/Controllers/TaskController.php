<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::findOrFail($id);
    return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
    $request->validate([
        'depends_on_id' => 'required|exists:tasks,id',
    ]);

    $dependsOnId = $request->input('depends_on_id');

    // منع تكرار التبعية
    if (!$task->dependencies()->where('depends_on_task_id', $dependsOnId)->exists()) {
        $task->dependencies()->attach($dependsOnId);
    }

    return redirect()->back()->with('success');
}

  
}
