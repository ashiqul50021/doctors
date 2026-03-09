{{-- Doctor Sidebar Partial --}}
{{-- Expects $doctor variable (with user, speciality relations loaded) --}}
{{-- Active route is determined automatically --}}

@push('styles')
<style>
    /* Sidebar Styling */
    .profile-sidebar {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        border: none;
        overflow: hidden;
        margin-bottom: 30px;
        padding-bottom: 1px;
    }

    .profile-sidebar .profile-info-widget {
        padding: 30px 20px;
        text-align: center;
        border-bottom: 1px solid #f0f0f0;
        background: linear-gradient(180deg, rgba(29, 78, 216, 0.03) 0%, rgba(255, 255, 255, 0) 100%);
    }

    .profile-sidebar .booking-doc-img {
        display: inline-block;
        margin-bottom: 15px;
        position: relative;
    }

    .profile-sidebar .booking-doc-img img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        object-fit: cover;
    }

    .profile-sidebar .profile-det-info h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #272b41;
        margin-bottom: 5px;
    }

    .profile-sidebar .patient-details h5 {
        font-size: 0.9rem;
        color: #757575;
        font-weight: 500;
    }

    /* Sidebar Menu */
    .dashboard-menu { padding: 15px 0; }
    .dashboard-menu ul { list-style: none; padding: 0; margin: 0; }

    .dashboard-menu ul li a {
        display: flex;
        align-items: center;
        padding: 14px 25px;
        color: #4b5563;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        border-left: 3px solid transparent;
        font-size: 0.95rem;
    }

    .dashboard-menu ul li a i {
        font-family: "Font Awesome 5 Free", "FontAwesome", sans-serif !important;
        font-weight: 900 !important;
        font-size: 1.1rem;
        width: 28px;
        margin-right: 15px;
        color: #9ca3af;
        text-align: center;
        transition: all 0.3s ease;
    }

    .dashboard-menu ul li a span { flex: 1; }

    .dashboard-menu ul li a:hover {
        background-color: rgba(29, 78, 216, 0.04);
        color: #1D4ED8 !important;
        border-left-color: transparent;
    }
    .dashboard-menu ul li a:hover i { color: #1D4ED8 !important; }

    .dashboard-menu ul li.active a {
        background-color: rgba(29, 78, 216, 0.08);
        color: #1D4ED8 !important;
        border-left-color: #1D4ED8;
        border-radius: 0 8px 8px 0;
        margin-right: 12px;
        font-weight: 600;
    }
    .dashboard-menu ul li.active a i { color: #1D4ED8 !important; }

    .dashboard-menu ul li a .unread-msg {
        background-color: #f73563;
        color: #fff;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
    }
</style>
@endpush

<div class="profile-sidebar">
    @php
        $doctorImage = asset('assets/img/doctors/doctor-thumb-02.jpg');
        if (!empty($doctor->profile_image)) {
            $doctorImage = str_starts_with($doctor->profile_image, 'uploads/')
                ? asset($doctor->profile_image)
                : asset('storage/' . $doctor->profile_image);
        }
    @endphp
    <div class="widget-profile pro-widget-content">
        <div class="profile-info-widget">
            <a href="#" class="booking-doc-img">
                <img src="{{ $doctorImage }}" alt="User Image">
            </a>
            <div class="profile-det-info">
                <h3>{{ $doctor->user->name ?? 'Doctor' }}</h3>
                <div class="patient-details">
                    <h5 class="mb-0">{{ $doctor->qualification ?? 'MBBS, MD' }} - {{ $doctor->speciality->name ?? 'General' }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="dashboard-widget">
        <nav class="dashboard-menu">
            <ul>
                <li class="{{ request()->routeIs('doctors.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('doctors.dashboard') }}">
                        <i class="fas fa-columns"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('doctors.appointments') ? 'active' : '' }}">
                    <a href="{{ route('doctors.appointments') }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Appointments</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('doctors.my.patients') ? 'active' : '' }}">
                    <a href="{{ route('doctors.my.patients') }}">
                        <i class="fas fa-user-injured"></i>
                        <span>My Patients</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('doctors.schedule.timings') ? 'active' : '' }}">
                    <a href="{{ route('doctors.schedule.timings') }}">
                        <i class="fas fa-hourglass-start"></i>
                        <span>Schedule Timings</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('doctors.invoices') ? 'active' : '' }}">
                    <a href="{{ route('doctors.invoices') }}">
                        <i class="fas fa-file-invoice"></i>
                        <span>Invoices</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('doctors.reviews') ? 'active' : '' }}">
                    <a href="{{ route('doctors.reviews') }}">
                        <i class="fas fa-star"></i>
                        <span>Reviews</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('chat.doctor') ? 'active' : '' }}">
                    <a href="{{ route('chat.doctor') }}">
                        <i class="fas fa-comments"></i>
                        <span>Message</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('doctors.profile.settings') ? 'active' : '' }}">
                    <a href="{{ route('doctors.profile.settings') }}">
                        <i class="fas fa-user-cog"></i>
                        <span>Profile Settings</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('doctors.social.media') ? 'active' : '' }}">
                    <a href="{{ route('doctors.social.media') }}">
                        <i class="fas fa-share-alt"></i>
                        <span>Social Media</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('doctors.change.password') ? 'active' : '' }}">
                    <a href="{{ route('doctors.change.password') }}">
                        <i class="fas fa-lock"></i>
                        <span>Change Password</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                    <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </li>
            </ul>
        </nav>
    </div>
</div>
