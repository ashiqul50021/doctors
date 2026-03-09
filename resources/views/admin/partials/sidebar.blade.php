<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main</span>
                </li>
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}"><i class="fe fe-home"></i> <span>Dashboard</span></a>
                </li>

                <li class="menu-title">
                    <span>Doctors</span>
                </li>
                <li class="{{ request()->routeIs('admin.appointments') ? 'active' : '' }}">
                    <a href="{{ route('admin.appointments') }}"><i class="fe fe-layout"></i>
                        <span>Appointments</span></a>
                </li>
                <li class="{{ request()->routeIs('doctors.admin.specialities.*') ? 'active' : '' }}">
                    <a href="{{ route('doctors.admin.specialities.index') }}"><i class="fe fe-users"></i>
                        <span>Specialities</span></a>
                </li>
                <li class="{{ request()->routeIs('doctors.admin.doctors.*') ? 'active' : '' }}">
                    <a href="{{ route('doctors.admin.doctors.index') }}"><i class="fe fe-user-plus"></i>
                        <span>Doctors</span></a>
                </li>
                <li class="{{ request()->routeIs('admin.patients') ? 'active' : '' }}">
                    <a href="{{ route('admin.patients') }}"><i class="fe fe-user"></i> <span>Patients</span></a>
                </li>
                <li class="{{ request()->routeIs('admin.reviews') ? 'active' : '' }}">
                    <a href="{{ route('admin.reviews') }}"><i class="fe fe-star-o"></i> <span>Reviews</span></a>
                </li>

                <li class="menu-title">
                    <span>Ecommerce</span>
                </li>
                <li class="{{ request()->routeIs('ecommerce.admin.product-categories.*') ? 'active' : '' }}">
                    <a href="{{ route('ecommerce.admin.product-categories.index') }}"><i class="fe fe-layout"></i> <span>Product
                            Categories</span></a>
                </li>
                <li class="{{ request()->routeIs('ecommerce.admin.products.*') ? 'active' : '' }}">
                    <a href="{{ route('ecommerce.admin.products.index') }}"><i class="fe fe-shopping-cart"></i>
                        <span>Products</span></a>
                </li>
                <li class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.orders.index') }}"><i class="fe fe-cart"></i> <span>Orders</span></a>
                </li>
                <li class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.coupons.index') }}"><i class="fe fe-star"></i> <span>Coupons</span></a>
                </li>
                <li class="{{ request()->routeIs('admin.health-packages.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.health-packages.index') }}"><i class="fe fe-heart"></i>
                        <span>Health Packages</span></a>
                </li>
                @if(Route::has('admin.advertisements.index'))
                    <li class="{{ request()->routeIs('admin.advertisements.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.advertisements.index') }}"><i class="fe fe-image"></i>
                            <span>Advertisements</span></a>
                    </li>
                @endif
                <li class="{{ request()->routeIs('admin.transactions') ? 'active' : '' }}">
                    <a href="{{ route('admin.transactions') }}"><i class="fe fe-activity"></i>
                        <span>Transactions</span></a>
                </li>

                <li class="menu-title">
                    <span>Courses</span>
                </li>
                <li class="{{ request()->routeIs('courses.admin.courses.*') ? 'active' : '' }}">
                    <a href="{{ route('courses.admin.courses.index') }}"><i class="fe fe-book-open"></i> <span>Courses
                            List</span></a>
                </li>

                <li class="menu-title">
                    <span>Settings</span>
                </li>
                <li class="{{ request()->routeIs('admin.site-settings.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.site-settings.index') }}"><i class="fe fe-settings"></i> <span>Site
                            Settings</span></a>
                </li>
                <li class="{{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.menus.index') }}"><i class="fe fe-list"></i> <span>Menu Manager</span></a>
                </li>
                <li class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.banners.index') }}"><i class="fe fe-star"></i> <span>Banners</span></a>
                </li>
                <li class="{{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                    <a href="{{ route('admin.profile') }}"><i class="fe fe-user-plus"></i> <span>Profile</span></a>
                </li>

                <li class="submenu">
                    <a href="#"><i class="fe fe-document"></i> <span> Reports</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a href="{{ route('admin.invoice.report') }}">Invoice Reports</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
