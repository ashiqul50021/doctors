<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        $doctor = Auth::user()->doctor;
        if (!$doctor) {
            return redirect()->route('login');
        }
        $doctor->load(['user', 'speciality']);

        $schedules = Schedule::where('doctor_id', $doctor->id)->get();
        $groupedSchedules = [];

        foreach ($schedules as $schedule) {
            $groupedSchedules[$schedule->day][] = $schedule;
        }

        // Load upcoming off days (today onwards)
        $offDays = \App\Models\DoctorOffDay::where('doctor_id', $doctor->id)
            ->where('off_date', '>=', today())
            ->orderBy('off_date')
            ->get();

        return view('frontend.schedule-timings', compact('doctor', 'groupedSchedules', 'offDays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'day' => 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'required', // Format check can be added
            'end_time' => 'required',
            'slot_duration' => 'nullable|integer',
        ]);

        $doctor = Auth::user()->doctor;

        // Parse 12-hour format to 24-hour
        $startTime = \Carbon\Carbon::parse($request->start_time)->format('H:i:s');
        $endTime = \Carbon\Carbon::parse($request->end_time)->format('H:i:s');

        // Validation: Start < End
        if ($startTime >= $endTime) {
            return back()->withErrors(['start_time' => 'Start time must be before end time.']);
        }

        // Logic: Add logic to check overlap if needed, for now simple add
        Schedule::create([
            'doctor_id' => $doctor->id,
            'day' => $request->day,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'slot_duration' => $request->slot_duration ?? 30,
        ]);

        return back()->with('success', 'Schedule added successfully!');
    }

    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);

        if ($schedule->doctor_id !== Auth::user()->doctor->id) {
            abort(403);
        }

        $schedule->delete();

        return back()->with('success', 'Schedule deleted successfully!');
    }
}
