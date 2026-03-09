@extends('layouts.admin')

@section('title', 'Manage Schedule - ' . $doctor->user->name)

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Manage Schedule: {{ $doctor->user->name }}</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.doctors.index') }}">Doctors</a></li>
                    <li class="breadcrumb-item active">Schedule</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Schedule Timings</h4>
                    <div class="profile-box">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card schedule-widget mb-0">
                                    <div class="schedule-header">
                                        <!-- Schedule Header -->
                                        <div class="schedule-nav">
                                            <ul class="nav nav-tabs nav-tabs-solid nav-justified" id="adminScheduleTab">
                                                @foreach(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $key => $day)
                                                    <li class="nav-item">
                                                        <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                            data-bs-toggle="tab" href="#slot_{{ $day }}">{{ ucfirst($day) }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <!-- /Schedule Header -->

                                        <!-- Schedule Content -->
                                        <div class="tab-content schedule-cont">
                                            @php
                                                $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                                            @endphp

                                            @foreach($days as $day)
                                                <div id="slot_{{ $day }}"
                                                    class="tab-pane fade {{ $loop->first ? 'show active' : '' }}">
                                                    <h4 class="card-title d-flex justify-content-between">
                                                        <span>Time Slots</span>
                                                        <a class="edit-link" data-bs-toggle="modal" href="#add_time_slot"
                                                            onclick="setDay('{{ $day }}')">
                                                            <i class="fa fa-plus-circle"></i> Add Slot
                                                        </a>
                                                    </h4>

                                                    @if(isset($groupedSchedules[$day]) && count($groupedSchedules[$day]) > 0)
                                                        <div class="doc-times">
                                                            @foreach($groupedSchedules[$day] as $schedule)
                                                                <div class="doc-slot-list"
                                                                    style="display: inline-block; background: #e9e9e9; padding: 5px 10px; border-radius: 4px; margin: 5px;">
                                                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i a') }}
                                                                    -
                                                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i a') }}
                                                                    <form
                                                                        action="{{ route('admin.doctors.schedule.destroy', $schedule->id) }}"
                                                                        method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="delete_schedule"
                                                                            style="border:none; background:none; color: red;">
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <p class="text-muted mb-0">Not Available</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        <!-- /Schedule Content -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Time Slot Modal -->
    <div class="modal fade custom-modal" id="add_time_slot">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Time Slots</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.doctors.schedule.update', $doctor->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="day" id="selected_day">
                        <div class="hours-info">
                            <div class="row form-row hours-cont">
                                <div class="col-12">
                                    <div class="row form-row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>Start Time</label>
                                                <input type="time" name="start_time" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>End Time</label>
                                                <input type="time" name="end_time" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="submit-section text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Add Time Slot Modal -->

@endsection

@section('scripts')
    <script>
        function setDay(day) {
            document.getElementById('selected_day').value = day;
        }
    </script>
@endsection