@push('styles')
<style>
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
        background: linear-gradient(180deg, rgba(52, 92, 206, 0.03) 0%, rgba(255, 255, 255, 0) 100%);
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
        background-color: rgba(52, 92, 206, 0.04);
        color: #345cce !important;
        border-left-color: transparent;
    }
    .dashboard-menu ul li a:hover i { color: #345cce !important; }

    .dashboard-menu ul li.active a {
        background-color: rgba(52, 92, 206, 0.08);
        color: #345cce !important;
        border-left-color: #345cce;
        border-radius: 0 8px 8px 0;
        margin-right: 12px;
        font-weight: 600;
    }
    .dashboard-menu ul li.active a i { color: #345cce !important; }
</style>
@endpush

<div class="profile-sidebar">
    <div class="widget-profile pro-widget-content">
        <div class="profile-info-widget">
            <a href="#" class="booking-doc-img">
                <img src="{{ asset('assets/img/patients/patient.jpg') }}" alt="Agent Photo">
            </a>
            <div class="profile-det-info">
                <h3>{{ Auth::user()->name }}</h3>
                <div class="patient-details">
                    <h5 class="mb-0">Agent Code: <span class="text-primary font-weight-bold">{{ Auth::user()->agent->referral_code }}</span></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="dashboard-widget">
        <nav class="dashboard-menu">
            <ul>
                <li class="{{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('agent.dashboard') }}">
                        <i class="fas fa-columns"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @if (Auth::user()->agent->can_book_appointments)
                    <li class="{{ request()->routeIs('agent.book-appointment') || request()->routeIs('agent.booking') ? 'active' : '' }}">
                        <a href="{{ route('agent.book-appointment') }}">
                            <i class="fas fa-calendar-check"></i>
                            <span>Book Appointment</span>
                        </a>
                    </li>
                @endif
                @if (Auth::user()->agent->can_sell_products)
                    <li class="{{ request()->routeIs('agent.products') || request()->routeIs('agent.cart') || request()->routeIs('agent.checkout') ? 'active' : '' }}">
                        <a href="{{ route('agent.products') }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Order Products</span>
                        </a>
                    </li>
                @endif
                <li class="{{ request()->routeIs('agent.wallet') ? 'active' : '' }}">
                    <a href="{{ route('agent.wallet') }}">
                        <i class="fas fa-wallet"></i>
                        <span>Wallet & Payouts</span>
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
