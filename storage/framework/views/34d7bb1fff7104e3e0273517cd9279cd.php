<div class="bottom-nav-container d-md-none">
    <div class="bottom-nav-menu">
        <!-- Home -->
        <a href="<?php echo e(route('home')); ?>" class="nav-item <?php echo e(Route::is('home') ? 'active' : ''); ?>">
            <div class="nav-icon-container">
                <i class="fas fa-home"></i>
            </div>
            <span>Home</span>
        </a>

        <!-- Products (Pharmacy) -->
        <a href="<?php echo e(route('ecommerce.products')); ?>"
            class="nav-item <?php echo e(Route::is('products') || Route::is('cart') ? 'active' : ''); ?>">
            <div class="nav-icon-container">
                <i class="fas fa-capsules"></i>
            </div>
            <span>Products</span>
        </a>

        <!-- Courses -->
        <!-- Note: Linking to products for now as no specific courses route exists, or maybe a hash link if on home -->
        <a href="<?php echo e(route('courses.index')); ?>" class="nav-item <?php echo e(Route::is('courses.*') ? 'active' : ''); ?>">
            <div class="nav-icon-container">
                <i class="fas fa-book-medical"></i>
            </div>
            <span>Courses</span>
        </a>

        <!-- Doctors -->
        <a href="<?php echo e(route('doctors.search')); ?>"
            class="nav-item <?php echo e(Route::is('search') || Route::is('doctors.profile') ? 'active' : ''); ?>">
            <div class="nav-icon-container">
                <i class="fas fa-user-md"></i>
            </div>
            <span>Doctors</span>
        </a>
    </div>
</div>
<?php /**PATH /home/ashiqul/.valet/Sites/doctor/resources/views/layouts/partials/bottom-nav.blade.php ENDPATH**/ ?>