<?php

namespace App\Http\Controllers;

use App\Models\VolunteerRequest;
use Illuminate\Http\Request;

class VolunteerRequestController extends Controller
{
    public function index() {
        $requests = VolunteerRequest::all();
        return view('volunteer_requests.index', compact('requests'));
    }

    public function create() {
        // جلب المهارات لعرضها في النموذج
        $skills = \App\Models\Skill::all();
        return view('volunteer_requests.create', compact('skills'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            // ... باقي الحقول ...
        ]);
        $volunteerRequest = VolunteerRequest::create($data);

        // ربط المهارات
        if ($request->has('skills')) {
            $volunteerRequest->skills()->sync($request->skills);
        }

        return redirect()->route('volunteer_requests.index')->with('success', 'تم إرسال الطلب');
    }

    public function show($id) {
        $request = VolunteerRequest::findOrFail($id);
        return view('volunteer_requests.show', compact('request'));
    }

    public function edit($id) {
        $request = VolunteerRequest::findOrFail($id);
        $skills = \App\Models\Skill::all();
        return view('volunteer_requests.edit', compact('request', 'skills'));
    }

    public function update(Request $request, $id) {
        $data = $request->validate([
            'full_name' => 'required|string',
            // ... باقي الحقول ...
        ]);
        $volunteerRequest = VolunteerRequest::findOrFail($id);
        $volunteerRequest->update($data);

        // تحديث المهارات
        if ($request->has('skills')) {
            $volunteerRequest->skills()->sync($request->skills);
        }

        return redirect()->route('volunteer_requests.index')->with('success', 'تم التعديل');
    }

    public function destroy($id) {
        VolunteerRequest::destroy($id);
        return redirect()->route('volunteer_requests.index')->with('success', 'تم الحذف');
    }

    public function updateStatus(Request $request, $id) {
        $request->validate(['status' => 'required|in:pending,approved,rejected,withdrawn']);
        $volunteerRequest = VolunteerRequest::findOrFail($id);
        $volunteerRequest->update(['status' => $request->status]);
        return back()->with('success', 'تم تحديث الحالة');
    }
} 