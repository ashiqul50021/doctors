{{--
    Weekly schedule picker used in the doctor profile wizard.
    Params: $type ('offline' | 'online'), $title, $icon, $schedules (that doctor's schedules of this type)
    Sends: schedule[$type][days][], schedule[$type][from], schedule[$type][to], schedule[$type][changed]
--}}
@php
    $weekDays = ['saturday' => 'Sat', 'sunday' => 'Sun', 'monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri'];

    $oldBlock = old("schedule.$type");
    $selectedDays = $oldBlock ? (array) ($oldBlock['days'] ?? []) : $schedules->pluck('day')->unique()->values()->all();
    $firstSchedule = $schedules->first();
    $fromValue = $oldBlock['from'] ?? ($firstSchedule ? substr($firstSchedule->start_time, 0, 5) : '16:00');
    $toValue = $oldBlock['to'] ?? ($firstSchedule ? substr($firstSchedule->end_time, 0, 5) : '20:00');
    $hasMixedTimes = $schedules->map(fn ($s) => $s->start_time . '-' . $s->end_time)->unique()->count() > 1;

    // 30-minute options from 06:00 AM to 11:30 PM
    $timeOptions = [];
    for ($m = 6 * 60; $m <= 23 * 60 + 30; $m += 30) {
        $value = sprintf('%02d:%02d', intdiv($m, 60), $m % 60);
        $timeOptions[$value] = \Carbon\Carbon::createFromFormat('H:i', $value)->format('h:i A');
    }
    if (!isset($timeOptions[$fromValue])) { $timeOptions[$fromValue] = \Carbon\Carbon::createFromFormat('H:i', $fromValue)->format('h:i A'); }
    if (!isset($timeOptions[$toValue])) { $timeOptions[$toValue] = \Carbon\Carbon::createFromFormat('H:i', $toValue)->format('h:i A'); }
    ksort($timeOptions);
@endphp

<div class="schedule-picker border rounded p-3 mb-3 bg-white" data-schedule-type="{{ $type }}">
    <input type="hidden" name="schedule[{{ $type }}][changed]" value="{{ $oldBlock['changed'] ?? 0 }}" class="schedule-changed">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <label class="form-label fw-semibold mb-0"><i class="{{ $icon }} text-primary me-1"></i> {{ $title }}</label>
        <small class="text-muted">Patients will see 20-minute appointment slots in this time.</small>
    </div>

    @if($hasMixedTimes)
        <div class="alert alert-warning py-2 small mb-2">
            You have different times on different days (set from Schedule Timings). Changing anything here will replace them with the single time below.
        </div>
    @endif

    <div class="mb-2">
        <span class="small fw-semibold text-muted d-block mb-1">Days</span>
        <div class="d-flex flex-wrap gap-2">
            <label class="schedule-day-chip schedule-all-chip">
                <input type="checkbox" class="schedule-all-days" {{ count(array_intersect(array_keys($weekDays), $selectedDays)) === 7 ? 'checked' : '' }}>
                <span>All Days</span>
            </label>
            @foreach($weekDays as $dayKey => $dayLabel)
                <label class="schedule-day-chip">
                    <input type="checkbox" class="schedule-day" name="schedule[{{ $type }}][days][]" value="{{ $dayKey }}" {{ in_array($dayKey, $selectedDays, true) ? 'checked' : '' }}>
                    <span>{{ $dayLabel }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="row g-2 align-items-end">
        <div class="col-6 col-md-4">
            <label class="small fw-semibold text-muted mb-1">From</label>
            <select class="form-select schedule-from" name="schedule[{{ $type }}][from]">
                @foreach($timeOptions as $value => $label)
                    <option value="{{ $value }}" {{ $value === $fromValue ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-4">
            <label class="small fw-semibold text-muted mb-1">To</label>
            <select class="form-select schedule-to" name="schedule[{{ $type }}][to]">
                @foreach($timeOptions as $value => $label)
                    <option value="{{ $value }}" {{ $value === $toValue ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-4">
            <div class="small text-primary fw-semibold schedule-summary"></div>
        </div>
    </div>
    @error("schedule.$type.to") <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>
