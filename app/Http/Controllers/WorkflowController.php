<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function index()
    {
        $workflows = Workflow::all();
        return view('workflows.index', compact('workflows'));
    }

    public function create()
    {
        return view('workflows.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:workflows',
            'description' => 'nullable|string',
        ]);
        Workflow::create($request->only('name', 'description'));
        return redirect()->route('workflows.index')->with('success', 'تمت إضافة الحالة بنجاح');
    }

    public function edit($id)
    {
        $workflow = Workflow::findOrFail($id);
        return view('workflows.edit', compact('workflow'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:workflows,name,'.$id,
            'description' => 'nullable|string',
        ]);
        $workflow = Workflow::findOrFail($id);
        $workflow->update($request->only('name', 'description'));
        return redirect()->route('workflows.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy($id)
    {
        Workflow::destroy($id);
        return redirect()->route('workflows.index')->with('success', 'تم الحذف بنجاح');
    }
}
