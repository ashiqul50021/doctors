@extends('layouts.admin')

@section('title', 'Manage Schedule - ' . $doctor->user->name)

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Manage Schedule: {{ $doctor->user->name }}</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('doctors.admin.doctors.index') }}">Doctors</a></li>
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
                                <!-- Primary Tab Nav -->
                                <ul class="nav nav-tabs nav-tabs-solid nav-justified mb-4" id="adminConsultationTypeTabs" style="border-bottom: 2px solid #f3f4f6;">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#admin_chamber_consultation" style="font-weight: 700; padding: 15px 20px; font-size: 15px;"><i class="fas fa-building me-2"></i>Chamber / Physical Consultation</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#admin_video_consultation" style="font-weight: 700; padding: 15px 20px; font-size: 15px;"><i class="fas fa-video me-2"></i>Online / Video Consultation</a>
                                    </li>
                                </ul>

                                <!-- Primary Tab Content -->
                                <div class="tab-content">
                                    <!-- Chamber / Physical Consultation -->
                                    <div class="tab-pane fade show active" id="admin_chamber_consultation">
                                        <div class="card schedule-widget mb-0">
                                            <div class="schedule-header">
                                                <div class="schedule-nav">
                                                    <ul class="nav nav-tabs nav-tabs-solid nav-justified">
                                                        @foreach(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                                            <li class="nav-item">
                                                                <a class="nav-link {{ $day === 'monday' ? 'active' : '' }}" data-bs-toggle="tab"
                                                                    href="#admin_offline_slot_{{ $day }}">{{ ucfirst($day) }}</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="tab-content schedule-cont">
                                                @foreach(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                                    <div id="admin_offline_slot_{{ $day }}"
                                                        class="tab-pane fade {{ $day === 'monday' ? 'show active' : '' }}">
                                                        <h4 class="card-title d-flex justify-content-between">
                                                            <span>Chamber Time Slots</span>
                                                            <a class="edit-link" data-bs-toggle="modal" href="#add_time_slot"
                                                                onclick="setDay('{{ $day }}', 'offline')">
                                                                <i class="fa fa-plus-circle"></i> Add Slot
                                                            </a>
                                                        </h4>

                                                        @if(isset($groupedSchedules['offline'][$day]) && count($groupedSchedules['offline'][$day]) > 0)
                                                            <div class="doc-times">
                                                                @foreach($groupedSchedules['offline'][$day] as $schedule)
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
                                        </div>
                                    </div>

                                    <!-- Online / Video Consultation -->
                                    <div class="tab-pane fade" id="admin_video_consultation">
                                        <div class="card schedule-widget mb-0">
                                            <div class="schedule-header">
                                                <div class="schedule-nav">
                                                    <ul class="nav nav-tabs nav-tabs-solid nav-justified">
                                                        @foreach(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                                            <li class="nav-item">
                                                                <a class="nav-link {{ $day === 'monday' ? 'active' : '' }}" data-bs-toggle="tab"
                                                                    href="#admin_online_slot_{{ $day }}">{{ ucfirst($day) }}</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="tab-content schedule-cont">
                                                @foreach(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                                    <div id="admin_online_slot_{{ $day }}"
                                                        class="tab-pane fade {{ $day === 'monday' ? 'show active' : '' }}">
                                                        <h4 class="card-title d-flex justify-content-between">
                                                            <span>Video Time Slots</span>
                                                            <a class="edit-link" data-bs-toggle="modal" href="#add_time_slot"
                                                                onclick="setDay('{{ $day }}', 'online')">
                                                                <i class="fa fa-plus-circle"></i> Add Slot
                                                            </a>
                                                        </h4>

                                                        @if(isset($groupedSchedules['online'][$day]) && count($groupedSchedules['online'][$day]) > 0)
                                                            <div class="doc-times">
                                                                @foreach($groupedSchedules['online'][$day] as $schedule)
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
                                        </div>
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
                        <input type="hidden" name="type" id="selected_type">
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
        function setDay(day, type) {
            document.getElementById('selected_day').value = day;
            document.getElementById('selected_type').value = type;
        }
    </script>
@endsection