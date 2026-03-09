<!-- Header -->
<style>
    .header .navbar .container {
        display: flex;
        align-items: center;
    }

    .header .main-menu-wrapper {
        display: flex !important;
        align-items: center;
        justify-content: space-between;
        flex: 1;
    }

    .header .main-nav {
        display: flex !important;
        align-items: center;
        visibility: visible !important;
        opacity: 1 !important;
        margin: 0;
        padding: 0;
    }

    .header .main-nav > li {
        display: inline-flex !important;
        visibility: visible !important;
    }

    .header .main-nav > li > a {
        color: #1e293b !important;
        opacity: 1 !important;
        font-weight: 600;
    }

    .header .main-nav > li > a:hover,
    .header .main-nav > li.active > a {
        color: #2563eb !important;
    }

    @media (max-width: 991.98px) {
        .header .main-menu-wrapper {
            display: block !important;
        }
    }

    .doctor-entry .dropdown-menu {
        min-width: 210px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
    }

    .doctor-entry .dropdown-item {
        border-radius: 8px;
        font-weight: 600;
        padding: 9px 12px;
    }

    .doctor-entry .dropdown-item:hover {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .mobile-doctor-submenu {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: -2px;
    }

    .mobile-doctor-submenu-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.12);
        color: #fff !important;
        border: 1px solid rgba(255, 255, 255, 0.28);
        border-radius: 10px;
        padding: 11px 14px;
        font-weight: 600;
        text-decoration: none;
    }
</style>
<header class="header">
    <nav class="navbar navbar-expand-lg header-nav">
        <div class="container">
            <div class="navbar-header">
                <a id="mobile_btn" href="javascript:void(0);">
                    <span class="bar-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </a>
                <a href="<?php echo e(route('home')); ?>" class="navbar-brand logo">
                    <img src="<?php echo e(!empty($siteSettings['logo']) ? asset($siteSettings['logo']) : asset('assets/img/logo.png')); ?>"
                        class="img-fluid" alt="Logo">
                </a>
            </div>
            <div class="main-menu-wrapper">
                <div class="menu-header">
                    <a href="<?php echo e(route('home')); ?>" class="menu-logo">
                        <img src="<?php echo e(!empty($siteSettings['logo']) ? asset($siteSettings['logo']) : asset('assets/img/logo.png')); ?>"
                            class="img-fluid" alt="Logo">
                    </a>
                    <a id="menu_close" class="menu-close" href="javascript:void(0);">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
                <ul class="main-nav">
                    <?php
                        $renderedMainMenu = collect($mainMenu ?? [])
                            ->filter(fn($menu) => !empty(trim((string) ($menu->title ?? ''))))
                            ->values();

                        // If DB menus exist but none has a usable URL/child, fallback to default static menu.
                        $hasUsableMenu = $renderedMainMenu->contains(function ($menu) {
                            $hasChildren = method_exists($menu, 'children') && $menu->children && $menu->children->count() > 0;
                            $url = method_exists($menu, 'getUrl') ? $menu->getUrl() : '#';
                            return $hasChildren || ($url !== '#');
                        });
                    ?>

                    <?php if($renderedMainMenu->count() > 0 && $hasUsableMenu): ?>
                        <?php $__currentLoopData = $renderedMainMenu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($menu->children->count() > 0): ?>
                                
                                <li class="has-submenu">
                                    <a href="<?php echo e($menu->getUrl()); ?>">
                                        <?php if($menu->icon): ?><i class="<?php echo e($menu->icon); ?>"></i> <?php endif; ?>
                                        <?php echo e($menu->title); ?> <i class="fas fa-chevron-down"></i>
                                    </a>
                                    <ul class="submenu">
                                        <?php $__currentLoopData = $menu->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>
                                                <a href="<?php echo e($child->getUrl()); ?>" <?php if($child->open_in_new_tab): ?> target="_blank" <?php endif; ?>>
                                                    <?php if($child->icon): ?><i class="<?php echo e($child->icon); ?>"></i> <?php endif; ?>
                                                    <?php echo e($child->title); ?>

                                                </a>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </li>
                            <?php else: ?>
                                
                                <li class="<?php echo e(request()->url() == $menu->getUrl() ? 'active' : ''); ?>">
                                    <a href="<?php echo e($menu->getUrl()); ?>" <?php if($menu->open_in_new_tab): ?> target="_blank" <?php endif; ?>>
                                        <?php if($menu->icon): ?><i class="<?php echo e($menu->icon); ?>"></i> <?php endif; ?>
                                        <?php echo e($menu->title); ?>

                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        
                        <li class="<?php echo e(request()->routeIs('home') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('home')); ?>">Home</a>
                        </li>
                        <li class="<?php echo e(request()->routeIs('doctors.search') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('doctors.search')); ?>">Doctors</a>
                        </li>
                        <li class="<?php echo e(request()->routeIs('ecommerce.products*') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('ecommerce.products')); ?>" style="text-transform: capitalize;">Products</a>
                        </li>
                        <li class="<?php echo e(request()->routeIs('courses.*') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('courses.index')); ?>">Courses</a>
                        </li>
                    <?php endif; ?>
                </ul>

                <!-- Mobile Menu Buttons -->
                <div class="mobile-menu-buttons">
                    <a class="mobile-btn-for-doctors" data-bs-toggle="collapse" href="#mobileDoctorMenu" role="button"
                        aria-expanded="false" aria-controls="mobileDoctorMenu">
                        <i class="fas fa-stethoscope"></i> For Doctors
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse mobile-doctor-submenu" id="mobileDoctorMenu">
                        <a class="mobile-doctor-submenu-link" href="<?php echo e(route('doctor.login')); ?>">
                            <i class="fas fa-sign-in-alt"></i> Doctor Login
                        </a>
                        <a class="mobile-doctor-submenu-link" href="<?php echo e(route('doctor.register')); ?>">
                            <i class="fas fa-user-plus"></i> Doctor Registration
                        </a>
                    </div>
                    <a class="mobile-btn-login" href="<?php echo e(route('login')); ?>">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a class="mobile-btn-signup" href="<?php echo e(route('register')); ?>">
                        <i class="fas fa-user-plus"></i> Sign Up
                    </a>
                </div>
            </div>
            <ul class="nav header-navbar-rht">
                <li class="nav-item">
                    <a class="nav-link cart-icon-wrapper" href="<?php echo e(route('ecommerce.cart')); ?>" id="cart-icon-btn" title="Shopping Cart">
                        <div class="cart-icon-circle">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <?php $cartCount = count(session('cart', [])); ?>
                        <?php if($cartCount > 0): ?>
                            <span class="cart-badge"><?php echo e($cartCount); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php if(auth()->guard()->guest()): ?>
                    <li class="nav-item dropdown doctor-entry">
                        <a class="nav-link btn-for-doctors dropdown-toggle" href="#" id="doctorMenuDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-stethoscope"></i> For Doctors
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="doctorMenuDropdown">
                            <a class="dropdown-item" href="<?php echo e(route('doctor.login')); ?>">
                                <i class="fas fa-sign-in-alt me-2"></i>Doctor Login
                            </a>
                            <a class="dropdown-item" href="<?php echo e(route('doctor.register')); ?>">
                                <i class="fas fa-user-plus me-2"></i>Doctor Registration
                            </a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-signup" href="<?php echo e(route('register')); ?>">Sign Up</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link header-login" href="<?php echo e(route('login')); ?>">Login</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item dropdown has-arrow logged-item">
                        <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                            <span class="user-img">
                                <img class="rounded-circle" src="<?php echo e(asset('assets/img/doctors/doctor-thumb-02.jpg')); ?>"
                                    width="31" alt="Darren Elder">
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="user-header">
                                <div class="avatar avatar-sm">
                                    <img src="<?php echo e(asset('assets/img/doctors/doctor-thumb-02.jpg')); ?>" alt="User Image"
                                        class="avatar-img rounded-circle">
                                </div>
                                <div class="user-text">
                                    <h6><?php echo e(Auth::user()->name); ?></h6>
                                    <p class="text-muted mb-0">Doctor</p>
                                </div>
                            </div>
                            <?php if(Auth::user()->role === 'doctor' || Auth::user()->is_doctor): ?>
                                <!-- Assuming role check or similar -->
                                <a class="dropdown-item" href="<?php echo e(route('doctors.dashboard')); ?>">Dashboard</a>
                                <a class="dropdown-item" href="<?php echo e(route('doctors.profile.settings')); ?>">Profile Settings</a>
                            <?php else: ?>
                                <a class="dropdown-item" href="<?php echo e(route('patient.dashboard')); ?>">Dashboard</a>
                                <a class="dropdown-item" href="<?php echo e(route('patient.profile.settings')); ?>">Profile Settings</a>
                            <?php endif; ?>
                            <a class="dropdown-item" href="<?php echo e(route('logout')); ?>"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                <?php echo csrf_field(); ?>
                            </form>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>
<!-- /Header -->
<?php /**PATH /home/ashiqul/.valet/Sites/doctor/resources/views/layouts/partials/header.blade.php ENDPATH**/ ?>