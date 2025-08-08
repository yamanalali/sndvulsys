<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\VolunteerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AvailabilityController extends Controller
{
    public function index() {
        $availabilities = Availability::with('volunteerRequest')->orderBy('day')->get();
        $days = Availability::getDays();
        $timeSlots = Availability::getTimeSlots();
        
        return view('availabilities.index', compact('availabilities', 'days', 'timeSlots'));
    }

    public function create() {
        $volunteerRequests = VolunteerRequest::all();
        $days = Availability::getDays();
        $timeSlots = Availability::getTimeSlots();
        
        return view('availabilities.create', compact('volunteerRequests', 'days', 'timeSlots'));
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'volunteer-request_id' => 'required|exists:volunteer-requests,id',
            'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
            'time_slot' => 'nullable|in:morning,afternoon,evening,night,flexible',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'is_available' => 'boolean',
            'notes' => 'nullable|string|max:500',
            'preferred_hours_per_week' => 'nullable|integer|min:1|max:168'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // التحقق من عدم تكرار نفس اليوم لنفس المتطوع
        $existingAvailability = Availability::where('volunteer-request_id', $request->input('volunteer-request_id'))
            ->where('day', $request->day)
            ->first();

        if ($existingAvailability) {
            return redirect()->back()->withErrors(['day' => 'يوجد توفر مسجل لهذا اليوم'])->withInput();
        }

        Availability::create($request->all());
        return redirect()->route('availabilities.index')->with('success', 'تمت إضافة التوفر بنجاح');
    }

    public function show($id) {
        $availability = Availability::with('volunteerRequest')->findOrFail($id);
        $days = Availability::getDays();
        $timeSlots = Availability::getTimeSlots();
        
        return view('availabilities.show', compact('availability', 'days', 'timeSlots'));
    }

    public function edit($id) {
        $availability = Availability::findOrFail($id);
        $volunteerRequests = VolunteerRequest::all();
        $days = Availability::getDays();
        $timeSlots = Availability::getTimeSlots();
        
        return view('availabilities.edit', compact('availability', 'volunteerRequests', 'days', 'timeSlots'));
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'volunteer-request_id' => 'required|exists:volunteer-requests,id',
            'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
            'time_slot' => 'nullable|in:morning,afternoon,evening,night,flexible',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'is_available' => 'boolean',
            'notes' => 'nullable|string|max:500',
            'preferred_hours_per_week' => 'nullable|integer|min:1|max:168'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $availability = Availability::findOrFail($id);
        
        // التحقق من عدم تكرار نفس اليوم لنفس المتطوع (باستثناء السجل الحالي)
        $existingAvailability = Availability::where('volunteer-request_id', $request->input('volunteer-request_id'))
            ->where('day', $request->day)
            ->where('id', '!=', $id)
            ->first();

        if ($existingAvailability) {
            return redirect()->back()->withErrors(['day' => 'يوجد توفر مسجل لهذا اليوم'])->withInput();
        }

        $availability->update($request->all());
        return redirect()->route('availabilities.index')->with('success', 'تم التعديل بنجاح');
    }

    public function destroy($id) {
        $availability = Availability::findOrFail($id);
        $availability->delete();
        return redirect()->route('availabilities.index')->with('success', 'تم الحذف بنجاح');
    }

    // API methods
    public function getAvailabilitiesByVolunteer($volunteerRequestId) {
        $availabilities = Availability::where('volunteer-request_id', $volunteerRequestId)
            ->orderBy('day')
            ->get();
        
        return response()->json($availabilities);
    }

    public function getAvailableVolunteers($day, $timeSlot = null) {
        $query = Availability::with('volunteerRequest')
            ->where('day', $day)
            ->where('is_available', true);

        if ($timeSlot) {
            $query->where('time_slot', $timeSlot);
        }

        $availabilities = $query->get();
        
        return response()->json($availabilities);
    }

    public function toggleAvailability($id) {
        $availability = Availability::findOrFail($id);
        $availability->update(['is_available' => !$availability->is_available]);
        
        return response()->json([
            'success' => true,
            'is_available' => $availability->is_available,
            'message' => $availability->is_available ? 'تم تفعيل التوفر' : 'تم إلغاء التوفر'
        ]);
    }

    public function getWeeklySchedule($volunteerRequestId) {
        $availabilities = Availability::where('volunteer-request_id', $volunteerRequestId)
            ->orderBy('day')
            ->get()
            ->groupBy('day');
        
        $days = Availability::getDays();
        $schedule = [];
        
        foreach ($days as $key => $day) {
            $schedule[$key] = $availabilities->get($key, collect());
        }
        
        return response()->json($schedule);
    }
} 