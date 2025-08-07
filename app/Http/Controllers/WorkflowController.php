<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function index()
    {
        // Only show workflow status definitions (where volunteer_request_id is null)
        $workflows = Workflow::whereNull('volunteer_request_id')->get();
        return view('workflows.index', compact('workflows'));
    }

    public function create()
    {
        return view('workflows.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:workflows,name',
            'description' => 'nullable|string|max:1000',
        ]);

        // Create workflow status definition (not tied to specific volunteer request)
        Workflow::create([
            'name' => $request->name,
            'description' => $request->description,
            'volunteer_request_id' => null,
            'reviewed_by' => null,
            'status' => 'active', // Default status for workflow definitions
        ]);

        return redirect()->route('workflows.index')->with('success', 'تمت إضافة الحالة بنجاح');
    }

    public function edit($id)
    {
        $workflow = Workflow::whereNull('volunteer_request_id')->findOrFail($id);
        return view('workflows.edit', compact('workflow'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:workflows,name,'.$id,
            'description' => 'nullable|string|max:1000',
        ]);

        $workflow = Workflow::whereNull('volunteer_request_id')->findOrFail($id);
        $workflow->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('workflows.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy($id)
    {
        $workflow = Workflow::whereNull('volunteer_request_id')->findOrFail($id);
        $workflow->delete();
        return redirect()->route('workflows.index')->with('success', 'تم الحذف بنجاح');
    }
}
