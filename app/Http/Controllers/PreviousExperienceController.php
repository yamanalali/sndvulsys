<?php

namespace App\Http\Controllers;

use App\Models\PreviousExperience;
use App\Models\VolunteerRequest;
use Illuminate\Http\Request;

class PreviousExperienceController extends Controller
{
    public function index() {
        $experiences = PreviousExperience::all();
        return view('previous_experiences.index', compact('experiences'));
    }

    public function create()
    {
        return view('previous_experiences.create');
    }

    public function store(Request $request, $volunteerRequestId) {
        $request->validate(['description' => 'required|string']);
        PreviousExperience::create([
            'volunteer_request_id' => $volunteerRequestId,
            'description' => $request->description,
        ]);
        return redirect()->route('previous_experiences.index', $volunteerRequestId)->with('success', 'تمت إضافة الخبرة');
    }

    public function edit($id) {
        $experience = PreviousExperience::findOrFail($id);
        return view('previous_experiences.edit', compact('experience'));
    }

    public function update(Request $request, $id) {
        $request->validate(['description' => 'required|string']);
        $experience = PreviousExperience::findOrFail($id);
        $experience->update($request->only('description'));
        return back()->with('success', 'تم التعديل');
    }

    public function destroy($id) {
        $experience = PreviousExperience::findOrFail($id);
        $volunteerRequestId = $experience->volunteer_request_id;
        $experience->delete();
        return redirect()->route('previous_experiences.index', $volunteerRequestId)->with('success', 'تم الحذف');
    }
} 