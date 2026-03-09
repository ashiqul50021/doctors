<!-- Header -->
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
                    <?php if(isset($mainMenu) && $mainMenu->count() > 0): ?>
                        <?php $__currentLoopData = $mainMenu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                        <li class="<?php echo e(request()->routeIs('search') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('search')); ?>">Doctors</a>
                        </li>
                        <li class="<?php echo e(request()->routeIs('products*') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('products')); ?>" style="text-transform: capitalize;">Products</a>
                        </li>
                    <?php endif; ?>
                    <!-- Always show Courses menu -->

                </ul>

                <!-- Mobile Menu Buttons -->
                <div class="mobile-menu-buttons">
                    <a class="mobile-btn-for-doctors" href="<?php echo e(route('doctor.register')); ?>">
                        <i class="fas fa-stethoscope"></i> For Doctors
                    </a>
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
                    <a class="nav-link position-relative" href="<?php echo e(route('cart')); ?>" id="cart-icon-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <?php $cartCount = count(session('cart', [])); ?>
                        <?php if($cartCount > 0): ?>
                            <span class="badge bg-danger position-absolute translate-middle"
                                style="top: 10px; left: 75%;"><?php echo e($cartCount); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn-for-doctors" href="<?php echo e(route('doctor.register')); ?>">
                        <i class="fas fa-stethoscope"></i> For Doctors
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn-signup" href="<?php echo e(route('register')); ?>">Sign Up</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link header-login" href="<?php echo e(route('login')); ?>">Login</a>
                </li>
            </ul>
        </div>
    </nav>
</header>
<!-- /Header --><?php /**PATH /home/ashiquli/doctors.ashiqulislamrasel.com/resources/views/layouts/partials/header.blade.php ENDPATH**/ ?>