<?php $__env->startSection('title', ($siteSettings['site_name'] ?? 'Doccure') . ' - ' . ($siteSettings['site_tagline'] ?? 'Doctor Appointment Booking')); ?>

<?php $__env->startSection('content'); ?>
    <!-- Home Banner - DocTime Inspired -->
    <section class="section-hero-doctime">
        <!-- Background Wave Pattern -->
        <div class="hero-wave-pattern"></div>

        <div class="container">
            <!-- Hero Slider -->
            <div class="hero-slider">
                <?php if(isset($banners) && $banners->count() > 0): ?>
                    <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($banner->type == 'content_image'): ?>
                            <!-- Content + Image Slide -->
                            <div class="hero-slide-item">
                                <div class="hero-main-wrapper">
                                    <div class="hero-content-left">
                                        <h1 class="hero-main-title">
                                            <?php echo $banner->title; ?>

                                        </h1>
                                        <?php if($banner->subtitle): ?>
                                            <p class="mb-4 text-muted"><?php echo e($banner->subtitle); ?></p>
                                        <?php endif; ?>

                                        <?php if($banner->stats_text): ?>
                                            <div class="hero-trust-badge">
                                                <i class="fas fa-check-circle"></i>
                                                <span>Trusted By <strong><?php echo e($banner->stats_text); ?></strong></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if($banner->button_text && $banner->button_link): ?>
                                            <a href="<?php echo e($banner->button_link); ?>" class="btn-hero-cta">
                                                <?php echo e($banner->button_text); ?> <i class="fas fa-arrow-right"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="hero-content-right">
                                        <img src="<?php echo e(asset($banner->image)); ?>" alt="<?php echo e($banner->title); ?>" class="hero-doctors-img">
                                    </div>
                                </div>
                            </div>
                        <?php elseif($banner->type == 'image_only'): ?>
                            <!-- Image Only Slide -->
                            <div class="hero-slide-item">
                                <div class="hero-full-image"
                                    style="background-image: url('<?php echo e(asset($banner->image)); ?>'); height: 380px; background-size: cover; background-position: center; border-radius: 20px; position: relative;">
                                    <?php if($banner->button_link): ?>
                                        <a href="<?php echo e($banner->button_link); ?>"
                                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <!-- Fallback Static Slides (Keep original if no dynamic banners) -->
                    <!-- Slide 1 -->
                    <div class="hero-slide-item">
                        <div class="hero-main-wrapper">
                            <div class="hero-content-left">
                                <h1 class="hero-main-title">
                                    <?php echo $bannerSettings['banner_title'] ?? 'The Largest Online<br><span class="text-blue">Doctor Platform</span><br>Of The Country'; ?>

                                </h1>
                                <div class="hero-trust-badge">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Trusted By <strong><?php echo e($bannerSettings['banner_stats_text'] ?? '700,000'); ?></strong>
                                        Patients</span>
                                </div>
                                <a href="<?php echo e(route('doctors.search')); ?>" class="btn-hero-cta">
                                    Consult a Doctor Now <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <div class="hero-content-right">
                                <?php if(!empty($bannerSettings['banner_image'])): ?>
                                    <img src="<?php echo e(asset($bannerSettings['banner_image'])); ?>" alt="Professional Doctors"
                                        class="hero-doctors-img">
                                <?php else: ?>
                                    <img src="<?php echo e(asset('assets/img/doctors-hero.png')); ?>" alt="Professional Doctors"
                                        class="hero-doctors-img">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- Other static slides can be removed or kept as backups -->
                <?php endif; ?>
            </div>

            <!-- Search Section -->
            <div class="hero-search-section">
                <!-- Main Search Bar -->
                <div class="hero-search-bar">
                    <form action="<?php echo e(route('doctors.search')); ?>" class="hero-search-form" id="filterSearchForm">
                        <!-- District - Custom Searchable Dropdown -->
                        <div class="search-field search-select">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="custom-dropdown" id="districtDropdown">
                                <input type="hidden" name="district_id" id="district_value">
                                <input type="text" class="dropdown-search" placeholder="District"
                                    data-default-placeholder="District" readonly id="district_display">
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                                <div class="dropdown-menu">
                                    <input type="text" class="dropdown-filter" placeholder="Search district...">
                                    <div class="dropdown-list">
                                        <div class="dropdown-item" data-value="">All Districts</div>
                                        <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="dropdown-item" data-value="<?php echo e($district->id); ?>"><?php echo e($district->name); ?>

                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Area - Custom Searchable Dropdown -->
                        <div class="search-field search-select">
                            <i class="fas fa-location-arrow"></i>
                            <div class="custom-dropdown" id="areaDropdown">
                                <input type="hidden" name="area_id" id="area_value">
                                <input type="text" class="dropdown-search" placeholder="Area"
                                    data-default-placeholder="Area" readonly id="area_display" disabled>
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                                <div class="dropdown-menu">
                                    <input type="text" class="dropdown-filter" placeholder="Search area...">
                                    <div class="dropdown-list" id="area_list">
                                        <div class="dropdown-item" data-value="">Select district first</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Speciality - Custom Searchable Dropdown -->
                        <div class="search-field search-select">
                            <i class="fas fa-stethoscope"></i>
                            <div class="custom-dropdown" id="specialityDropdown">
                                <input type="hidden" name="speciality_id" id="speciality_value">
                                <input type="text" class="dropdown-search" placeholder="Speciality"
                                    data-default-placeholder="Speciality" readonly id="speciality_display">
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                                <div class="dropdown-menu">
                                    <input type="text" class="dropdown-filter" placeholder="Search speciality...">
                                    <div class="dropdown-list">
                                        <div class="dropdown-item" data-value="">All Specialities</div>
                                        <?php $__currentLoopData = $searchSpecialities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $speciality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="dropdown-item" data-value="<?php echo e($speciality->id); ?>">
                                                <?php echo e($speciality->name); ?>

                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keyword Input (Search by doctor) -->
                        <div class="search-field search-keyword">
                            <i class="fas fa-search"></i>
                            <input type="text" name="keywords" placeholder="Search by doctor name/code"
                                class="form-control">
                        </div>

                        <button type="submit" class="btn-hero-search">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Service Cards Removed -->
        </div>
    </section>
    <!-- /Home Banner -->

    <!-- Doctor Registration CTA -->
    <section class="section-doctor-cta">
        <div class="container">
            <div class="doctor-cta-wrapper">
                <div class="doctor-cta-content">
                    <div class="doctor-cta-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="doctor-cta-text">
                        <h3>Are you a Doctor?</h3>
                        <p>Join thousands of doctors on our platform and grow your practice. Get more patients and manage
                            your appointments easily.</p>
                    </div>
                </div>
                <div class="doctor-cta-action">
                    <a href="<?php echo e(route('doctor.register')); ?>" class="btn-doctor-register">
                        <i class="fas fa-stethoscope"></i> Register as Doctor
                    </a>
                </div>
            </div>
        </div>
    </section>
    <!-- /Doctor Registration CTA -->

    <!-- Video Section -->
    <section class="section-video">
        <div class="container">
            <div class="section-header text-center">
                <h2>Watch How We Help You</h2>
                <p class="sub-title">See our platform in action and learn how easy it is to book appointments</p>
            </div>
            <div class="video-wrapper">
                <div class="video-container">
                    <!-- Replace VIDEO_ID with your YouTube video ID -->
                    <iframe src="https://www.youtube.com/embed/8-8A8E-G4Co?rel=0" title="Platform Introduction Video"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>
    </section>
    <!-- /Video Section -->

    <!-- Clinic and Specialities -->
    
    <!-- Clinic and Specialities -->

    <!-- Medical Products -->
    <section class="section section-products" style="background-color: #f8f9fa;">
        <div class="container">
            <div class="section-header text-center">
                <h2>Featured Medical Products</h2>
                <p class="sub-title">Order medicines and health products from our trusted pharmacy store.</p>
            </div>

            <div class="row">
                <!-- Sidebar Filter -->
                <div class="col-lg-3 col-md-4 mb-4">
                    <div class="product-filter-card">
                        <!-- Search -->
                        <div class="filter-section">
                            <h5 class="filter-title"><i class="fas fa-search"></i> Search</h5>
                            <div class="search-input-wrapper">
                                <input type="text" class="form-control" id="productSearchInput"
                                    placeholder="Search products...">
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="filter-section">
                            <h5 class="filter-title"><i class="fas fa-list"></i> Categories</h5>
                            <div class="category-list">
                                <label class="category-item">
                                    <input type="radio" name="product_category" value="all" checked>
                                    <span class="category-name">All Products</span>
                                </label>
                                <?php $__currentLoopData = $productCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="category-item">
                                        <input type="radio" name="product_category" value="<?php echo e($category->id); ?>">
                                        <span class="category-name"><?php echo e($category->name); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Sidebar Filter -->

                <!-- Products Grid -->
                <div class="col-lg-9 col-md-8">
                    <div class="row" id="productsGrid">
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-lg-4 col-md-6 col-sm-6 mb-4 product-grid-item">
                                <div class="product-card-modern">
                                    <!-- Stock Badge -->
                                    <div class="stock-badge <?php echo e($product->stock > 0 ? 'in-stock' : 'out-of-stock'); ?>">
                                        <?php echo e($product->stock > 0 ? 'IN STOCK' : 'OUT OF STOCK'); ?>

                                    </div>

                                    <!-- Product Image -->
                                    <div class="product-image-container">
                                        <a href="<?php echo e(route('ecommerce.products.show', $product->id)); ?>">
                                            <?php
                                                $image = $product->image;
                                                if (!$image && !empty($product->gallery) && is_array($product->gallery)) {
                                                    $image = $product->gallery[0] ?? null;
                                                }
                                            ?>
                                            <img src="<?php echo e($image ? asset($image) : asset('assets/img/products/default-product.png')); ?>"
                                                class="product-main-img" alt="<?php echo e($product->name); ?>">
                                        </a>
                                    </div>

                                    <!-- Product Details -->
                                    <div class="product-details">
                                        <!-- Rating -->
                                        <div class="product-rating">
                                            <i class="fas fa-star"></i>
                                            <span class="rating-value"><?php echo e(number_format($product->rating ?? 4.5, 1)); ?></span>
                                            <span class="review-count">(<?php echo e($product->reviews_count ?? rand(10, 200)); ?>)</span>
                                        </div>

                                        <!-- Brand/Category -->
                                        <div class="product-brand"><?php echo e($product->category->name ?? 'Medicine'); ?></div>

                                        <!-- Title -->
                                        <h4 class="product-name">
                                            <a href="<?php echo e(route('ecommerce.products.show', $product->id)); ?>"><?php echo e($product->name); ?></a>
                                        </h4>

                                        <!-- Price & Actions -->
                                        <div class="product-footer">
                                            <div class="product-price-tag">
                                                <?php if($product->sale_price): ?>
                                                    <span class="price-current">৳<?php echo e(number_format($product->sale_price, 0)); ?></span>
                                                    <span class="price-original">৳<?php echo e(number_format($product->price, 0)); ?></span>
                                                <?php else: ?>
                                                    <span class="price-current">৳<?php echo e(number_format($product->price, 0)); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <form action="<?php echo e(route('ecommerce.cart.add')); ?>" method="POST" class="product-actions-form">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <div class="btn-group-modern">
                                                    <button type="submit" class="btn-cart-modern" title="Add to Cart">
                                                        <i class="fas fa-shopping-cart"></i>
                                                    </button>
                                                    <button type="submit" name="buy_now" value="1" class="btn-buy-modern">
                                                        Buy
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <!-- View All Button -->
                    <div class="text-center mt-4">
                        <a href="<?php echo e(route('ecommerce.products')); ?>" class="btn-view-all-arrow">
                            View All Products <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <!-- /Products Grid -->
            </div>
        </div>
    </section>
    <!-- /Medical Products -->

    <!-- Popular Doctors -->
    <section class="section section-doctor" style="background-color: #f8f9fa;">
        <div class="container">
            <div class="section-header text-center">
                <h2>Book Our Doctors</h2>
                <p class="sub-title">Meet our expert doctors and book your appointment today</p>
            </div>

            <div class="row">
                <!-- Sidebar Filter -->
                <div class="col-lg-3 col-md-4 mb-4">
                    <div class="doctor-filter-card">
                        <!-- Search -->
                        <div class="filter-section">
                            <h5 class="filter-title"><i class="fas fa-search"></i> Search Doctor</h5>
                            <div class="search-input-wrapper">
                                <input type="text" class="form-control" id="doctorSearchInput"
                                    placeholder="Search by name...">
                            </div>
                        </div>

                        <!-- Specialities -->
                        <div class="filter-section">
                            <h5 class="filter-title"><i class="fas fa-stethoscope"></i> Speciality</h5>
                            <div class="category-list speciality-list">
                                <label class="category-item">
                                    <input type="radio" name="doctor_speciality" value="all" checked>
                                    <span class="category-name">All Doctors</span>
                                </label>
                                <?php $__currentLoopData = $searchSpecialities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $speciality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="category-item">
                                        <input type="radio" name="doctor_speciality" value="<?php echo e($speciality->id); ?>">
                                        <span class="category-name"><?php echo e($speciality->name); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Sidebar Filter -->

                <!-- Doctors Grid -->
                <div class="col-lg-9 col-md-8">
                    <div class="row" id="doctorsGrid">
                        <?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-lg-4 col-md-6 col-sm-6 mb-4 doctor-grid-item">
                                <div class="doctor-card-new">
                                    <div class="doctor-img-wrapper">
                                        <a href="<?php echo e(route('doctors.profile', $doctor->id)); ?>">
                                            <img src="<?php echo e($doctor->profile_image ? asset($doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-01.jpg')); ?>"
                                                class="doctor-img" alt="<?php echo e($doctor->user->name); ?>">
                                        </a>
                                        <div class="doctor-fee-badge">
                                            <span>৳
                                                <?php echo e($doctor->pricing === 'free' ? 'Free' : number_format($doctor->custom_price, 0)); ?></span>
                                        </div>
                                    </div>
                                    <div class="doctor-info">
                                        <span class="doctor-speciality"><?php echo e($doctor->speciality->name ?? 'General'); ?></span>
                                        <h4 class="doctor-name">
                                            <a href="<?php echo e(route('doctors.profile', $doctor->id)); ?>">Dr.
                                                <?php echo e($doctor->user->name); ?></a>
                                            <i class="fas fa-check-circle verified-badge" title="Verified"></i>
                                        </h4>
                                        <div class="doctor-rating">
                                            <i class="fas fa-star"></i>
                                            <span><?php echo e(number_format($doctor->average_rating, 1)); ?></span>
                                            <span class="rating-count">(<?php echo e($doctor->review_count); ?> reviews)</span>
                                        </div>
                                        <div class="doctor-location">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><?php echo e($doctor->clinic_name ?? ($doctor->area->name ?? 'Dhaka')); ?></span>
                                        </div>
                                        <div class="doctor-buttons">
                                            <a href="<?php echo e(route('doctors.profile', $doctor->id)); ?>" class="btn-view-details">
                                                <i class="fas fa-user"></i> Details
                                            </a>
                                            <a href="<?php echo e(route('booking', $doctor->id)); ?>" class="btn-book-appointment">
                                                <i class="fas fa-calendar-check"></i> Appointment
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <!-- View All Button -->
                    <div class="text-center mt-4">
                        <a href="<?php echo e(route('doctors.search')); ?>" class="btn-view-all-arrow">
                            View All Doctors <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <!-- /Doctors Grid -->
            </div>
        </div>
    </section>
    <!-- /Popular Doctors -->

    <!-- Health Packages Section -->
    <?php if(isset($healthPackages) && $healthPackages->count() > 0): ?>
    <section class="section section-health-packages"
        style="background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%); padding: 80px 0;">
        <div class="container">
            <!-- Section Header -->
            <div class="section-header text-center mb-5">
                <span class="badge badge-soft-blue mb-3">Health Packages</span>
                <h2 class="mb-3">Choose Your Health Package</h2>
                <p class="text-muted">Comprehensive health checkup packages at affordable prices</p>
            </div>

            <!-- Packages Grid -->
            <div class="row justify-content-center">
                <?php $__currentLoopData = $healthPackages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="health-package-card <?php echo e($package->is_featured ? 'featured' : ''); ?>">
                        <?php if($package->is_featured): ?>
                            <div class="featured-ribbon">Most Popular</div>
                        <?php endif; ?>
                        <div class="package-icon">
                            <i class="<?php echo e($package->icon); ?>"></i>
                        </div>
                        <div class="package-badge"><?php echo e($package->badge_label); ?></div>
                        <h4 class="package-title"><?php echo e($package->title); ?></h4>
                        <p class="package-tests"><i class="fas fa-vial"></i> <?php echo e($package->test_count); ?>+ Tests Included</p>
                        <?php if(is_array($package->features) && count($package->features) > 0): ?>
                        <ul class="package-features">
                            <?php $__currentLoopData = $package->features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><i class="fas fa-check"></i> <?php echo e($feature); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <?php endif; ?>
                        <div class="package-price">
                            <span class="price">৳<?php echo e(number_format($package->price, 0)); ?></span>
                            <span class="period"><?php echo e($package->price_label); ?></span>
                        </div>
                        <a href="<?php echo e($package->link ?? route('ecommerce.products')); ?>" class="btn-package">
                            Book Now <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-4">
                <a href="<?php echo e(route('ecommerce.products')); ?>" class="btn-view-all-arrow">
                    View All Packages <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <!-- /Health Packages Section -->

    <!-- Health Courses Section -->
    <section class="section section-courses" style="background: #fff; padding: 80px 0;">
        <div class="container">
            <!-- Section Header -->
            <div class="section-header text-center mb-5">
                <span class="badge badge-soft-blue mb-3">Learn & Grow</span>
                <h2 class="mb-3">Health Education Courses</h2>
                <p class="text-muted">Free and paid courses to help you understand and manage your health better</p>
            </div>

            <!-- Courses Grid -->
            <div class="row">
                <!-- Course 1 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="course-card">
                        <div class="course-thumbnail">
                            <img src="<?php echo e(asset('assets/img/features/feature-01.jpg')); ?>" alt="Diabetes Management">
                            <div class="play-overlay">
                                <i class="fas fa-play-circle"></i>
                            </div>
                            <span class="course-badge free">Free</span>
                        </div>
                        <div class="course-content">
                            <div class="course-meta">
                                <span><i class="fas fa-clock"></i> 2h 30m</span>
                                <span><i class="fas fa-book"></i> 8 Lessons</span>
                            </div>
                            <h4 class="course-title">Diabetes Management</h4>
                            <p class="course-desc">Learn how to manage blood sugar levels, diet plans, and lifestyle changes
                                for diabetes control.</p>
                            <div class="course-footer">
                                <div class="course-instructor">
                                    <img src="<?php echo e(asset('assets/img/doctors/doctor-thumb-01.jpg')); ?>" alt="Instructor">
                                    <span>Dr. Sarah Wilson</span>
                                </div>
                                <a href="<?php echo e(route('ecommerce.products')); ?>" class="btn-enroll">Enroll <i
                                        class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course 2 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="course-card">
                        <div class="course-thumbnail">
                            <img src="<?php echo e(asset('assets/img/features/feature-02.jpg')); ?>" alt="Heart Health">
                            <div class="play-overlay">
                                <i class="fas fa-play-circle"></i>
                            </div>
                            <span class="course-badge premium">৳500</span>
                        </div>
                        <div class="course-content">
                            <div class="course-meta">
                                <span><i class="fas fa-clock"></i> 3h 15m</span>
                                <span><i class="fas fa-book"></i> 12 Lessons</span>
                            </div>
                            <h4 class="course-title">Heart Health Awareness</h4>
                            <p class="course-desc">Understanding cardiovascular health, risk factors, prevention strategies
                                and heart-healthy lifestyle.</p>
                            <div class="course-footer">
                                <div class="course-instructor">
                                    <img src="<?php echo e(asset('assets/img/doctors/doctor-thumb-02.jpg')); ?>" alt="Instructor">
                                    <span>Dr. John Smith</span>
                                </div>
                                <a href="<?php echo e(route('ecommerce.products')); ?>" class="btn-enroll">Enroll <i
                                        class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course 3 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="course-card">
                        <div class="course-thumbnail">
                            <img src="<?php echo e(asset('assets/img/features/feature-03.jpg')); ?>" alt="Mental Health">
                            <div class="play-overlay">
                                <i class="fas fa-play-circle"></i>
                            </div>
                            <span class="course-badge free">Free</span>
                        </div>
                        <div class="course-content">
                            <div class="course-meta">
                                <span><i class="fas fa-clock"></i> 1h 45m</span>
                                <span><i class="fas fa-book"></i> 6 Lessons</span>
                            </div>
                            <h4 class="course-title">Mental Health & Wellness</h4>
                            <p class="course-desc">Techniques for stress management, anxiety relief, and maintaining
                                positive mental health.</p>
                            <div class="course-footer">
                                <div class="course-instructor">
                                    <img src="<?php echo e(asset('assets/img/doctors/doctor-thumb-03.jpg')); ?>" alt="Instructor">
                                    <span>Dr. Emily Brown</span>
                                </div>
                                <a href="<?php echo e(route('ecommerce.products')); ?>" class="btn-enroll">Enroll <i
                                        class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-4">
                <a href="<?php echo e(route('ecommerce.products')); ?>" class="btn-view-all-arrow">
                    View All Courses <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
    <!-- /Health Courses Section -->

    <!-- Video Section -->
    <section class="section section-video-promo">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="video-promo-content">
                        <span class="badge badge-soft-blue mb-3">Health First</span>
                        <h2 class="mb-4">We Are Always Here For Your Health</h2>
                        <p class="mb-4 text-muted">Doccure provides progressive, and affordable healthcare, accessible on
                            mobile and online for everyone. To us, it's not just work. We take pride in the solutions we
                            deliver</p>

                        <ul class="video-promo-list list-unstyled mb-4">
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Leading Healthcare Provider</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> 24/7 Support Available</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Experienced Doctors</li>
                        </ul>

                        <a href="<?php echo e(route('doctors.search')); ?>" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="video-promo-box">
                        <img src="<?php echo e(asset('assets/img/features/feature.png')); ?>" alt="Video Thumbnail"
                            class="img-fluid rounded-lg shadow-lg">
                        <a href="https://www.youtube.com/watch?v=Nu6Z42pKLri" data-fancybox class="video-play-btn">
                            <i class="fas fa-play"></i>
                            <span class="video-ripple ripple-1"></span>
                            <span class="video-ripple ripple-2"></span>
                            <span class="video-ripple ripple-3"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /Video Section -->

    <!-- Services Section -->
    <section class="section section-services">
        <div class="container">
            <div class="section-header text-center">
                <h2>Our Services</h2>
                <p class="sub-title">We provide the best quality healthcare services.</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-4 col-sm-6">
                    <div class="service-box">
                        <div class="service-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <h4>Medical</h4>
                        <p>Comprehensive medical care with state-of-the-art facilities and expert physicians.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="service-box">
                        <div class="service-icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <h4>Laboratory</h4>
                        <p>Advanced diagnostic laboratory for accurate and timely test results.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="service-box">
                        <div class="service-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h4>ICU Service</h4>
                        <p>24/7 Intensive Care Unit with specialized monitoring and support.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="service-box">
                        <div class="service-icon">
                            <i class="fas fa-procedures"></i>
                        </div>
                        <h4>Operation</h4>
                        <p>Modern operation theaters equipped for complex surgical procedures.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="service-box">
                        <div class="service-icon">
                            <i class="fas fa-vials"></i>
                        </div>
                        <h4>Test Room</h4>
                        <p>Dedicated rooms for various specialized medical tests and screenings.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="service-box">
                        <div class="service-icon">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <h4>Patient Ward</h4>
                        <p>Comfortable and hygienic wards for optimal patient recovery.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /Services Section -->

    <!-- Blog Section -->
    <section class="section section-blogs" style="background-color: #ffff;">
        <div class="container">
            <div class="section-header text-center">
                <h2>Latest Blogs & News</h2>
                <p class="sub-title">Stay updated with our latest health tips and news.</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-3 col-sm-12">
                    <div class="blog-grid">
                        <div class="blog-grid-img">
                            <a href="#">
                                <img src="<?php echo e(asset('assets/img/blog/blog-01.jpg')); ?>" class="img-fluid" alt="Blog Image">
                            </a>
                        </div>
                        <div class="blog-grid-info">
                            <div class="blog-date">05 Sep 2025</div>
                            <h4 class="blog-title"><a href="#">How to Handle Patient Health?</a></h4>
                            <p class="blog-text">Learn the best practices for managing patient health effectively...</p>
                            <a href="#" class="read-more-btn">Read More <i class="fas fa-long-arrow-alt-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 col-sm-12">
                    <div class="blog-grid">
                        <div class="blog-grid-img">
                            <a href="#">
                                <img src="<?php echo e(asset('assets/img/blog/blog-02.jpg')); ?>" class="img-fluid" alt="Blog Image">
                            </a>
                        </div>
                        <div class="blog-grid-info">
                            <div class="blog-date">06 Sep 2025</div>
                            <h4 class="blog-title"><a href="#">The Benefits of Regular Checkups</a></h4>
                            <p class="blog-text">Regular health checkups are vital for early detection and prevention...</p>
                            <a href="#" class="read-more-btn">Read More <i class="fas fa-long-arrow-alt-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 col-sm-12">
                    <div class="blog-grid">
                        <div class="blog-grid-img">
                            <a href="#">
                                <img src="<?php echo e(asset('assets/img/blog/blog-03.jpg')); ?>" class="img-fluid" alt="Blog Image">
                            </a>
                        </div>
                        <div class="blog-grid-info">
                            <div class="blog-date">07 Sep 2025</div>
                            <h4 class="blog-title"><a href="#">Healthy Living Tips</a></h4>
                            <p class="blog-text">Simple lifestyle changes can lead to significant health improvements...</p>
                            <a href="#" class="read-more-btn">Read More <i class="fas fa-long-arrow-alt-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 col-sm-12">
                    <div class="blog-grid">
                        <div class="blog-grid-img">
                            <a href="#">
                                <img src="<?php echo e(asset('assets/img/blog/blog-04.jpg')); ?>" class="img-fluid" alt="Blog Image">
                            </a>
                        </div>
                        <div class="blog-grid-info">
                            <div class="blog-date">08 Sep 2025</div>
                            <h4 class="blog-title"><a href="#">Understanding Mental Health</a></h4>
                            <p class="blog-text">Mental health is just as important as physical health. Find out why...</p>
                            <a href="#" class="read-more-btn">Read More <i class="fas fa-long-arrow-alt-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="view-all text-center mt-4">
                <a href="#" class="btn btn-outline-primary">View All Blogs</a>
            </div>
        </div>
    </section>
    <!-- /Blog Section -->

    <!-- How It Works -->
    <section class="section section-how-it-works" style="background-color: #f9faff;">
        <div class="container">
            <div class="section-header text-center">
                <h2>How It Works</h2>
                <p class="sub-title">Get started with just a few simple steps</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card text-center border-0 shadow-sm h-100 how-it-works-card" style="border-radius: 15px;">
                        <div class="card-body py-5">
                            <div class="mb-4">
                                <span style="font-size: 50px; color: #1D4ED8;"><i class="fas fa-search"></i></span>
                            </div>
                            <h5 class="card-title font-weight-bold">Search Doctor</h5>
                            <p class="card-text text-muted">Find the right doctor by specialty, name, or location.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card text-center border-0 shadow-sm h-100 how-it-works-card" style="border-radius: 15px;">
                        <div class="card-body py-5">
                            <div class="mb-4">
                                <span style="font-size: 50px; color: #1D4ED8;"><i class="fas fa-user-check"></i></span>
                            </div>
                            <h5 class="card-title font-weight-bold">Check Profile</h5>
                            <p class="card-text text-muted">View doctor's qualifications, reviews, and experience.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card text-center border-0 shadow-sm h-100 how-it-works-card" style="border-radius: 15px;">
                        <div class="card-body py-5">
                            <div class="mb-4">
                                <span style="font-size: 50px; color: #1D4ED8;"><i class="fas fa-calendar-check"></i></span>
                            </div>
                            <h5 class="card-title font-weight-bold">Book Appointment</h5>
                            <p class="card-text text-muted">Select a convenient time slot and book your visit.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card text-center border-0 shadow-sm h-100 how-it-works-card" style="border-radius: 15px;">
                        <div class="card-body py-5">
                            <div class="mb-4">
                                <span style="font-size: 50px; color: #1D4ED8;"><i class="fas fa-notes-medical"></i></span>
                            </div>
                            <h5 class="card-title font-weight-bold">Get Consultation</h5>
                            <p class="card-text text-muted">Visit the doctor and receive quality care.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /How It Works -->

    <!-- Statistics Section -->
    <section class="section section-stats"
        style="background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%); padding: 80px 0;">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h2 class="stat-number">
                            <span class="counter-number" data-target="500">0</span>+
                        </h2>
                        <p class="stat-label">Expert Doctors</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h2 class="stat-number">
                            <span class="counter-number" data-target="10000" data-suffix="K">0</span>+
                        </h2>
                        <p class="stat-label">Happy Patients</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-hospital"></i>
                        </div>
                        <h2 class="stat-number">
                            <span class="counter-number" data-target="100">0</span>+
                        </h2>
                        <p class="stat-label">Clinics & Hospitals</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <h2 class="stat-number">
                            <span class="counter-number" data-target="15">0</span>+
                        </h2>
                        <p class="stat-label">Years of Experience</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /Statistics Section -->

    <!-- Testimonials Section -->
    <section class="section section-specialities">
        <div class="container">
            <div class="section-header text-center">
                <h2>What Our Patients Say</h2>
                <p class="sub-title">Real feedback from our valued patients</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <div class="d-flex mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                            </div>
                            <p class="card-text text-muted">"Excellent service! The doctor was very professional and the
                                booking process was seamless. Highly recommend Doccure to everyone."</p>
                            <div class="d-flex align-items-center mt-4">
                                <img src="<?php echo e(asset('assets/img/patients/patient1.jpg')); ?>" class="rounded-circle me-3"
                                    alt="Patient" style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 font-weight-bold">Sarah Johnson</h6>
                                    <small class="text-muted">Cardiology Patient</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <div class="d-flex mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                            </div>
                            <p class="card-text text-muted">"Found the best dentist through Doccure. The platform is easy to
                                use and the doctor profiles are very detailed. Great experience!"</p>
                            <div class="d-flex align-items-center mt-4">
                                <img src="<?php echo e(asset('assets/img/patients/patient2.jpg')); ?>" class="rounded-circle me-3"
                                    alt="Patient" style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 font-weight-bold">Michael Chen</h6>
                                    <small class="text-muted">Dental Patient</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <div class="d-flex mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                            </div>
                            <p class="card-text text-muted">"Very convenient way to book appointments. No more waiting in
                                long queues. The reminder system is also very helpful."</p>
                            <div class="d-flex align-items-center mt-4">
                                <img src="<?php echo e(asset('assets/img/patients/patient3.jpg')); ?>" class="rounded-circle me-3"
                                    alt="Patient" style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 font-weight-bold">Emily Davis</h6>
                                    <small class="text-muted">General Checkup</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /Testimonials Section -->

    <!-- Call to Action -->
    <!-- Call to Action -->
    <section class="section-cta">
        <div class="cta-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
        <div class="container text-center position-relative z-index-1">
            <h2 class="display-5 font-weight-bold mb-3 text-white">Ready to Book Your Appointment?</h2>
            <p class="lead mb-5 text-white-50">Join thousands of patients who trust Doccure for their healthcare needs.</p>
            <a href="<?php echo e(route('doctors.search')); ?>" class="btn btn-light cta-btn">
                <i class="fas fa-calendar-check me-2"></i> Find a Doctor Now
            </a>
        </div>
    </section>
    <!-- /Call to Action -->
    <!-- /Call to Action -->

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function () {
            // Counter Animation for Statistics Section
            const counters = document.querySelectorAll('.counter-number');
            let hasAnimated = false;

            function animateCounter(counter) {
                const target = parseInt(counter.getAttribute('data-target'));
                const suffix = counter.getAttribute('data-suffix') || '';
                const duration = 1500; // 1.5 seconds
                const step = target / (duration / 16); // 60fps
                let current = 0;

                const updateCounter = () => {
                    current += step;
                    if (current < target) {
                        if (suffix === 'K') {
                            counter.textContent = Math.floor(current / 1000) + 'K';
                        } else {
                            counter.textContent = Math.floor(current);
                        }
                        requestAnimationFrame(updateCounter);
                    } else {
                        if (suffix === 'K') {
                            counter.textContent = (target / 1000) + 'K';
                        } else {
                            counter.textContent = target;
                        }
                    }
                };
                updateCounter();
            }

            // Intersection Observer to trigger animation when section is visible
            const statsSection = document.querySelector('.section-stats');
            if (statsSection) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !hasAnimated) {
                            hasAnimated = true;
                            counters.forEach(counter => animateCounter(counter));
                        }
                    });
                }, { threshold: 0.3 });

                observer.observe(statsSection);
            }

            // Hero Slider Initialization - Explicit Call
            if ($('.hero-slider').length > 0) {
                $('.hero-slider').slick({
                    dots: false,
                    autoplay: true,
                    autoplaySpeed: 4000,
                    infinite: true,
                    speed: 500,
                    fade: true,
                    cssEase: 'linear',
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: true,
                    prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
                    nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>',
                    responsive: [{
                        breakpoint: 768,
                        settings: {
                            arrows: false
                        }
                    }]
                });
                console.log('Hero Slider Initialized Successfully');
            }

            // =====================================
            // CUSTOM SEARCHABLE DROPDOWN JS
            // =====================================

            // Toggle dropdown
            $(document).on('click', '.custom-dropdown .dropdown-search', function (e) {
                if ($(this).prop('disabled')) return;
                e.stopPropagation();
                var $dropdown = $(this).closest('.custom-dropdown');

                // Close other dropdowns
                $('.custom-dropdown').not($dropdown).removeClass('open');

                // Toggle this dropdown
                $dropdown.toggleClass('open');

                // Focus on filter input
                if ($dropdown.hasClass('open')) {
                    $dropdown.find('.dropdown-filter').val('').trigger('input').focus();
                }
            });

            // Filter items on search
            $(document).on('input', '.custom-dropdown .dropdown-filter', function () {
                var query = $(this).val().toLowerCase();
                var $list = $(this).siblings('.dropdown-list');
                var hasResults = false;

                $list.find('.dropdown-item').each(function () {
                    var text = $(this).text().toLowerCase();
                    if (text.indexOf(query) > -1 || $(this).data('value') === '') {
                        $(this).removeClass('hidden');
                        hasResults = true;
                    } else {
                        $(this).addClass('hidden');
                    }
                });

                // Show no results message
                $list.find('.no-results-msg').remove();
                if (!hasResults) {
                    $list.append('<div class="dropdown-item no-results no-results-msg">No results found</div>');
                }
            });

            // Select item
            $(document).on('click', '.custom-dropdown .dropdown-item:not(.no-results)', function () {
                var $dropdown = $(this).closest('.custom-dropdown');
                var value = $(this).data('value');
                var text = $.trim($(this).text());
                var $display = $dropdown.find('.dropdown-search');
                var defaultPlaceholder = $display.data('default-placeholder') || $display.attr('placeholder') || 'Select';

                // Update hidden input
                $dropdown.find('input[type="hidden"]').val(value);

                // Update display - show text if value exists, else reset to placeholder
                if (value !== '' && value !== undefined && value !== null) {
                    $display.val(text);
                } else {
                    $display.val('').attr('placeholder', defaultPlaceholder);
                }

                // Mark as selected
                $dropdown.find('.dropdown-item').removeClass('selected');
                $(this).addClass('selected');

                // Close dropdown
                $dropdown.removeClass('open');

                // Trigger change event for dependent dropdowns
                $dropdown.find('input[type="hidden"]').trigger('change');
            });

            // Close dropdown on click outside
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.custom-dropdown').length) {
                    $('.custom-dropdown').removeClass('open');
                }
            });

            // Prevent form submit on enter in filter
            $(document).on('keydown', '.custom-dropdown .dropdown-filter', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    var $firstItem = $(this).siblings('.dropdown-list').find('.dropdown-item:not(.hidden):not(.no-results)').first();
                    if ($firstItem.length) $firstItem.click();
                } else if (e.key === 'Escape') {
                    $(this).closest('.custom-dropdown').removeClass('open');
                }
            });

            // District change handler for area loading
            $('#district_value').on('change', function () {
                var districtId = $(this).val();
                var $areaDisplay = $('#area_display');
                var $areaList = $('#area_list');
                var $areaValue = $('#area_value');

                $areaValue.val('');
                $areaDisplay.val('').attr('placeholder', 'Area');

                if (districtId) {
                    $areaDisplay.prop('disabled', false);
                    $areaList.html('<div class="dropdown-item" data-value="">Loading...</div>');

                    $.ajax({
                        url: '/api/areas/' + districtId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (areas) {
                            var html = '<div class="dropdown-item" data-value="">All Areas</div>';
                            $.each(areas, function (key, area) {
                                html += '<div class="dropdown-item" data-value="' + area.id + '">' + area.name + '</div>';
                            });
                            $areaList.html(html);
                        },
                        error: function () {
                            $areaList.html('<div class="dropdown-item" data-value="">Failed to load</div>');
                            $areaDisplay.prop('disabled', true);
                        }
                    });
                } else {
                    $areaDisplay.prop('disabled', true);
                    $areaList.html('<div class="dropdown-item" data-value="">Select district first</div>');
                }
            });

            // Product filtering functionality
            var searchTimeout;

            function filterProducts() {
                var category = $('input[name="product_category"]:checked').val();
                var search = $('#productSearchInput').val();

                $.ajax({
                    url: '/api/products/filter',
                    type: 'GET',
                    data: { category: category, search: search },
                    success: function (products) {
                        renderProducts(products);
                    }
                });
            }

            function renderProducts(products) {
                var grid = $('#productsGrid');
                grid.empty();

                if (products.length === 0) {
                    grid.html('<div class="col-12"><div class="alert alert-info text-center">No products found.</div></div>');
                    return;
                }

                products.forEach(function (product) {
                    var imageSrc = product.image ? (product.image.startsWith('http') ? product.image : '/' + product.image) : '/assets/img/products/default-product.png';

                    var priceHtml = '';
                    if (product.sale_price) {
                        priceHtml = '<span class="price-current">৳' + numberFormat(product.sale_price) + '</span>' +
                            '<span class="price-original">৳' + numberFormat(product.price) + '</span>';
                    } else {
                        priceHtml = '<span class="price-current">৳' + numberFormat(product.price) + '</span>';
                    }

                    var stockClass = (product.stock > 0) ? 'in-stock' : 'out-of-stock';
                    var stockText = (product.stock > 0) ? 'IN STOCK' : 'OUT OF STOCK';
                    var rating = product.rating || 4.5;
                    var reviewCount = product.reviews_count || Math.floor(Math.random() * 190) + 10;
                    var categoryName = product.category ? product.category.name : 'Medicine';

                    var html = `
                                                                                                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4 product-grid-item">
                                                                                                                    <div class="product-card-modern">
                                                                                                                        <div class="stock-badge ${stockClass}">${stockText}</div>
                                                                                                                        <div class="product-image-container">
                                                                                                                            <a href="/products/${product.id}">
                                                                                                                                <img src="${imageSrc}" class="product-main-img" alt="${product.name}">
                                                                                                                            </a>
                                                                                                                        </div>
                                                                                                                        <div class="product-details">
                                                                                                                            <div class="product-rating">
                                                                                                                                <i class="fas fa-star"></i>
                                                                                                                                <span class="rating-value">${rating.toFixed(1)}</span>
                                                                                                                                <span class="review-count">(${reviewCount})</span>
                                                                                                                            </div>
                                                                                                                            <div class="product-brand">${categoryName}</div>
                                                                                                                            <h4 class="product-name">
                                                                                                                                <a href="/products/${product.id}">${product.name}</a>
                                                                                                                            </h4>
                                                                                                                            <div class="product-footer">
                                                                                                                                <div class="product-price-tag">${priceHtml}</div>
                                                                                                                                <form action="/cart/add" method="POST" class="product-actions-form">
                                                                                                                                    <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                                                                                                                                    <input type="hidden" name="product_id" value="${product.id}">
                                                                                                                                    <input type="hidden" name="quantity" value="1">
                                                                                                                                    <div class="btn-group-modern">
                                                                                                                                        <button type="submit" class="btn-cart-modern" title="Add to Cart">
                                                                                                                                            <i class="fas fa-shopping-cart"></i>
                                                                                                                                        </button>
                                                                                                                                        <button type="submit" name="buy_now" value="1" class="btn-buy-modern">
                                                                                                                                            Buy
                                                                                                                                        </button>
                                                                                                                                    </div>
                                                                                                                                </form>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            `;
                    grid.append(html);
                });
            }

            function numberFormat(num) {
                return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // Category filter change
            $('input[name="product_category"]').on('change', function () {
                filterProducts();
            });

            // Search input with debounce
            $('#productSearchInput').on('keyup', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function () {
                    filterProducts();
                }, 300);
            });
            // Doctor filtering functionality
            var doctorSearchTimeout;

            function filterDoctors() {
                var speciality = $('input[name="doctor_speciality"]:checked').val();
                var search = $('#doctorSearchInput').val();

                $.ajax({
                    url: '<?php echo e(route('api.doctors.filter')); ?>',
                    type: 'GET',
                    data: { speciality: speciality, search: search },
                    success: function (doctors) {
                        renderDoctors(doctors);
                    },
                    error: function () {
                        $('#doctorsGrid').html('<div class="col-12"><div class="alert alert-danger text-center">Failed to load doctors.</div></div>');
                    }
                });
            }

            function renderDoctors(doctors) {
                var grid = $('#doctorsGrid');
                grid.empty();

                if (doctors.length === 0) {
                    grid.html('<div class="col-12"><div class="alert alert-info text-center">No doctors found.</div></div>');
                    return;
                }

                doctors.forEach(function (doctor) {
                    var imageSrc = doctor.profile_image || '/assets/img/doctors/doctor-thumb-01.jpg';
                    var fee = doctor.pricing === 'free' ? 'Free' : '৳ ' + numberFormat(doctor.custom_price || 0);

                    var html = `
                                                                                                                <div class="col-lg-4 col-md-6 col-sm-6 mb-4 doctor-grid-item">
                                                                                                                    <div class="doctor-card-new">
                                                                                                                        <div class="doctor-img-wrapper">
                                                                                                                            <a href="/doctor-profile/${doctor.id}">
                                                                                                                                <img src="${imageSrc}" class="doctor-img" alt="${doctor.name}">
                                                                                                                            </a>
                                                                                                                            <div class="doctor-fee-badge">
                                                                                                                                <span>${fee}</span>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div class="doctor-info">
                                                                                                                            <span class="doctor-speciality">${doctor.speciality}</span>
                                                                                                                            <h4 class="doctor-name">
                                                                                                                                <a href="/doctor-profile/${doctor.id}">Dr. ${doctor.name}</a>
                                                                                                                                <i class="fas fa-check-circle verified-badge" title="Verified"></i>
                                                                                                                            </h4>
                                                                                                                            <div class="doctor-rating">
                                                                                                                                <i class="fas fa-star"></i>
                                                                                                                                <span>${parseFloat(doctor.average_rating || 0).toFixed(1)}</span>
                                                                                                                                <span class="rating-count">(${doctor.review_count || 0} reviews)</span>
                                                                                                                            </div>
                                                                                                                            <div class="doctor-location">
                                                                                                                                <i class="fas fa-map-marker-alt"></i>
                                                                                                                                <span>${doctor.clinic_name || doctor.area_name}</span>
                                                                                                                            </div>
                                                                                                                            <div class="doctor-buttons">
                                                                                                                                <a href="/doctor-profile/${doctor.id}" class="btn-view-details">
                                                                                                                                    <i class="fas fa-user"></i> Details
                                                                                                                                </a>
                                                                                                                                <a href="/booking/${doctor.id}" class="btn-book-appointment">
                                                                                                                                    <i class="fas fa-calendar-check"></i> Appointment
                                                                                                                                </a>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            `;
                    grid.append(html);
                });
            }

            // Doctor speciality filter change
            $('input[name="doctor_speciality"]').on('change', function () {
                filterDoctors();
            });

            // Doctor search input with debounce
            $('#doctorSearchInput').on('keyup', function () {
                clearTimeout(doctorSearchTimeout);
                doctorSearchTimeout = setTimeout(function () {
                    filterDoctors();
                }, 300);
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        /* Header For Doctors Button */
        .btn-for-doctors {
            border: 2px solid #28a745 !important;
            color: #28a745 !important;
            border-radius: 25px !important;
            padding: 8px 18px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            margin-right: 10px;
        }

        .btn-for-doctors:hover {
            background: #28a745 !important;
            color: #fff !important;
        }

        .btn-for-doctors i {
            margin-right: 5px;
        }

        /* Doctor Registration CTA Section */
        .section-doctor-cta {
            background: linear-gradient(135deg, #1D4ED8 0%, #60A5FA 100%);
            padding: 25px 0;
            position: relative;
            overflow: hidden;
            max-width: 1320px;
            margin: 20px auto;
            border-radius: 20px;
        }

        .section-doctor-cta::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .section-doctor-cta::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .doctor-cta-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            position: relative;
            z-index: 1;
        }

        .doctor-cta-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .doctor-cta-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .doctor-cta-icon i {
            font-size: 28px;
            color: #fff;
        }

        .doctor-cta-text h3 {
            color: #fff;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .doctor-cta-text p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
            margin: 0;
            max-width: 500px;
        }

        .btn-doctor-register {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 30px;
            background: #fff;
            color: #1D4ED8;
            border-radius: 50px;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-doctor-register:hover {
            background: #272b41;
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
            text-decoration: none;
        }

        .btn-doctor-register i {
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .doctor-cta-wrapper {
                flex-direction: column;
                text-align: center;
            }

            .doctor-cta-content {
                flex-direction: column;
            }

            .doctor-cta-text p {
                max-width: 100%;
            }
        }

        /* Video Section */
        .section-video {
            background: linear-gradient(180deg, #e8f4fc 0%, #f0f9ff 100%);
            padding: 60px 0;
            margin: 20px 0;
        }

        .section-video .section-header h2 {
            color: #272b41;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .section-video .section-header .sub-title {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 40px;
        }

        .video-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            /* 16:9 aspect ratio */
            height: 0;
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 102, 255, 0.15);
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 20px;
        }

        /* Product Filter Card */
        .product-filter-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            padding: 25px;
            position: sticky;
            top: 100px;
        }

        .filter-section {
            margin-bottom: 25px;
        }

        .filter-section:last-child {
            margin-bottom: 0;
        }

        .filter-title {
            font-size: 16px;
            font-weight: 600;
            color: #272b41;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-title i {
            color: #1D4ED8;
        }

        .search-input-wrapper input {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e8e8e8;
            transition: all 0.3s;
        }

        .search-input-wrapper input:focus {
            border-color: #1D4ED8;
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
        }

        /* Category List */
        .category-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .speciality-list .category-item {
            padding: 7px 8px;
            gap: 8px;
        }

        .speciality-list .category-item input[type="radio"] {
            width: 15px;
            height: 15px;
            flex: 0 0 15px;
        }

        .speciality-list .category-name {
            font-size: 12px;
            line-height: 1.3;
            white-space: normal;
            word-break: break-word;
            text-transform: uppercase;
        }

        .category-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            margin: 0;
        }

        .category-item:hover {
            background: #f5f8ff;
        }

        .category-item input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #1D4ED8;
        }

        .category-item .category-name {
            font-size: 14px;
            color: #555;
        }

        .category-item input:checked+.category-name {
            color: #1D4ED8;
            font-weight: 600;
        }

        /* Product Card Modern */
        .section-products .row {
            margin: 0 -10px;
        }

        .section-products .product-grid-item {
            padding: 0 10px;
        }

        .product-card-modern {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            border: 1px solid #f0f0f0;
        }

        .product-card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 102, 255, 0.12);
        }

        /* Stock Badge */
        .stock-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
            z-index: 10;
            text-transform: uppercase;
        }

        .stock-badge.in-stock {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .stock-badge.out-of-stock {
            background: #ffebee;
            color: #c62828;
        }

        /* Product Image */
        .product-image-container {
            position: relative;
            height: 180px;
            overflow: hidden;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .product-main-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card-modern:hover .product-main-img {
            transform: scale(1.05);
        }

        /* Product Details */
        .product-details {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Rating */
        .product-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .product-rating i {
            color: #ffc107;
            font-size: 12px;
        }

        .product-rating .rating-value {
            font-weight: 600;
            color: #333;
        }

        .product-rating .review-count {
            color: #999;
            font-size: 12px;
        }

        /* Brand */
        .product-brand {
            font-size: 11px;
            color: #1D4ED8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        /* Product Name */
        .product-name {
            font-size: 14px;
            font-weight: 600;
            line-height: 1.4;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 40px;
        }

        .product-name a {
            color: #272b41;
            text-decoration: none;
            transition: color 0.2s;
        }

        .product-name a:hover {
            color: #1D4ED8;
        }

        /* Product Footer - Price & Buttons */
        .product-footer {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .product-price-tag {
            display: flex;
            flex-direction: column;
        }

        .price-current {
            font-size: 18px;
            font-weight: 700;
            color: #272b41;
        }

        .price-original {
            font-size: 12px;
            color: #999;
            text-decoration: line-through;
        }

        /* Button Group */
        .product-actions-form {
            display: flex;
        }

        .btn-group-modern {
            display: flex;
            gap: 6px;
        }

        .btn-cart-modern {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            border: 2px solid #1D4ED8;
            background: transparent;
            color: #1D4ED8;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cart-modern:hover {
            background: #1D4ED8;
            color: #fff;
        }

        .btn-buy-modern {
            padding: 0 20px;
            height: 38px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(135deg, #1D4ED8 0%, #60A5FA 100%);
            color: #fff;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-buy-modern:hover {
            background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 102, 255, 0.3);
        }

        /* View All Button */
        .view-all-btn {
            padding: 10px 30px;
            border-radius: 50px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .view-all-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 102, 255, 0.3);
        }

        /* Arrow Animation Button */
        .btn-view-all-arrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 28px;
            background: linear-gradient(135deg, #1D4ED8 0%, #3B82F6 100%);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(29, 78, 216, 0.3);
        }

        .btn-view-all-arrow i {
            transition: transform 0.3s ease;
        }

        .btn-view-all-arrow:hover {
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(29, 78, 216, 0.4);
        }

        .btn-view-all-arrow:hover i {
            transform: translateX(6px);
        }

        /* =====================================
                                                                                               STATISTICS CARDS SECTION
                                                                                            ===================================== */
        .stat-card {
            background: #fff;
            border-radius: 20px;
            padding: 35px 25px;
            text-align: center;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(29, 78, 216, 0.15);
            border-color: #1D4ED8;
        }

        .stat-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .stat-icon i {
            font-size: 32px;
            color: #1D4ED8;
        }

        .stat-number {
            font-size: 42px;
            font-weight: 700;
            color: #1D4ED8;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 15px;
            color: #6b7280;
            margin: 0;
            font-weight: 500;
        }

        /* =====================================
                                                                                               HEALTH COURSES SECTION
                                                                                            ===================================== */
        .course-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(29, 78, 216, 0.15);
        }

        .course-thumbnail {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .course-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .course-card:hover .course-thumbnail img {
            transform: scale(1.08);
        }

        .play-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .course-card:hover .play-overlay {
            opacity: 1;
        }

        .play-overlay i {
            font-size: 60px;
            color: #fff;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
        }

        .course-card:hover .play-overlay i {
            transform: scale(1.1);
        }

        .course-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .course-badge.free {
            background: #10b981;
            color: #fff;
        }

        .course-badge.premium {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            color: #fff;
        }

        .course-content {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .course-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 12px;
        }

        .course-meta span {
            font-size: 12px;
            color: #6b7280;
        }

        .course-meta i {
            color: #1D4ED8;
            margin-right: 5px;
        }

        .course-title {
            font-size: 17px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .course-desc {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .course-footer {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 15px;
            border-top: 1px solid #f3f4f6;
        }

        .course-instructor {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .course-instructor img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }

        .course-instructor span {
            font-size: 12px;
            font-weight: 600;
            color: #4b5563;
        }

        .btn-enroll {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #1D4ED8 0%, #3B82F6 100%);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-enroll:hover {
            color: #fff;
            box-shadow: 0 4px 15px rgba(29, 78, 216, 0.4);
        }

        .btn-enroll i {
            font-size: 11px;
            transition: transform 0.3s ease;
        }

        .btn-enroll:hover i {
            transform: translateX(4px);
        }

        /* =====================================
                                                                                               HEALTH PACKAGES SECTION
                                                                                            ===================================== */
        .health-package-card {
            background: #fff;
            border-radius: 20px;
            padding: 30px 25px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            border: 2px solid transparent;
        }

        .health-package-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(29, 78, 216, 0.15);
            border-color: #1D4ED8;
        }

        .health-package-card.featured {
            border: 2px solid #1D4ED8;
            transform: scale(1.02);
        }

        .health-package-card.featured:hover {
            transform: scale(1.02) translateY(-10px);
        }

        .featured-ribbon {
            position: absolute;
            top: 15px;
            right: -35px;
            background: linear-gradient(135deg, #1D4ED8 0%, #3B82F6 100%);
            color: #fff;
            padding: 5px 40px;
            font-size: 11px;
            font-weight: 600;
            transform: rotate(45deg);
            box-shadow: 0 2px 10px rgba(29, 78, 216, 0.3);
        }

        .package-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .package-icon i {
            font-size: 28px;
            color: #1D4ED8;
        }

        .package-badge {
            display: inline-block;
            background: #EEF2FF;
            color: #1D4ED8;
            padding: 4px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }

        .package-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .package-tests {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .package-tests i {
            color: #1D4ED8;
            margin-right: 5px;
        }

        .package-features {
            list-style: none;
            padding: 0;
            margin: 0 0 20px 0;
            text-align: left;
        }

        .package-features li {
            padding: 8px 0;
            font-size: 13px;
            color: #4b5563;
            border-bottom: 1px solid #f3f4f6;
        }

        .package-features li:last-child {
            border-bottom: none;
        }

        .package-features li i {
            color: #10b981;
            margin-right: 10px;
            font-size: 12px;
        }

        .package-price {
            margin: auto 0 20px 0;
            padding-top: 15px;
        }

        .package-price .price {
            font-size: 32px;
            font-weight: 700;
            color: #1D4ED8;
        }

        .package-price .period {
            display: block;
            font-size: 12px;
            color: #9ca3af;
            margin-top: 2px;
        }

        .btn-package {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(135deg, #1D4ED8 0%, #3B82F6 100%);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-package:hover {
            color: #fff;
            box-shadow: 0 5px 20px rgba(29, 78, 216, 0.4);
            transform: translateY(-2px);
        }

        .btn-package i {
            transition: transform 0.3s ease;
        }

        .btn-package:hover i {
            transform: translateX(5px);
        }

        /* Responsive */
        @media (max-width: 768px) {

            .product-filter-card,
            .doctor-filter-card {
                position: static;
                margin-bottom: 20px;
            }
        }

        /* Legacy Product Card Styles (for backward compatibility) */
        .product-card-new:hover .product-img {
            transform: scale(1.05);
        }

        .product-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-discount {
            background: #ff4d4d;
            color: #fff;
        }

        .badge-featured {
            background: #1D4ED8;
            color: #fff;
        }

        .product-info {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-category {
            font-size: 11px;
            color: #1D4ED8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .product-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 12px;
            line-height: 1.5;
            min-height: 45px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-title a {
            color: #272b41;
            text-decoration: none;
        }

        .product-title a:hover {
            color: #1D4ED8;
        }

        .product-price {
            margin-bottom: 15px;
            min-height: 28px;
        }

        .current-price {
            font-size: 20px;
            font-weight: 700;
            color: #1D4ED8;
        }

        .original-price {
            font-size: 14px;
            color: #999;
            text-decoration: line-through;
            margin-left: 8px;
        }

        /* Button Styles */
        .btn-add-cart,
        .btn-buy-now {
            width: 100%;
            padding: 8px 5px;
            /* Reduced side padding to prevent overflow */
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 38px;
            /* Fixed height alignment */
        }

        /* Add to Cart Button */
        .btn-add-cart {
            background: linear-gradient(135deg, #1D4ED8, #60A5FA);
            border: none;
            color: #fff;
        }

        .btn-add-cart:hover {
            background: linear-gradient(135deg, #1E40AF, #3B82F6);
            transform: translateY(-2px);
            color: #fff;
        }

        /* Buy Now Button */
        .btn-buy-now {
            background: #fff;
            border: 1px solid #1D4ED8;
            /* Thinner border */
            color: #1D4ED8;
        }

        .btn-buy-now:hover {
            background: #1D4ED8;
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-add-cart i,
        .btn-buy-now i {
            margin-right: 4px;
            font-size: 12px;
        }

        /* View All Button */
        .view-all-btn {
            padding: 10px 30px;
            border-radius: 50px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .view-all-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 102, 255, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {

            .product-filter-card,
            .doctor-filter-card {
                position: static;
                margin-bottom: 20px;
            }

            .category-list {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .category-item {
                flex: 0 0 auto;
            }

            .speciality-list {
                flex-direction: column;
            }
        }

        /* Doctor Filter Card */
        .doctor-filter-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            padding: 25px;
            position: sticky;
            top: 100px;
        }

        /* Doctor Card New */
        .section-doctor .row {
            margin: 0 -10px;
        }

        .section-doctor .doctor-grid-item {
            padding: 0 10px;
        }

        .doctor-card-new {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            transition: all 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid #f0f0f0;
        }

        .doctor-card-new:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0, 102, 255, 0.15);
            border-color: #1D4ED8;
        }

        .doctor-img-wrapper {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: linear-gradient(135deg, #e8f4ff 0%, #f0f8ff 100%);
        }

        .doctor-img-wrapper .doctor-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .doctor-card-new:hover .doctor-img {
            transform: scale(1.05);
        }

        .doctor-fee-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #1D4ED8, #60A5FA);
            color: #fff;
            padding: 8px 15px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(0, 102, 255, 0.3);
        }

        .doctor-info {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .doctor-speciality {
            font-size: 11px;
            color: #1D4ED8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-weight: 600;
            display: inline-block;
            background: #e8f4ff;
            padding: 4px 10px;
            border-radius: 20px;
            width: fit-content;
        }

        .doctor-name {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.4;
            min-height: 25px;
        }

        .doctor-name a {
            color: #272b41;
            text-decoration: none;
        }

        .doctor-name a:hover {
            color: #1D4ED8;
        }

        .verified-badge {
            color: #1D4ED8;
            font-size: 14px;
            margin-left: 5px;
        }

        .doctor-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .doctor-rating i {
            color: #ffc107;
        }

        .doctor-rating .rating-count {
            color: #888;
            font-size: 12px;
        }

        .doctor-location {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
            font-size: 13px;
            color: #666;
        }

        .doctor-location i {
            color: #1D4ED8;
        }

        /* Doctor Buttons Container */
        .doctor-buttons {
            display: flex;
            gap: 10px;
            margin-top: auto;
        }

        .btn-view-details {
            flex: 1;
            padding: 10px 8px;
            background: transparent;
            border: 2px solid #1D4ED8;
            border-radius: 8px;
            color: #1D4ED8;
            font-weight: 600;
            font-size: 12px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-view-details:hover {
            background: #1D4ED8;
            color: #fff;
            text-decoration: none;
        }

        .btn-view-details i {
            margin-right: 4px;
        }

        .btn-book-appointment {
            flex: 1;
            padding: 10px 8px;
            background: linear-gradient(135deg, #1D4ED8, #60A5FA);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-weight: 600;
            font-size: 12px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-book-appointment:hover {
            background: linear-gradient(135deg, #1E40AF, #3B82F6);
            transform: translateY(-2px);
            color: #fff;
            text-decoration: none;
            box-shadow: 0 8px 25px rgba(0, 102, 255, 0.3);
        }

        .btn-book-appointment i {
            margin-right: 4px;
        }

        .section-hero-doctime {
            padding: 0 0 20px 0;
            margin-top: 0 !important;
            min-height: auto;
        }

        .hero-main-wrapper {
            min-height: 380px;
            max-height: 380px;
            overflow: hidden;
        }

        .hero-doctors-img {
            max-height: 380px;
            width: auto;
            object-fit: contain;
        }

        /* Mobile Dropdown Popup Style */
        @media (max-width: 991px) {
            .custom-dropdown .dropdown-menu {
                position: fixed !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                width: 90% !important;
                max-width: 350px !important;
                max-height: 80vh !important;
                /* Dynamic height */
                z-index: 10001 !important;
                background: #fff !important;
                /* Darker backdrop to hide background lines/text */
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3), 0 0 0 100vmax rgba(0, 0, 0, 0.85) !important;
                border-radius: 15px !important;
                padding: 15px !important;

                /* Blur effect for better focus */
                backdrop-filter: blur(5px);
                -webkit-backdrop-filter: blur(5px);

                /* Default hidden if not toggled by JS */
                display: none;

                /* Always enforce clipping and column layout */
                overflow: hidden !important;
                flex-direction: column !important;
            }

            /* Only apply flex layout when JS sets display: block */
            .custom-dropdown .dropdown-menu[style*="block"] {
                display: flex !important;
            }

            .dropdown-search {
                flex: 0 0 auto;
                /* Search bar fixed */
                margin-bottom: 10px;
            }

            .dropdown-list {
                flex: 1 1 auto;
                /* List takes remaining space */
                overflow-y: auto;
                /* Fallback calculation for reliable scrolling */
                max-height: calc(80vh - 80px) !important;
                padding-bottom: 50px;
                /* Extra padding */
                position: relative !important;
                min-height: 0;
                /* Soft scrolling for iOS */
                -webkit-overflow-scrolling: touch;
                overscroll-behavior: contain;
                /* Prevent background scroll */
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function () {
            // Fix z-index for mobile dropdowns to prevent overlapping
            $('.custom-dropdown').on('click', function () {
                // Only apply on mobile/tablet
                if ($(window).width() < 992) {
                    // Reset all search fields to base z-index
                    $('.search-field').css('z-index', '10');
                    // Elevate the clicked dropdown's container
                    $(this).closest('.search-field').css('z-index', '1050');
                }
            });

            // Close dropdowns when clicking outside
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.custom-dropdown').length) {
                    $('.search-field').css('z-index', '10');
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ashiqul/.valet/Sites/doctor/resources/views/frontend/home.blade.php ENDPATH**/ ?>