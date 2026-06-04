<div class="profile-sidebar">
    <div class="widget-profile pro-widget-content">
        <div class="profile-info-widget">
            <a href="#" class="booking-doc-img">
                <img src="{{ Auth::user()->patient && Auth::user()->patient->profile_image ? asset(Auth::user()->patient->profile_image) : asset('assets/img/patients/patient.jpg') }}" alt="User Image">
            </a>
            <div class="profile-det-info">
                <h3>{{ Auth::user()->name }}</h3>
                <div class="patient-details">
                    @if(Auth::user()->patient && Auth::user()->patient->date_of_birth)
                        <h5><i class="fas fa-birthday-cake"></i> {{ \Carbon\Carbon::parse(Auth::user()->patient->date_of_birth)->format('d M Y') }}, {{ \Carbon\Carbon::parse(Auth::user()->patient->date_of_birth)->age }} years</h5>
                    @else
                        <h5><i class="fas fa-birthday-cake"></i> Date of Birth not set</h5>
                    @endif
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> 
                        @if(Auth::user()->patient && (Auth::user()->patient->city || Auth::user()->patient->country))
                            {{ trim((Auth::user()->patient->city ?? '') . (Auth::user()->patient->country ? ', ' . Auth::user()->patient->country : '')) }}
                        @else
                            Location not set
                        @endif
                    </h5>
                </div>
            </div>
        </div>
    </div>
    <div class="dashboard-widget">
        <nav class="dashboard-menu">
            <ul>
                <li class="{{ request()->routeIs('patient.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('patient.dashboard') }}">
                        <i class="fas fa-columns"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('patient.favourites') ? 'active' : '' }}">
                    <a href="{{ route('patient.favourites') }}">
                        <i class="fas fa-bookmark"></i>
                        <span>Favourites</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('chat') ? 'active' : '' }}">
                    <a href="{{ route('chat') }}">
                        <i class="fas fa-comments"></i>
                        <span>Message</span>
                        @php
                            $unreadCount = \App\Models\Message::where('receiver_id', Auth::id())->where('is_read', false)->count();
                        @endphp
                        @if($unreadCount > 0)
                            <small class="unread-msg">{{ $unreadCount }}</small>
                        @endif
                    </a>
                </li>
                <li class="{{ request()->routeIs('patient.profile.settings') ? 'active' : '' }}">
                    <a href="{{ route('patient.profile.settings') }}">
                        <i class="fas fa-user-cog"></i>
                        <span>Profile Settings</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('patient.change.password') ? 'active' : '' }}">
                    <a href="{{ route('patient.change.password') }}">
                        <i class="fas fa-lock"></i>
                        <span>Change Password</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</div>
