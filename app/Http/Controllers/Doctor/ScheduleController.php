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
        $groupedSchedules = [
            'offline' => [],
            'online' => [],
        ];

        foreach ($schedules as $schedule) {
            $type = $schedule->type ?? 'offline';
            $groupedSchedules[$type][$schedule->day][] = $schedule;
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
            'days' => 'nullable|array',
            'days.*' => 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'day' => 'nullable|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'required',
            'end_time' => 'required',
            'slot_duration' => 'nullable|integer',
            'type' => 'required|in:online,offline',
        ]);

        $doctor = Auth::user()->doctor;

        // Parse 12-hour or 24-hour format
        $startTime = \Carbon\Carbon::parse($request->start_time)->format('H:i:s');
        $endTime = \Carbon\Carbon::parse($request->end_time)->format('H:i:s');

        // Validation: Start < End
        if ($startTime >= $endTime) {
            return back()->withErrors(['start_time' => 'Start time must be before end time.']);
        }

        // Determine target days (multi-select days or single day fallback)
        $targetDays = $request->input('days', []);
        if (empty($targetDays) && $request->filled('day')) {
            $targetDays = [$request->day];
        }

        if (empty($targetDays)) {
            return back()->withErrors(['days' => 'Please select at least one day.']);
        }

        $createdCount = 0;
        foreach ($targetDays as $day) {
            // Check if exact same slot already exists for this day and type
            $exists = Schedule::where('doctor_id', $doctor->id)
                ->where('day', $day)
                ->where('type', $request->type)
                ->where('start_time', $startTime)
                ->where('end_time', $endTime)
                ->exists();

            if (!$exists) {
                Schedule::create([
                    'doctor_id' => $doctor->id,
                    'day' => $day,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'slot_duration' => $request->slot_duration ?? 30,
                    'type' => $request->type,
                ]);
                $createdCount++;
            }
        }

        $message = $createdCount > 1 
            ? "Schedule added for {$createdCount} days successfully!" 
            : 'Schedule added successfully!';

        return back()->with('success', $message);
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
