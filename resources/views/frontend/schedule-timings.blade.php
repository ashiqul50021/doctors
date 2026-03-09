@extends('layouts.app')

@section('title', 'Schedule Timings - Doccure')

@push('styles')
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
@endpush

@section('content')

    <!-- Page Content -->
    <div class="content">
        <div class="container">

            <div class="row">
                <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">

                    @include('frontend.includes.doctor-sidebar')

                </div>

                <div class="col-md-7 col-lg-8 col-xl-9">

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Schedule Timings</h4>
                                    <div class="profile-box">
                                        <div class="row">

                                            <div class="col-lg-4">
                                                <div class="mb-3">
                                                    <label>Timing Slot Duration</label>
                                                    <select class="select form-control">
                                                        <option>-</option>
                                                        <option>15 mins</option>
                                                        <option selected="selected">30 mins</option>
                                                        <option>45 mins</option>
                                                        <option>1 Hour</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card schedule-widget mb-0">

                                                    <!-- Schedule Header -->
                                                    <div class="schedule-header">

                                                        <!-- Schedule Nav -->
                                                        <div class="schedule-nav">
                                                            <ul class="nav nav-tabs nav-justified">
                                                                <li class="nav-item">
                                                                    <a class="nav-link" data-bs-toggle="tab"
                                                                        href="#slot_sunday">Sunday</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link active" data-bs-toggle="tab"
                                                                        href="#slot_monday">Monday</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link" data-bs-toggle="tab"
                                                                        href="#slot_tuesday">Tuesday</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link" data-bs-toggle="tab"
                                                                        href="#slot_wednesday">Wednesday</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link" data-bs-toggle="tab"
                                                                        href="#slot_thursday">Thursday</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link" data-bs-toggle="tab"
                                                                        href="#slot_friday">Friday</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link" data-bs-toggle="tab"
                                                                        href="#slot_saturday">Saturday</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <!-- /Schedule Nav -->

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
                                                                    <a class="edit-link" data-bs-toggle="modal"
                                                                        href="#add_time_slot" onclick="setDay('{{ $day }}')">
                                                                        <i class="fa fa-plus-circle"></i> Add Slot
                                                                    </a>
                                                                </h4>

                                                                @if(isset($groupedSchedules[$day]) && count($groupedSchedules[$day]) > 0)
                                                                    <div class="doc-times">
                                                                        @foreach($groupedSchedules[$day] as $schedule)
                                                                            <div class="doc-slot-list">
                                                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i a') }}
                                                                                -
                                                                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i a') }}
                                                                                <form
                                                                                    action="{{ route('doctors.schedule.destroy', $schedule->id) }}"
                                                                                    method="POST" style="display:inline;">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="delete_schedule"
                                                                                        style="border:none; background:none;">
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
            </div>

        </div>

    </div>
    <!-- /Page Content -->

    <!-- Add Time Slot Modal -->
    <div class="modal fade custom-modal" id="add_time_slot">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Time Slots</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('doctors.schedule.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="day" id="selected_day">
                        <div class="hours-info">
                            <div class="row form-row hours-cont">
                                <div class="col-12 col-md-10">
                                    <div class="row form-row">
                                        <div class="col-12 col-md-6">
                                            <div class="mb-3">
                                                <label>Start Time</label>
                                                <input type="time" name="start_time" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="mb-3">
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

    <!-- Edit Time Slot Modal -->
    <div class="modal fade custom-modal" id="edit_time_slot">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Time Slots</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="hours-info">
                            <div class="row form-row hours-cont">
                                <div class="col-12 col-md-10">
                                    <div class="row form-row">
                                        <div class="col-12 col-md-6">
                                            <div class="mb-3">
                                                <label>Start Time</label>
                                                <select class="form-control">
                                                    <option>-</option>
                                                    <option selected>12.00 am</option>
                                                    <option>12.30 am</option>
                                                    <option>1.00 am</option>
                                                    <option>1.30 am</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="mb-3">
                                                <label>End Time</label>
                                                <select class="form-control">
                                                    <option>-</option>
                                                    <option>12.00 am</option>
                                                    <option selected>12.30 am</option>
                                                    <option>1.00 am</option>
                                                    <option>1.30 am</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row form-row hours-cont">
                                <div class="col-12 col-md-10">
                                    <div class="row form-row">
                                        <div class="col-12 col-md-6">
                                            <div class="mb-3">
                                                <label>Start Time</label>
                                                <select class="form-control">
                                                    <option>-</option>
                                                    <option>12.00 am</option>
                                                    <option selected>12.30 am</option>
                                                    <option>1.00 am</option>
                                                    <option>1.30 am</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="mb-3">
                                                <label>End Time</label>
                                                <select class="form-control">
                                                    <option>-</option>
                                                    <option>12.00 am</option>
                                                    <option>12.30 am</option>
                                                    <option selected>1.00 am</option>
                                                    <option>1.30 am</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-2"><label class="d-md-block d-sm-none d-none">&nbsp;</label><a
                                        href="#" class="btn btn-danger trash"><i class="far fa-trash-alt"></i></a></div>
                            </div>

                            <div class="row form-row hours-cont">
                                <div class="col-12 col-md-10">
                                    <div class="row form-row">
                                        <div class="col-12 col-md-6">
                                            <div class="mb-3">
                                                <label>Start Time</label>
                                                <select class="form-control">
                                                    <option>-</option>
                                                    <option>12.00 am</option>
                                                    <option>12.30 am</option>
                                                    <option selected>1.00 am</option>
                                                    <option>1.30 am</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="mb-3">
                                                <label>End Time</label>
                                                <select class="form-control">
                                                    <option>-</option>
                                                    <option>12.00 am</option>
                                                    <option>12.30 am</option>
                                                    <option>1.00 am</option>
                                                    <option selected>1.30 am</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-2"><label class="d-md-block d-sm-none d-none">&nbsp;</label><a
                                        href="#" class="btn btn-danger trash"><i class="far fa-trash-alt"></i></a></div>
                            </div>

                        </div>

                        <div class="add-more mb-3">
                            <a href="javascript:void(0);" class="add-hours"><i class="fa fa-plus-circle"></i> Add More</a>
                        </div>
                        <div class="submit-section text-center">
                            <button type="submit" class="btn btn-primary submit-btn">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Edit Time Slot Modal -->
@endsection

@push('scripts')
    <!-- Sticky Sidebar JS -->
    <script src="{{ asset('assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
    <script src="{{ asset('assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>

    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

    <script>
        function setDay(day) {
            document.getElementById('selected_day').value = day;
        }
    </script>
@endpush
