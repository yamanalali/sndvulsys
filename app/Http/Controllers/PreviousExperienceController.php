<?php

namespace App\Http\Controllers;

use App\Models\PreviousExperience;
use App\Models\VolunteerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PreviousExperienceController extends Controller
{
    public function index() {
        $experiences = PreviousExperience::with('volunteerRequest')->orderBy('start_date', 'desc')->get();
        return view('previous_experiences.index', compact('experiences'));
    }

    public function create() {
        $volunteerRequests = VolunteerRequest::all();
        return view('previous_experiences.create', compact('volunteerRequests'));
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'volunteer-request_id' => 'required|exists:volunteer-requests,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'organization' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_current' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // إذا كانت الخبرة حالية، لا نحتاج تاريخ النهاية
        if ($request->is_current) {
            $request->merge(['end_date' => null]);
        }

        PreviousExperience::create($request->all());
        return redirect()->route('previous-experiences.index')->with('success', 'تمت إضافة الخبرة بنجاح');
    }

    public function show($id) {
        $experience = PreviousExperience::with('volunteerRequest')->findOrFail($id);
        return view('previous_experiences.show', compact('experience'));
    }

    public function edit($id) {
        $experience = PreviousExperience::findOrFail($id);
        $volunteerRequests = VolunteerRequest::all();
        return view('previous_experiences.edit', compact('experience', 'volunteerRequests'));
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'volunteer-request_id' => 'required|exists:volunteer-requests,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'organization' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_current' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $experience = PreviousExperience::findOrFail($id);
        
        // إذا كانت الخبرة حالية، لا نحتاج تاريخ النهاية
        if ($request->is_current) {
            $request->merge(['end_date' => null]);
        }

        $experience->update($request->all());
        
        return redirect()->route('previous-experiences.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy($id) {
        $experience = PreviousExperience::findOrFail($id);
        $experience->delete();
        return redirect()->route('previous-experiences.index')->with('success', 'تم الحذف بنجاح');
    }

    // API methods
    public function getExperiencesByVolunteer($volunteerRequestId) {
        $experiences = PreviousExperience::where('volunteer-request_id', $volunteerRequestId)
            ->orderBy('start_date', 'desc')
            ->get();
        
        return response()->json($experiences);
    }

    public function getCurrentExperiences() {
        $experiences = PreviousExperience::current()->with('volunteerRequest')->get();
        return response()->json($experiences);
    }

    public function getPastExperiences() {
        $experiences = PreviousExperience::past()->with('volunteerRequest')->get();
        return response()->json($experiences);
    }
} 