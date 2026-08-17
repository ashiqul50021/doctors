<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    @if(auth()->check() && auth()->user()->role === 'seller')
        <div class="sidebar-inner">
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <li class="menu-title">
                        <span>Seller Panel</span>
                    </li>
                    <li class="{{ request()->routeIs('ecommerce.seller.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('ecommerce.seller.dashboard') }}" wire:navigate><i class="fe fe-home"></i> <span>Dashboard</span></a>
                    </li>
                    <li class="{{ request()->routeIs('ecommerce.seller.products.*') ? 'active' : '' }}">
                        <a href="{{ route('ecommerce.seller.products.index') }}" wire:navigate><i class="fe fe-shopping-cart"></i> <span>My Products</span></a>
                    </li>
                    <li class="{{ request()->routeIs('ecommerce.seller.orders.*') ? 'active' : '' }}">
                        <a href="{{ route('ecommerce.seller.orders.index') }}" wire:navigate><i class="fe fe-activity"></i> <span>My Orders</span></a>
                    </li>
                    <li class="{{ request()->routeIs('ecommerce.seller.payouts.*') ? 'active' : '' }}">
                        <a href="{{ route('ecommerce.seller.payouts.index') }}" wire:navigate><i class="fe fe-credit-card"></i> <span>Payouts / Withdraw</span></a>
                    </li>
                    <li class="{{ request()->routeIs('ecommerce.seller.profile.*') ? 'active' : '' }}">
                        <a href="{{ route('ecommerce.seller.profile.edit') }}" wire:navigate><i class="fe fe-settings"></i> <span>Shop Settings</span></a>
                    </li>
                </ul>
            </div>
        </div>
    @else
        <div class="sidebar-inner">
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <li class="menu-title">
                        <span>Main</span>
                    </li>
                    <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" wire:navigate><i class="fe fe-home"></i> <span>Dashboard</span></a>
                    </li>

                    <li class="menu-title">
                        <span>Doctors</span>
                    </li>
                    <li class="{{ request()->routeIs('admin.appointments') ? 'active' : '' }}">
                        <a href="{{ route('admin.appointments') }}" wire:navigate><i class="fe fe-layout"></i>
                            <span>Appointments</span></a>
                    </li>
                    <li class="{{ request()->routeIs('doctors.admin.specialities.*') ? 'active' : '' }}">
                        <a href="{{ route('doctors.admin.specialities.index') }}" wire:navigate><i class="fe fe-users"></i>
                            <span>Specialities</span></a>
                    </li>
                    <li class="{{ request()->routeIs('doctors.admin.doctors.*') ? 'active' : '' }}">
                        <a href="{{ route('doctors.admin.doctors.index') }}" wire:navigate><i class="fe fe-user-plus"></i>
                            <span>Doctors</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.patients') ? 'active' : '' }}">
                        <a href="{{ route('admin.patients') }}" wire:navigate><i class="fe fe-user"></i> <span>Patients</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.reviews') ? 'active' : '' }}">
                        <a href="{{ route('admin.reviews') }}" wire:navigate><i class="fe fe-star-o"></i> <span>Reviews</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.health-packages.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.health-packages.index') }}" wire:navigate><i class="fe fe-heart"></i>
                            <span>Health Packages</span></a>
                    </li>

                    <li class="menu-title">
                        <span>Ecommerce</span>
                    </li>
                    <li class="{{ request()->routeIs('ecommerce.admin.products.*') ? 'active' : '' }}">
                        <a href="{{ route('ecommerce.admin.products.index') }}" wire:navigate><i class="fe fe-shopping-cart"></i>
                            <span>Products</span></a>
                    </li>
                    <li class="{{ request()->routeIs('ecommerce.admin.product-categories.*') ? 'active' : '' }}">
                        <a href="{{ route('ecommerce.admin.product-categories.index') }}" wire:navigate><i class="fe fe-layout"></i> <span>Product
                                Categories</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.orders.index') }}" wire:navigate><i class="fe fe-cart"></i> <span>Orders</span></a>
                    </li>
                    <li class="{{ request()->routeIs('ecommerce.admin.product-reviews.*') ? 'active' : '' }}">
                        <a href="{{ route('ecommerce.admin.product-reviews.index') }}" wire:navigate><i class="fe fe-star-o"></i> <span>Product Reviews</span></a>
                    </li>
                    <li class="{{ request()->routeIs('ecommerce.admin.campaigns.*') ? 'active' : '' }}">
                        <a href="{{ route('ecommerce.admin.campaigns.index') }}" wire:navigate><i class="fe fe-tag"></i> <span>Campaigns / Flash Sale</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.coupons.index') }}" wire:navigate><i class="fe fe-star"></i> <span>Coupons</span></a>
                    </li>
                    <li class="{{ request()->routeIs('ecommerce.admin.sellers.*') ? 'active' : '' }}">
                        <a href="{{ route('ecommerce.admin.sellers.index') }}" wire:navigate><i class="fe fe-users"></i> <span>Sellers List</span></a>
                    </li>
                    <li class="{{ request()->routeIs('ecommerce.admin.seller-payouts.*') ? 'active' : '' }}">
                        <a href="{{ route('ecommerce.admin.seller-payouts.index') }}" wire:navigate><i class="fe fe-credit-card"></i> <span>Seller Payouts</span></a>
                    </li>

                    @if(Route::has('admin.advertisements.index'))
                        <li class="{{ request()->routeIs('admin.advertisements.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.advertisements.index') }}" wire:navigate><i class="fe fe-image"></i>
                                <span>Advertisements</span></a>
                        </li>
                    @endif
                    <li class="{{ request()->routeIs('admin.transactions') ? 'active' : '' }}">
                        <a href="{{ route('admin.transactions') }}" wire:navigate><i class="fe fe-activity"></i>
                            <span>Transactions</span></a>
                    </li>

                    <li class="menu-title">
                        <span>Courses</span>
                    </li>
                    <li class="{{ request()->routeIs('courses.admin.courses.*') ? 'active' : '' }}">
                        <a href="{{ route('courses.admin.courses.index') }}" wire:navigate><i class="fe fe-book-open"></i> <span>Courses
                                List</span></a>
                    </li>

                    <li class="menu-title">
                        <span>Agents</span>
                    </li>
                    <li class="{{ request()->routeIs('admin.agents.*') && !request()->routeIs('admin.agents.payouts') ? 'active' : '' }}">
                        <a href="{{ route('admin.agents.index') }}" wire:navigate><i class="fe fe-users"></i> <span>Agents List</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.agents.payouts') ? 'active' : '' }}">
                        <a href="{{ route('admin.agents.payouts') }}" wire:navigate><i class="fe fe-activity"></i> <span>Payout Requests</span></a>
                    </li>

                    <li class="menu-title">
                        <span>Settings</span>
                    </li>
                    <li class="{{ request()->routeIs('admin.site-settings.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.site-settings.index') }}" wire:navigate><i class="fe fe-settings"></i> <span>Site
                                Settings</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.menus.index') }}" wire:navigate><i class="fe fe-list"></i> <span>Menu Manager</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.banners.index') }}" wire:navigate><i class="fe fe-star"></i> <span>Banners</span></a>
                    </li>
                    <li class="{{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                        <a href="{{ route('admin.profile') }}" wire:navigate><i class="fe fe-user"></i> <span>Profile</span></a>
                    </li>
                    <li class="submenu {{ request()->routeIs('admin.invoice.report') ? 'active' : '' }}">
                        <a href="#"><i class="fe fe-document"></i> <span> Reports</span> <span
                                 class="menu-arrow"></span></a>
                        <ul style="display: none;">
                            <li><a href="{{ route('admin.invoice.report') }}" wire:navigate>Invoice Reports</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    @endif
</div>
<!-- /Sidebar -->
