@extends('layouts.app')

@section('title', 'Appointments - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@push('styles')
<style>
    .appt-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 1.5rem;
    }
    .appt-section-header h4 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a2e44;
    }
    /* ── Filter bar ─────────────────────────────── */
    .filter-bar {
        background: #fff;
        border: 1px solid #e8edf2;
        border-radius: 12px;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
    }
    .filter-bar label {
        font-weight: 600;
        font-size: .85rem;
        color: #5a6a85;
        margin: 0;
        white-space: nowrap;
    }
    .filter-bar input[type="date"] {
        border: 1px solid #d0d7e2;
        border-radius: 8px;
        padding: 7px 12px;
        font-size: .9rem;
        color: #1a2e44;
        outline: none;
        transition: border-color .2s;
    }
    .filter-bar input[type="date"]:focus { border-color: #20c0f3; }
    .filter-bar .btn-filter {
        background: #20c0f3;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 7px 18px;
        font-size: .875rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s;
    }
    .filter-bar .btn-filter:hover { background: #17aad8; }
    .filter-bar .btn-clear {
        background: #f1f5f9;
        color: #5a6a85;
        border: 1px solid #d0d7e2;
        border-radius: 8px;
        padding: 7px 16px;
        font-size: .875rem;
        font-weight: 600;
        text-decoration: none;
        transition: background .2s;
    }
    .filter-bar .btn-clear:hover { background: #e2e8f0; }

    /* ── Print block ────────────────────────────── */
    .print-card {
        background: linear-gradient(135deg, #1a2e44 0%, #0f6b8f 100%);
        border-radius: 14px;
        padding: 20px 24px;
        color: #fff;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 16px rgba(26,46,68,.25);
    }
    .print-card .print-icon {
        font-size: 2rem;
        opacity: .85;
    }
    .print-card .print-label { flex: 1; min-width: 180px; }
    .print-card .print-label h5 { margin: 0 0 2px; font-size: 1rem; font-weight: 700; }
    .print-card .print-label p  { margin: 0; font-size: .82rem; opacity: .8; }
    .print-card input[type="date"],
    .print-card select {
        border: 1px solid rgba(255,255,255,.4);
        background: rgba(255,255,255,.15);
        color: #fff;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: .875rem;
        outline: none;
        transition: background .2s;
    }
    .print-card input[type="date"]::placeholder { color: rgba(255,255,255,.7); }
    .print-card input[type="date"]:focus,
    .print-card select:focus { background: rgba(255,255,255,.25); }
    .print-card select option { color: #1a2e44; background: #fff; }
    .print-card .btn-print {
        background: #fff;
        color: #1a2e44;
        border: none;
        border-radius: 8px;
        padding: 9px 22px;
        font-size: .9rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 7px;
        transition: transform .15s, box-shadow .15s;
        white-space: nowrap;
    }
    .print-card .btn-print:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.2); }

    /* ── Tabs ───────────────────────────────────── */
    .appt-tabs {
        display: flex;
        gap: 0;
        border-bottom: 2px solid #e8edf2;
        margin-bottom: 1.25rem;
    }
    .appt-tab-btn {
        background: none;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 10px 22px;
        font-size: .93rem;
        font-weight: 600;
        color: #7a8ea8;
        cursor: pointer;
        transition: color .2s, border-color .2s;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: -2px;
    }
    .appt-tab-btn.active { color: #20c0f3; border-bottom-color: #20c0f3; }
    .appt-tab-btn .badge-count {
        background: #e8f8fd;
        color: #20c0f3;
        border-radius: 20px;
        padding: 1px 9px;
        font-size: .75rem;
        font-weight: 700;
    }
    .appt-tab-btn.active .badge-count { background: #20c0f3; color: #fff; }
    .tab-pane-appt { display: none; }
    .tab-pane-appt.active { display: block; }

    /* ── Appointment list items ─────────────────── */
    .appointment-list {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        background: #fff;
        border: 1px solid #e8edf2;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 12px;
        transition: box-shadow .2s, transform .15s;
    }
    .appointment-list:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); transform: translateY(-1px); }
    .profile-info-widget { display: flex; align-items: center; gap: 14px; flex: 1; min-width: 220px; }
    .booking-doc-img img { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; border: 2px solid #e8edf2; }
    .profile-det-info h3 { font-size: 1rem; font-weight: 700; margin: 0 0 4px; color: #1a2e44; }
    .patient-details h5 { font-size: .82rem; color: #5a6a85; margin: 0 0 2px; display: flex; align-items: center; gap: 5px; }
    .appt-type-badge {
        font-size: .72rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 4px;
    }
    .appt-type-badge.online  { background: #e8f8fd; color: #0097c4; }
    .appt-type-badge.offline { background: #fff3e0; color: #e65100; }
    .appointment-action { display: flex; align-items: center; flex-wrap: wrap; gap: 6px; }
    .appointment-action .btn { font-size: .8rem; padding: 5px 12px; border-radius: 6px; }
    .empty-state { text-align: center; padding: 40px 20px; color: #a0aec0; }
    .empty-state i { font-size: 2.5rem; margin-bottom: 12px; display: block; }
    .empty-state p { font-size: .95rem; margin: 0; }
    .token-badge {
        background: #fef3c7;
        color: #92400e;
        border-radius: 6px;
        padding: 2px 9px;
        font-size: .75rem;
        font-weight: 700;
        margin-left: 6px;
    }
</style>
@endpush

@section('content')
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                @include('frontend.includes.doctor-sidebar')
            </div>

            <div class="col-md-7 col-lg-8 col-xl-9">

                <div class="appt-section-header">
                    <h4><i class="fas fa-calendar-check me-2" style="color:#20c0f3"></i>Appointments</h4>
                </div>

                {{-- ── Date Filter ─────────────────────────────── --}}
                <form method="GET" action="{{ route('doctors.appointments') }}">
                    <div class="filter-bar">
                        <label for="filter_date"><i class="fas fa-filter me-1"></i> তারিখ ফিল্টার:</label>
                        <input type="date" name="filter_date" id="filter_date"
                               value="{{ $filterDate ?? '' }}">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search"></i> দেখান
                        </button>
                        @if($filterDate)
                            <a href="{{ route('doctors.appointments') }}" class="btn-clear">
                                <i class="fas fa-times"></i> ক্লিয়ার
                            </a>
                        @endif
                        @if($filterDate)
                            <span class="ms-auto" style="font-size:.85rem;color:#5a6a85;">
                                ফলাফল: <strong>{{ \Carbon\Carbon::parse($filterDate)->format('d M Y') }}</strong>
                            </span>
                        @endif
                    </div>
                </form>

                {{-- ── Print Card ──────────────────────────────── --}}
                <form method="GET" action="{{ route('doctors.appointments.print') }}" target="_blank">
                    <div class="print-card">
                        <div class="print-icon"><i class="fas fa-print"></i></div>
                        <div class="print-label">
                            <h5>Patient List প্রিন্ট করুন</h5>
                            <p>তারিখ ও ধরন বেছে Serial Number সহ প্রিন্ট করুন</p>
                        </div>
                        <input type="date" name="print_date"
                               value="{{ $filterDate ?? \Carbon\Carbon::today()->format('Y-m-d') }}"
                               required>
                        <select name="print_type">
                            <option value="all">সব (Online + Offline)</option>
                            <option value="offline">Offline মাত্র</option>
                            <option value="online">Online মাত্র</option>
                        </select>
                        <button type="submit" class="btn-print">
                            <i class="fas fa-print"></i> প্রিন্ট
                        </button>
                    </div>
                </form>

                {{-- ── Tabs ────────────────────────────────────── --}}
                <div class="appt-tabs">
                    <button class="appt-tab-btn active" onclick="switchTab('offline', this)">
                        <i class="fas fa-hospital-user"></i> Offline
                        <span class="badge-count">{{ $offlineAppointments->total() }}</span>
                    </button>
                    <button class="appt-tab-btn" onclick="switchTab('online', this)">
                        <i class="fas fa-video"></i> Online
                        <span class="badge-count">{{ $onlineAppointments->total() }}</span>
                    </button>
                </div>

                {{-- ── Offline Tab ─────────────────────────────── --}}
                <div id="tab-offline" class="tab-pane-appt active">
                    @forelse($offlineAppointments as $appointment)
                    <div class="appointment-list">
                        <div class="profile-info-widget">
                            <a href="#" class="booking-doc-img">
                                <img src="{{ optional($appointment->patient)->profile_image ? asset($appointment->patient->profile_image) : asset('assets/img/patients/patient.jpg') }}" alt="Patient">
                            </a>
                            <div class="profile-det-info">
                                <h3>
                                    <span class="appt-type-badge offline"><i class="fas fa-hospital-user"></i> Offline</span>
                                    @if($appointment->token_number)
                                        <span class="token-badge"><i class="fas fa-hashtag"></i> {{ $appointment->token_number }}</span>
                                    @endif
                                </h3>
                                <h3>
                                    <a href="#">{{ optional(optional($appointment->patient)->user)->name ?? 'Unknown Patient' }}</a>
                                </h3>
                                <div class="patient-details">
                                    <h5><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}, {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</h5>
                                    @if(optional(optional($appointment->patient)->user)->email)
                                        <h5><i class="fas fa-envelope"></i> {{ $appointment->patient->user->email }}</h5>
                                    @endif
                                    @if(optional($appointment->patient)->phone)
                                        <h5 class="mb-0"><i class="fas fa-phone"></i> {{ $appointment->patient->phone }}</h5>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="appointment-action">
                            <a href="#" class="btn btn-sm bg-info-light" data-bs-toggle="modal" data-bs-target="#appt_details_{{ $appointment->id }}">
                                <i class="far fa-eye"></i> View
                            </a>
                            @if(in_array($appointment->status, ['confirmed', 'completed']))
                                <a href="{{ route('doctors.add.prescription', $appointment->id) }}" class="btn btn-sm bg-primary-light">
                                    <i class="fas fa-prescription"></i> Prescription
                                </a>
                            @endif
                            @if($appointment->status == 'pending')
                                <form action="{{ route('appointment.accept', $appointment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm bg-success-light"><i class="fas fa-check"></i> Accept</button>
                                </form>
                                <form action="{{ route('appointment.cancel', $appointment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm bg-danger-light"><i class="fas fa-times"></i> Cancel</button>
                                </form>
                            @elseif($appointment->status == 'confirmed')
                                <form action="{{ route('appointment.complete', $appointment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm bg-info-light"><i class="fas fa-check-double"></i> Complete</button>
                                </form>
                                <form action="{{ route('appointment.cancel', $appointment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm bg-danger-light"><i class="fas fa-times"></i> Cancel</button>
                                </form>
                            @else
                                <span class="badge bg-{{ $appointment->status == 'completed' ? 'info' : 'danger' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="fas fa-hospital-user"></i>
                        <p>কোনো Offline appointment নেই।</p>
                    </div>
                    @endforelse
                    <div class="mt-3">{{ $offlineAppointments->appends(request()->query())->links() }}</div>
                </div>

                {{-- ── Online Tab ──────────────────────────────── --}}
                <div id="tab-online" class="tab-pane-appt">
                    @forelse($onlineAppointments as $appointment)
                    <div class="appointment-list">
                        <div class="profile-info-widget">
                            <a href="#" class="booking-doc-img">
                                <img src="{{ optional($appointment->patient)->profile_image ? asset($appointment->patient->profile_image) : asset('assets/img/patients/patient.jpg') }}" alt="Patient">
                            </a>
                            <div class="profile-det-info">
                                <h3>
                                    <span class="appt-type-badge online"><i class="fas fa-video"></i> Online</span>
                                </h3>
                                <h3>
                                    <a href="#">{{ optional(optional($appointment->patient)->user)->name ?? 'Unknown Patient' }}</a>
                                </h3>
                                <div class="patient-details">
                                    <h5><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}, {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</h5>
                                    @if(optional(optional($appointment->patient)->user)->email)
                                        <h5><i class="fas fa-envelope"></i> {{ $appointment->patient->user->email }}</h5>
                                    @endif
                                    @if(optional($appointment->patient)->phone)
                                        <h5 class="mb-0"><i class="fas fa-phone"></i> {{ $appointment->patient->phone }}</h5>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="appointment-action">
                            <a href="#" class="btn btn-sm bg-info-light" data-bs-toggle="modal" data-bs-target="#appt_details_{{ $appointment->id }}">
                                <i class="far fa-eye"></i> View
                            </a>
                            @if(in_array($appointment->status, ['confirmed', 'completed']))
                                <a href="{{ route('doctors.add.prescription', $appointment->id) }}" class="btn btn-sm bg-primary-light">
                                    <i class="fas fa-prescription"></i> Prescription
                                </a>
                            @endif
                            @if($appointment->type === 'online' && $appointment->meeting_link && in_array($appointment->status, ['pending', 'confirmed']))
                                <a href="{{ $appointment->meeting_link }}" target="_blank" class="btn btn-sm bg-success-light">
                                    <i class="fas fa-video"></i> Join Call
                                </a>
                            @endif
                            @if($appointment->status == 'pending')
                                <form action="{{ route('appointment.accept', $appointment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm bg-success-light"><i class="fas fa-check"></i> Accept</button>
                                </form>
                                <form action="{{ route('appointment.cancel', $appointment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm bg-danger-light"><i class="fas fa-times"></i> Cancel</button>
                                </form>
                            @elseif($appointment->status == 'confirmed')
                                <form action="{{ route('appointment.complete', $appointment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm bg-info-light"><i class="fas fa-check-double"></i> Complete</button>
                                </form>
                                <form action="{{ route('appointment.cancel', $appointment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm bg-danger-light"><i class="fas fa-times"></i> Cancel</button>
                                </form>
                            @else
                                <span class="badge bg-{{ $appointment->status == 'completed' ? 'info' : 'danger' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="fas fa-video-slash"></i>
                        <p>কোনো Online appointment নেই।</p>
                    </div>
                    @endforelse
                    <div class="mt-3">{{ $onlineAppointments->appends(request()->query())->links() }}</div>
                </div>

            </div>{{-- /col --}}
        </div>{{-- /row --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(tab, btn) {
    document.querySelectorAll('.tab-pane-appt').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.appt-tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    btn.classList.add('active');
}
</script>
@endpush
