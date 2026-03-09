<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Schedule;

class DoctorScheduleController extends Controller
{
    /**
     * Show the schedule management page for a specific doctor.
     */
    public function edit(Doctor $doctor)
    {
        $schedules = Schedule::where('doctor_id', $doctor->id)->get();
        $groupedSchedules = [];

        foreach ($schedules as $schedule) {
            $groupedSchedules[$schedule->day][] = $schedule;
        }

        return view('admin.doctors.schedule', compact('doctor', 'groupedSchedules'));
    }

    /**
     * Store/Add a new slot for the doctor.
     */
    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'day' => 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'required',
            'end_time' => 'required',
            'slot_duration' => 'nullable|integer',
        ]);

        // Parse 12-hour format to 24-hour if sent formatted, or assume H:i from time input
        $startTime = \Carbon\Carbon::parse($request->start_time)->format('H:i:s');
        $endTime = \Carbon\Carbon::parse($request->end_time)->format('H:i:s');

        if ($startTime >= $endTime) {
            return back()->withErrors(['start_time' => 'Start time must be before end time.']);
        }

        Schedule::create([
            'doctor_id' => $doctor->id,
            'day' => $request->day,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'slot_duration' => $request->slot_duration ?? 30,
        ]);

        return back()->with('success', 'Schedule added successfully!');
    }

    /**
     * Remove the specified slot.
     */
    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return back()->with('success', 'Schedule deleted successfully!');
    }
}
