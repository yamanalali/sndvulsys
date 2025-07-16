<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\VolunteerRequest;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function index($volunteerRequestId) {
        $volunteer = VolunteerRequest::findOrFail($volunteerRequestId);
        $availabilities = $volunteer->availabilities;
        return view('availabilities.index', compact('availabilities', 'volunteer'));
    }

    public function create()
    {
        return view('availabilities.create');
    }

    public function store(Request $request, $volunteerRequestId) {
        $request->validate([
            'day' => 'required|string',
            'period' => 'required|string', // صباحاً/مساءً
        ]);
        Availability::create([
            'volunteer_request_id' => $volunteerRequestId,
            'day' => $request->day,
            'period' => $request->period,
        ]);
        return redirect()->route('availabilities.index', $volunteerRequestId)->with('success', 'تمت إضافة التوفر');
    }

    public function edit($id) {
        $availability = Availability::findOrFail($id);
        return view('availabilities.edit', compact('availability'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'day' => 'required|string',
            'period' => 'required|string',
        ]);
        $availability = Availability::findOrFail($id);
        $availability->update($request->only('day', 'period'));
        return back()->with('success', 'تم التعديل');
    }

    public function destroy($id) {
        $availability = Availability::findOrFail($id);
        $volunteerRequestId = $availability->volunteer_request_id;
        $availability->delete();
        return redirect()->route('availabilities.index', $volunteerRequestId)->with('success', 'تم الحذف');
    }
} 