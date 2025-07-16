<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index() {
        $skills = Skill::all();
        return view('skills.index', compact('skills'));
    }

    public function create() {
        return view('skills.create');
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|string|unique:skills']);
        Skill::create($request->only('name'));
        return redirect()->route('skills.index')->with('success', 'تمت إضافة المهارة بنجاح');
    }

    public function edit($id) {
        $skill = Skill::findOrFail($id);
        return view('skills.edit', compact('skill'));
    }

    public function update(Request $request, $id) {
        $request->validate(['name' => 'required|string|unique:skills,name,'.$id]);
        $skill = Skill::findOrFail($id);
        $skill->update($request->only('name'));
        return redirect()->route('skills.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy($id) {
        Skill::destroy($id);
        return redirect()->route('skills.index')->with('success', 'تم الحذف بنجاح');
    }
} 