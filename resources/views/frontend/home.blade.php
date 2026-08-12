@extends('layouts.app')

@php
    $favDoctorIds = [];
    if (auth()->check() && auth()->user()->role === 'patient' && auth()->user()->patient) {
        $favDoctorIds = auth()->user()->patient->favouriteDoctors()->pluck('doctors.id')->toArray();
    }
@endphp

@section('title', ($siteSettings['site_name'] ?? 'abcsheba') . ' - ' . ($siteSettings['site_tagline'] ?? 'Doctor Appointment Booking'))

@section('content')
    <!-- Home Banner - DocTime Inspired -->
    <section class="section-hero-doctime">
        <!-- Background Wave Pattern -->
        <div class="hero-wave-pattern"></div>

        <div class="container">
            <!-- Hero Slider -->
            <div class="hero-slider">
                @if(isset($banners) && $banners->count() > 0)
                    @foreach($banners as $banner)
                        @if($banner->type == 'content_image')
                            <!-- Content + Image Slide -->
                            <div class="hero-slide-item">
                                <div class="hero-main-wrapper">
                                    <div class="hero-content-left">
                                        <h1 class="hero-main-title">
                                            {!! $banner->title !!}
                                        </h1>
                                        @if($banner->subtitle)
                                            <p class="mb-4 text-muted">{{ $banner->subtitle }}</p>
                                        @endif

                                        @if($banner->stats_text)
                                            <div class="hero-trust-badge">
                                                <i class="fas fa-check-circle"></i>
                                                <span>Trusted By <strong>{{ $banner->stats_text }}</strong></span>
                                            </div>
                                        @endif

                                        @if($banner->button_text && $banner->button_link)
                                            <a href="{{ $banner->button_link }}" class="btn-hero-cta">
                                                {{ $banner->button_text }} <i class="fas fa-arrow-right"></i>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="hero-content-right">
                                        <img src="{{ asset($banner->image) }}" alt="{{ $banner->title }}" class="hero-doctors-img">
                                    </div>
                                </div>
                            </div>
                        @elseif($banner->type == 'image_only')
                            <!-- Image Only Slide -->
                            <div class="hero-slide-item">
                                <div class="hero-full-image-wrapper">
                                    <img src="{{ asset($banner->image) }}" alt="Banner Image" class="hero-full-image-img">
                                    @if($banner->button_link)
                                        <a href="{{ $banner->button_link }}"
                                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 2;"></a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <!-- Fallback Static Slides (Keep original if no dynamic banners) -->
                    <!-- Slide 1 -->
                    <div class="hero-slide-item">
                        <div class="hero-main-wrapper">
                            <div class="hero-content-left">
                                <h1 class="hero-main-title">
                                    {!! $bannerSettings['banner_title'] ?? 'The Largest Online<br><span class="text-blue">Doctor Platform</span><br>Of The Country' !!}
                                </h1>
                                <div class="hero-trust-badge">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Trusted By <strong>{{ $bannerSettings['banner_stats_text'] ?? '700,000' }}</strong>
                                        Patients</span>
                                </div>
                                <a href="{{ route('doctors.search') }}" class="btn-hero-cta">
                                    Consult a Doctor Now <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <div class="hero-content-right">
                                @if(!empty($bannerSettings['banner_image']))
                                    <img src="{{ asset($bannerSettings['banner_image']) }}" alt="Professional Doctors"
                                        class="hero-doctors-img">
                                @else
                                    <img src="{{ asset('assets/img/doctors-hero.png') }}" alt="Professional Doctors"
                                        class="hero-doctors-img">
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- Other static slides can be removed or kept as backups -->
                @endif
            </div>

            <!-- Search Section -->
            <div class="hero-search-section">
                <!-- Card 1: Search by Doctor -->
                <div class="search-card-wrapper mb-3 p-3 bg-white rounded-4 shadow-sm" style="box-shadow: 0 2px 15px rgba(0,0,0,0.05) !important;">
                    <h5 class="search-box-title d-flex align-items-center gap-2 mb-3 font-weight-bold" style="font-size: 16px; color: #1e3a8a !important;">
                        <span class="icon-circle rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: #dbeafe !important; color: #3b82f6 !important;">
                            <i class="fas fa-user-md" style="font-size: 13px;"></i>
                        </span> 
                        Search by Doctor
                    </h5>
                    <!-- Normal Search Bar (Doctor Name/Code) -->
                    <div class="hero-search-bar border rounded-3 p-1" style="background: #f8fafc; border-color: #e2e8f0 !important;" id="normal-search-form">
                        <form action="{{ route('doctors.search') }}" class="hero-search-form d-flex align-items-center" id="normalSearchForm">
                            <div class="search-field search-keyword-full flex-grow-1 d-flex align-items-center px-3">
                                <i class="fas fa-user-md text-muted me-2"></i>
                                <input type="text" name="keywords" placeholder="Search by doctor name/code" class="form-control border-0 bg-transparent shadow-none" style="font-size: 14px; color: #334155;">
                            </div>
                            <button type="submit" class="btn text-white rounded-3 px-3 py-2" style="background: #3b82f6 !important; border: none !important;">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Card 2: Search by Speciality -->
                <div class="search-card-wrapper p-3 bg-white rounded-4 shadow-sm" style="box-shadow: 0 2px 15px rgba(0,0,0,0.05) !important;">
                    <h5 class="search-box-title d-flex align-items-center gap-2 mb-3 font-weight-bold" style="font-size: 16px; color: #1e3a8a !important;">
                        <span class="icon-circle rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: #dbeafe !important; color: #3b82f6 !important;">
                            <i class="fas fa-stethoscope" style="font-size: 13px;"></i>
                        </span> 
                        Search by Speciality
                    </h5>
                    <!-- Filter Search Bar (Location & Speciality) -->
                    <div class="hero-search-bar border rounded-3 p-1" style="background: #f8fafc; border-color: #e2e8f0 !important;" id="filter-search-form">
                        <form action="{{ route('doctors.search') }}" class="hero-search-form d-flex align-items-center" id="filterSearchForm">
                            <!-- Speciality - Custom Searchable Dropdown -->
                            <div class="search-field search-select flex-grow-1 border-end px-3 position-relative" style="border-color: #e2e8f0 !important; overflow: visible !important;">
                                <i class="fas fa-stethoscope text-muted me-2"></i>
                                <div class="custom-dropdown w-100 position-relative" id="specialityDropdown" style="position: relative !important;">
                                    <input type="hidden" name="speciality_id" id="speciality_value">
                                    <input type="text" class="dropdown-search border-0 bg-transparent shadow-none w-100" placeholder="Speciality"
                                        data-default-placeholder="Speciality" readonly id="speciality_display" style="font-size: 14px; color: #334155;">
                                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                                    <div class="dropdown-menu">
                                        <input type="text" class="dropdown-filter" placeholder="Search speciality...">
                                        <div class="dropdown-list">
                                            <div class="dropdown-item" data-value="">All Specialities</div>
                                            @foreach($searchSpecialities as $speciality)
                                                <div class="dropdown-item" data-value="{{ $speciality->id }}">{{ $speciality->name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- District - Custom Searchable Dropdown -->
                            <div class="search-field search-select flex-grow-1 border-end px-3 position-relative" style="border-color: #e2e8f0 !important; overflow: visible !important;">
                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                <div class="custom-dropdown w-100 position-relative" id="districtDropdown" style="position: relative !important;">
                                    <input type="hidden" name="district_id" id="district_value">
                                    <input type="text" class="dropdown-search border-0 bg-transparent shadow-none w-100" placeholder="District"
                                        data-default-placeholder="District" readonly id="district_display" style="font-size: 14px; color: #334155;">
                                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                                    <div class="dropdown-menu">
                                        <input type="text" class="dropdown-filter" placeholder="Search district...">
                                        <div class="dropdown-list">
                                            <div class="dropdown-item" data-value="">All Districts</div>
                                            @foreach($districts as $district)
                                                <div class="dropdown-item" data-value="{{ $district->id }}">{{ $district->name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Area - Custom Searchable Dropdown -->
                            <div class="search-field search-select flex-grow-1 px-3 position-relative" style="overflow: visible !important;">
                                <i class="fas fa-location-arrow text-muted me-2"></i>
                                <div class="custom-dropdown w-100 position-relative" id="areaDropdown" style="position: relative !important;">
                                    <input type="hidden" name="area_id" id="area_value">
                                    <input type="text" class="dropdown-search border-0 bg-transparent shadow-none w-100" placeholder="Area"
                                        data-default-placeholder="Area" readonly id="area_display" disabled style="font-size: 14px; color: #334155;">
                                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                                    <div class="dropdown-menu">
                                        <input type="text" class="dropdown-filter" placeholder="Search area...">
                                        <div class="dropdown-list" id="area_list">
                                            <div class="dropdown-item" data-value="">Select district first</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn text-white rounded-3 px-3 py-2 ms-2" style="background: #3b82f6 !important; border: none !important;">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
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
                        <p>Join our platform to grow your practice and manage appointments easily.</p>
                    </div>
                </div>
                <div class="doctor-cta-action">
                    <a href="{{ route('doctor.register') }}" class="btn-doctor-register">
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
            <div class="video-wrapper">
                <div class="video-container" id="telemedicineVideoContainer">
                    <div class="video-cover-wrapper" style="background-image: url('{{ asset('uploads/settings/video_cover.png') }}');" onclick="playTelemedicineVideo()">
                        <div class="video-cover-overlay"></div>
                        <div class="video-play-btn-modern">
                            <i class="fas fa-play"></i>
                            <div class="video-ripple-modern"></div>
                            <div class="video-ripple-modern video-ripple-modern-2"></div>
                            <div class="video-ripple-modern video-ripple-modern-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /Video Section -->

    <!-- Clinic and Specialities -->
    {{-- <section class="section section-specialities">
        <div class="container">
            <div class="section-header text-center">
                <h2>Browse Top Specialities</h2>
                <p class="sub-title">Explore our wide range of trusted medical departments and find the right care for you.
                </p>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-9">
                    <!-- Slider -->
                    <div class="specialities-slider slider">
                        @foreach($searchSpecialities as $speciality)
                        <!-- Slider Item -->
                        <div class="speicality-item text-center">
                            <a href="{{ route('doctors.search', ['speciality_id' => $speciality->id]) }}" class="speciality-link">
                                <div class="speicality-img">
                                    <img src="{{ asset($speciality->image) }}" class="img-fluid" alt="Speciality">
                                    <span class="hover-icon"><i class="fas fa-chevron-right"></i></span>
                                </div>
                                <p>{{ $speciality->name }}</p>
                            </a>
                        </div>
                        <!-- /Slider Item -->
                        @endforeach
                    </div>
                    <!-- /Slider -->

                </div>
            </div>
        </div>
    </section> --}}
    <!-- Clinic and Specialities -->

    <!-- Medical Products -->
    <section class="section section-products" style="background-color: #f8f9fa;">
        <div class="container">
            <div class="section-header text-center">
                <h2>Our Products</h2>
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
                            <div class="category-list" id="homeCategoryList">
                                <label class="category-item home-cat-root active-cat" id="cat-all-label">
                                    <input type="radio" name="product_category" value="all" checked>
                                    <span class="category-name">All Products</span>
                                </label>

                                @foreach($productCategories as $category)
                                    @if($category->children->isNotEmpty())
                                        {{-- Parent with children --}}
                                        <div class="home-cat-group" id="hcg-{{ $category->id }}">
                                            <label class="category-item home-cat-root"
                                                onclick="toggleHomeCat(event, 'hcg-{{ $category->id }}-children')">
                                                <input type="radio" name="product_category" value="{{ $category->id }}">
                                                <span class="category-name">{{ $category->name }}</span>
                                                <span class="home-cat-arrow ms-auto">
                                                    <i class="fas fa-chevron-down"></i>
                                                </span>
                                            </label>
                                            <div class="home-sub-list" id="hcg-{{ $category->id }}-children" style="display:none;">
                                                @foreach($category->children as $child)
                                                    @if($child->children->isNotEmpty())
                                                        {{-- Sub with grandchildren --}}
                                                        <div class="home-cat-group" id="hcg-{{ $child->id }}">
                                                            <label class="category-item home-cat-sub"
                                                                onclick="toggleHomeCat(event, 'hcg-{{ $child->id }}-children')">
                                                                <input type="radio" name="product_category" value="{{ $child->id }}">
                                                                <span class="category-name">{{ $child->name }}</span>
                                                                <span class="home-cat-arrow ms-auto">
                                                                    <i class="fas fa-chevron-down"></i>
                                                                </span>
                                                            </label>
                                                            <div class="home-sub-list home-subsub-list" id="hcg-{{ $child->id }}-children" style="display:none;">
                                                                @foreach($child->children as $grandchild)
                                                                    <label class="category-item home-cat-subsub">
                                                                        <input type="radio" name="product_category" value="{{ $grandchild->id }}">
                                                                        <span class="category-name">{{ $grandchild->name }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @else
                                                        <label class="category-item home-cat-sub">
                                                            <input type="radio" name="product_category" value="{{ $child->id }}">
                                                            <span class="category-name">{{ $child->name }}</span>
                                                        </label>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        {{-- Leaf category --}}
                                        <label class="category-item home-cat-root">
                                            <input type="radio" name="product_category" value="{{ $category->id }}">
                                            <span class="category-name">{{ $category->name }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Sidebar Filter -->

                <!-- Products Grid -->
                <div class="col-lg-9 col-md-8">
                    <div class="row" id="productsGrid">
                        @foreach($products as $product)
                            @php
                                $availableStock = $product->availableStock();
                                $hasVariants = $product->hasActiveVariants();
                                $displayPrice = $product->effectivePrice();
                                $displayRegularPrice = $product->effectiveRegularPrice();
                                $productReviewCount = (int) ($product->reviews_count ?? 0);
                                $productRating = $productReviewCount > 0 ? (float) ($product->rating ?? 0) : 0;
                            @endphp
                            <div class="col-lg-4 col-md-6 col-sm-6 col-6 mb-4 product-grid-item">
                                <div class="product-card-modern">
                                    <!-- Product Image -->
                                    <div class="product-image-container">
                                        <!-- Stock Badge -->
                                        <div class="stock-badge {{ $availableStock > 0 ? 'in-stock' : 'out-of-stock' }}">
                                            {{ $availableStock > 0 ? 'IN STOCK' : 'OUT OF STOCK' }}
                                        </div>
                                        <a href="{{ route('ecommerce.products.show', $product->id) }}" class="product-image-link">
                                            @php
                                                $image = $product->image;
                                                if (!$image && !empty($product->gallery) && is_array($product->gallery)) {
                                                    $image = $product->gallery[0] ?? null;
                                                }
                                            @endphp
                                            <img src="{{ $image ? asset($image) : asset('assets/img/products/default-product.png') }}"
                                                class="product-main-img" alt="{{ $product->name }}">
                                        </a>
                                    </div>

                                    <!-- Product Details -->
                                    <div class="product-details">
                                        <!-- Rating -->
                                        <div class="product-rating">
                                            <i class="fas fa-star"></i>
                                            <span class="rating-value">{{ number_format($productRating, 1) }}</span>
                                            <span class="review-count">({{ $productReviewCount }})</span>
                                        </div>

                                        <!-- Brand/Category -->
                                        <div class="product-brand">{{ $product->category->name ?? 'Medicine' }}</div>

                                        <!-- Title -->
                                        <h4 class="product-name">
                                            <a href="{{ route('ecommerce.products.show', $product->id) }}" wire:navigate>{{ $product->name }}</a>
                                        </h4>

                                        <!-- Price & Actions -->
                                        <div class="product-footer">
                                            <div class="product-price-tag">
                                                @if($hasVariants)
                                                    <span class="d-block text-muted small mb-1">From</span>
                                                @endif

                                                <span class="price-current">৳{{ number_format($displayPrice, 0) }}</span>

                                                @if($displayPrice < $displayRegularPrice)
                                                    <span class="price-original">৳{{ number_format($displayRegularPrice, 0) }}</span>
                                                @endif
                                            </div>

                                            @if($hasVariants)
                                                <a href="{{ route('ecommerce.products.show', $product->id) }}" class="btn-buy-modern btn-link-modern">
                                                    Select Options
                                                </a>
                                            @else
                                                <form action="{{ route('ecommerce.cart.add') }}" method="POST" class="product-actions-form">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <div class="btn-group-modern">
                                                        <button type="submit" class="btn-cart-modern" title="Add to Cart" {{ $availableStock < 1 ? 'disabled' : '' }}>
                                                            <i class="fas fa-shopping-cart"></i>
                                                        </button>
                                                        <button type="submit" name="buy_now" value="1" class="btn-buy-modern" {{ $availableStock < 1 ? 'disabled' : '' }}>
                                                            Buy
                                                        </button>
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- View All Button -->
                    <div class="text-center mt-4">
                        <a href="{{ route('ecommerce.products') }}" class="btn-view-all-arrow">
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
                            <div class="select-wrapper">
                                <select name="doctor_speciality" id="doctorSpecialitySelect" class="form-control form-select select-modern">
                                    <option value="all">All Doctors</option>
                                    @foreach($searchSpecialities as $speciality)
                                        <option value="{{ $speciality->id }}">{{ $speciality->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Sidebar Filter -->

                <!-- Doctors Grid -->
                <div class="col-lg-9 col-md-8">
                    <div class="row" id="doctorsGrid">
                        @foreach($doctors as $doctor)
                            <div class="col-lg-4 col-md-6 col-sm-6 mb-4 doctor-grid-item">
                                <div class="doctor-card-new">
                                    <div class="doctor-img-wrapper">
                                        <a href="{{ route('doctors.profile', $doctor->id) }}">
                                            <img src="{{ $doctor->profile_image ? asset($doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-01.jpg') }}"
                                                class="doctor-img" alt="{{ $doctor->user->name }}">
                                        </a>
                                        <div class="doctor-fee-badge">
                                            <span>৳
                                                {{ $doctor->pricing === 'free' ? 'Free' : number_format($doctor->custom_price ?: ($doctor->consultation_fee ?: 500), 0) }}</span>
                                        </div>
                                        <a href="javascript:void(0)" class="fav-btn {{ in_array($doctor->id, $favDoctorIds) ? 'active' : '' }}" data-id="{{ $doctor->id }}">
                                            <i class="{{ in_array($doctor->id, $favDoctorIds) ? 'fas' : 'far' }} fa-bookmark"></i>
                                        </a>
                                    </div>
                                    <div class="doctor-info">
                                        <span class="doctor-speciality">{{ $doctor->speciality->name ?? 'General' }}</span>
                                        <h4 class="doctor-name">
                                            <a href="{{ route('doctors.profile', $doctor->id) }}">{{ $doctor->user->name }}</a>
                                            <i class="fas fa-check-circle verified-badge" title="Verified"></i>
                                        </h4>
                                        <div class="doctor-rating">
                                            <i class="fas fa-star"></i>
                                            <span>{{ number_format($doctor->average_rating, 1) }}</span>
                                            <span class="rating-count">({{ $doctor->review_count }} reviews)</span>
                                        </div>
                                        <div class="doctor-location">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>{{ $doctor->clinic_name ?? ($doctor->area->name ?? 'Dhaka') }}</span>
                                        </div>
                                        <div class="doctor-buttons">
                                            <a href="{{ route('doctors.profile', $doctor->id) }}" class="btn-view-details">
                                                <i class="fas fa-user"></i> Details
                                            </a>
                                            <a href="{{ route('booking', $doctor->id) }}" class="btn-book-appointment">
                                                <i class="fas fa-calendar-check"></i> Appointment
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- View All Button -->
                    <div class="text-center mt-4">
                        <a href="{{ route('doctors.search') }}" class="btn-view-all-arrow">
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
    @if(isset($healthPackages) && $healthPackages->count() > 0)
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
                @foreach($healthPackages as $package)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="health-package-card {{ $package->is_featured ? 'featured' : '' }}">
                        @if($package->is_featured)
                            <div class="featured-ribbon">Most Popular</div>
                        @endif
                        <div class="package-icon">
                            <i class="{{ $package->icon }}"></i>
                        </div>
                        <div class="package-badge">{{ $package->badge_label }}</div>
                        <h4 class="package-title">{{ $package->title }}</h4>
                        <p class="package-tests"><i class="fas fa-vial"></i> {{ $package->test_count }}+ Tests Included</p>
                        @if(is_array($package->features) && count($package->features) > 0)
                        <ul class="package-features">
                            @foreach($package->features as $feature)
                                <li><i class="fas fa-check"></i> {{ $feature }}</li>
                            @endforeach
                        </ul>
                        @endif
                        <div class="package-price">
                            <span class="price">৳{{ number_format($package->price, 0) }}</span>
                            <span class="period">{{ $package->price_label }}</span>
                        </div>
                        <a href="{{ $package->link ?? route('ecommerce.products') }}" class="btn-package">
                            Book Now <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- View All Button -->
            <div class="text-center mt-4">
                <a href="{{ route('ecommerce.products') }}" class="btn-view-all-arrow">
                    View All Packages <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
    @endif
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
                            <img src="{{ asset('assets/img/features/feature-01.jpg') }}" alt="Diabetes Management">
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
                                    <img src="{{ asset('assets/img/doctors/doctor-thumb-01.jpg') }}" alt="Instructor">
                                    <span>Dr. Sarah Wilson</span>
                                </div>
                                <a href="{{ route('ecommerce.products') }}" class="btn-enroll">Enroll <i
                                        class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course 2 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="course-card">
                        <div class="course-thumbnail">
                            <img src="{{ asset('assets/img/features/feature-02.jpg') }}" alt="Heart Health">
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
                                    <img src="{{ asset('assets/img/doctors/doctor-thumb-02.jpg') }}" alt="Instructor">
                                    <span>Dr. John Smith</span>
                                </div>
                                <a href="{{ route('ecommerce.products') }}" class="btn-enroll">Enroll <i
                                        class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course 3 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="course-card">
                        <div class="course-thumbnail">
                            <img src="{{ asset('assets/img/features/feature-03.jpg') }}" alt="Mental Health">
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
                                    <img src="{{ asset('assets/img/doctors/doctor-thumb-03.jpg') }}" alt="Instructor">
                                    <span>Dr. Emily Brown</span>
                                </div>
                                <a href="{{ route('ecommerce.products') }}" class="btn-enroll">Enroll <i
                                        class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-4">
                <a href="{{ route('ecommerce.products') }}" class="btn-view-all-arrow">
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
                        <p class="mb-4 text-muted">abcsheba provides progressive, and affordable healthcare, accessible on
                            mobile and online for everyone. To us, it's not just work. We take pride in the solutions we
                            deliver</p>

                        <ul class="video-promo-list list-unstyled mb-4">
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Leading Healthcare Provider</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> 24/7 Support Available</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Experienced Doctors</li>
                        </ul>

                        <a href="{{ route('doctors.search') }}" class="btn btn-primary">Book Now</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="video-promo-box">
                        <img src="{{ asset('assets/img/features/feature.png') }}" alt="Video Thumbnail"
                            class="img-fluid" style="background: transparent;">
                        <a href="https://www.youtube.com/watch?v=zNHq9gD2uqc" data-fancybox class="video-play-btn">
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
                                <img src="{{ asset('assets/img/img-01.jpg') }}" class="img-fluid" alt="Blog Image" style="width: 100%; height: 200px; object-fit: cover;">
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
                                <img src="{{ asset('assets/img/img-02.jpg') }}" class="img-fluid" alt="Blog Image" style="width: 100%; height: 200px; object-fit: cover;">
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
                                <img src="{{ asset('assets/img/img-03.jpg') }}" class="img-fluid" alt="Blog Image" style="width: 100%; height: 200px; object-fit: cover;">
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
                                <img src="{{ asset('assets/img/img-04.jpg') }}" class="img-fluid" alt="Blog Image" style="width: 100%; height: 200px; object-fit: cover;">
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
                                booking process was seamless. Highly recommend abcsheba to everyone."</p>
                            <div class="d-flex align-items-center mt-4">
                                <img src="{{ asset('assets/img/patients/patient1.jpg') }}" class="rounded-circle me-3"
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
                            <p class="card-text text-muted">"Found the best dentist through abcsheba. The platform is easy to
                                use and the doctor profiles are very detailed. Great experience!"</p>
                            <div class="d-flex align-items-center mt-4">
                                <img src="{{ asset('assets/img/patients/patient2.jpg') }}" class="rounded-circle me-3"
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
                                <img src="{{ asset('assets/img/patients/patient3.jpg') }}" class="rounded-circle me-3"
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
            <p class="lead mb-5 text-white-50">Join thousands of patients who trust abcsheba for their healthcare needs.</p>
            <a href="{{ route('doctors.search') }}" class="btn btn-light cta-btn">
                <i class="fas fa-calendar-check me-2"></i> Find a Doctor Now
            </a>
        </div>
    </section>
    <!-- /Call to Action -->
    <!-- /Call to Action -->

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Tab Switcher for Homepage Search - Disabled as search boxes are now stacked vertically

            var favDoctorIds = {!! json_encode($favDoctorIds) !!};
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
                    var imageSrc = product.image_url || (product.image ? (product.image.startsWith('http') ? product.image : '/' + product.image.replace(/^\/+/, '')) : '/assets/img/products/default-product.png');

                    var priceHtml = '';
                    if (product.has_variants) {
                        priceHtml += '<span class="d-block text-muted small mb-1">From</span>';
                    }

                    if (product.sale_price) {
                        priceHtml = '<span class="price-current">৳' + numberFormat(product.sale_price) + '</span>' +
                            '<span class="price-original">৳' + numberFormat(product.regular_price) + '</span>';
                    } else {
                        priceHtml = '<span class="price-current">৳' + numberFormat(product.price) + '</span>';
                    }

                    if (product.has_variants) {
                        priceHtml = '<span class="d-block text-muted small mb-1">From</span>' + priceHtml;
                    }

                    var stockClass = (product.stock > 0) ? 'in-stock' : 'out-of-stock';
                    var stockText = (product.stock > 0) ? 'IN STOCK' : 'OUT OF STOCK';
                    var rating = product.rating === null || product.rating === undefined ? 0 : Number(product.rating);
                    var reviewCount = product.reviews_count === null || product.reviews_count === undefined ? 0 : Number(product.reviews_count);
                    var categoryName = product.category ? product.category.name : 'Medicine';
                    var actionHtml = product.has_variants
                        ? `<a href="/products/${product.id}" class="btn-buy-modern btn-link-modern">Select Options</a>`
                        : `<form action="/cart/add" method="POST" class="product-actions-form">
                                <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                                <input type="hidden" name="product_id" value="${product.id}">
                                <input type="hidden" name="quantity" value="1">
                                <div class="btn-group-modern">
                                    <button type="submit" class="btn-cart-modern" title="Add to Cart" ${product.stock < 1 ? 'disabled' : ''}>
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                    <button type="submit" name="buy_now" value="1" class="btn-buy-modern" ${product.stock < 1 ? 'disabled' : ''}>
                                        Buy
                                    </button>
                                </div>
                            </form>`;

                    var html = `
                                                                                                                <div class="col-lg-4 col-md-6 col-sm-6 col-6 mb-4 product-grid-item">
                                                                                                                    <div class="product-card-modern">
                                                                                                                        <div class="stock-badge ${stockClass}">${stockText}</div>
                                                                                                                        <div class="product-image-container">
                                                                                                                            <a href="/products/${product.id}" class="product-image-link">
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
                                                                                                                                ${actionHtml}
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

            // Nested category tree toggle
            var lastToggleTimes = {};
            window.toggleHomeCat = function(event, childListId) {
                var now = Date.now();
                if (lastToggleTimes[childListId] && (now - lastToggleTimes[childListId] < 100)) {
                    return;
                }
                lastToggleTimes[childListId] = now;

                var childList = document.getElementById(childListId);
                if (!childList) return;

                var isOpen = childList.style.display !== 'none';
                childList.style.display = isOpen ? 'none' : 'block';

                // Toggle arrow icon
                var arrow = event.currentTarget.querySelector('.home-cat-arrow i');
                if (arrow) {
                    if (isOpen) {
                        arrow.className = 'fas fa-chevron-down';
                    } else {
                        arrow.className = 'fas fa-chevron-up';
                    }
                }
            };

            // Category filter change — update active class + filter products
            $('body').on('change', 'input[name="product_category"]', function () {
                // Remove active from all root labels
                document.querySelectorAll('#homeCategoryList .category-item').forEach(function(el) {
                    el.classList.remove('active-cat');
                });
                // Add active to parent label of checked input
                var parentLabel = this.closest('label.category-item');
                if (parentLabel) parentLabel.classList.add('active-cat');

                // Auto-expand parent group if subcategory clicked
                var parentGroup = this.closest('.home-sub-list');
                if (parentGroup && parentGroup.style.display === 'none') {
                    parentGroup.style.display = 'block';
                }

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
                var speciality = $('#doctorSpecialitySelect').val();
                var search = $('#doctorSearchInput').val();

                $.ajax({
                    url: '{{ route('api.doctors.filter') }}',
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
                    var isFav = favDoctorIds.includes(doctor.id);
                    var favClass = isFav ? 'active' : '';
                    var favIconClass = isFav ? 'fas' : 'far';

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
                                                                                                                            <a href="javascript:void(0)" class="fav-btn ${favClass}" data-id="${doctor.id}">
                                                                                                                                <i class="${favIconClass} fa-bookmark"></i>
                                                                                                                            </a>
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
            $('#doctorSpecialitySelect').on('change', function () {
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
@endpush

@push('styles')
    <style>
        /* Modern styled dropdown select */
        .select-modern {
            width: 100%;
            height: 46px;
            padding: 0 15px;
            font-size: 14px;
            color: #333;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231D4ED8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .select-modern:focus {
            border-color: #1D4ED8;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
            background-color: #fff;
        }

        /* Stacked Search Bar styles */
        .hero-search-bar {
            margin-bottom: 12px;
        }
        .hero-search-bar:last-child {
            margin-bottom: 0;
        }

        .search-box-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .search-box-title:first-of-type {
            margin-top: 0;
        }
        .search-box-title i {
            color: #2563eb;
            font-size: 18px;
        }

        /* Responsive styling for normal search keywords input to look modern */
        .search-field.search-keyword-full {
            flex: 1;
            border-right: none !important;
        }

        @media (max-width: 991px) {
            .search-field.search-keyword-full {
                flex: 1 1 100% !important;
                border-bottom: none !important;
            }
        }

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
            .section-doctor-cta {
                padding: 15px 10px !important;
                margin: 15px 15px !important;
                border-radius: 12px !important;
            }

            .doctor-cta-wrapper {
                flex-direction: column;
                text-align: center;
                gap: 12px !important;
            }

            .doctor-cta-content {
                flex-direction: column;
                gap: 10px !important;
            }

            .doctor-cta-icon {
                width: 44px !important;
                height: 44px !important;
            }

            .doctor-cta-icon i {
                font-size: 20px !important;
            }

            .doctor-cta-text h3 {
                font-size: 18px !important;
                margin-bottom: 4px !important;
            }

            .doctor-cta-text p {
                font-size: 12px !important;
                max-width: 100%;
                line-height: 1.4 !important;
            }

            .btn-doctor-register {
                padding: 10px 22px !important;
                font-size: 13px !important;
            }

            .btn-doctor-register i {
                font-size: 14px !important;
            }

            /* Reduce section spacing on mobile */
            .section, 
            .section-products, 
            .section-health-packages, 
            .section-courses, 
            .section-video, 
            .section-services {
                padding: 30px 0 !important;
            }

            .section-video {
                margin: 10px 0 !important;
            }
        }

        @media (max-width: 480px) {
            .doctor-cta-text h3 {
                font-size: 16px !important;
            }
            .doctor-cta-text p {
                font-size: 11px !important;
            }
            .btn-doctor-register {
                padding: 8px 18px !important;
                font-size: 12px !important;
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

        .video-cover-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s ease;
        }

        .video-cover-wrapper:hover {
            transform: scale(1.02);
        }

        .video-cover-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(36, 105, 237, 0.2);
            transition: background 0.3s ease;
        }

        .video-cover-wrapper:hover .video-cover-overlay {
            background: rgba(36, 105, 237, 0.3);
        }

        /* Pulsing Play Button */
        .video-play-btn-modern {
            position: relative;
            width: 80px;
            height: 80px;
            background: #2469ed;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 26px;
            box-shadow: 0 10px 30px rgba(36, 105, 237, 0.5);
            transition: all 0.3s ease;
            z-index: 2;
        }

        .video-cover-wrapper:hover .video-play-btn-modern {
            transform: scale(1.1);
            background: #1052d1;
            box-shadow: 0 10px 35px rgba(16, 82, 209, 0.6);
        }

        .video-play-btn-modern i {
            margin-left: 5px; /* offset play icon slightly to align visually */
        }

        /* Ripple Animations */
        .video-ripple-modern {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 2px solid rgba(36, 105, 237, 0.6);
            border-radius: 50%;
            animation: play-ripple 2s infinite ease-out;
            pointer-events: none;
            opacity: 0;
        }

        .video-ripple-modern-2 {
            animation-delay: 0.6s;
        }

        .video-ripple-modern-3 {
            animation-delay: 1.2s;
        }

        @keyframes play-ripple {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(2.2);
                opacity: 0;
            }
        }

        /* Info overlay */
        .video-info-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 20px;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.7) 100%);
            color: #fff;
            text-align: left;
            z-index: 2;
        }

        .video-info-overlay h4 {
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 5px 0;
            color: #fff;
        }

        .video-info-overlay p {
            font-size: 14px;
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
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

        /* ===== Nested Home Category Tree ===== */
        .home-cat-group {
            margin-bottom: 2px;
        }

        .home-cat-root {
            font-weight: 600;
            color: #1e293b;
        }

        .home-cat-root.active-cat {
            background: #eff6ff;
            color: #1D4ED8;
        }

        .home-cat-arrow {
            font-size: 10px;
            color: #94a3b8;
            transition: transform 0.25s ease;
            pointer-events: none;
        }

        .home-cat-arrow.open {
            transform: rotate(180deg);
        }

        .home-sub-list {
            padding-left: 12px;
            border-left: 2px solid #e2e8f0;
            margin-left: 10px;
            margin-top: 2px;
            margin-bottom: 4px;
        }

        .home-subsub-list {
            padding-left: 10px;
            border-left: 2px dashed #cbd5e1;
            margin-left: 8px;
        }

        .home-cat-sub {
            font-size: 13px !important;
            padding: 7px 10px !important;
            color: #475569;
        }

        .home-cat-sub:hover {
            background: #f0f9ff;
            color: #2563eb;
        }

        .home-cat-subsub {
            font-size: 12px !important;
            padding: 5px 8px !important;
            color: #64748b;
        }

        .home-cat-subsub:hover {
            background: #f8fafc;
            color: #2563eb;
        }

        .category-list {
            max-height: 420px;
            overflow-y: auto;
            padding-right: 2px;
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

        .product-image-link {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .product-main-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            transition: transform 0.3s ease;
        }

        .product-card-modern:hover .product-main-img {
            transform: scale(1.03);
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

        .btn-link-modern {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
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
            left: 15px;
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
            font-size: 13px;
            color: #1D4ED8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            font-weight: 600;
            display: inline-block;
            background: #e8f4ff;
            padding: 5px 12px;
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
            padding: 36px 0 120px 0;
            min-height: auto;
        }

        .section-hero-doctime .container {
            position: relative;
        }

        .hero-slider {
            margin-bottom: 0;
        }

        .hero-main-wrapper {
            min-height: 460px;
            max-height: none;
            overflow: hidden;
            border-radius: 20px;
        }

        .section-hero-doctime {
            padding-bottom: 150px !important;
        }

        .hero-search-section {
            position: absolute !important;
            left: 12px !important;
            right: 12px !important;
            bottom: -110px !important;
            transform: none !important;
            margin: 0 !important;
            z-index: 500 !important;
        }

        .hero-search-bar {
            border-radius: 18px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
        }

        .hero-doctors-img {
            max-height: 460px;
            width: auto;
            object-fit: contain;
        }

        /* Responsive image-only banner style */
        .hero-full-image-wrapper {
            width: 100%;
            height: 460px;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
        }

        .hero-full-image-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            border-radius: 20px;
            display: block;
        }

        /* Mobile Dropdown Popup Style */
        @media (max-width: 991px) {
            .section-hero-doctime {
                padding: 20px 0 10px 0 !important;
            }

            .hero-main-wrapper {
                flex-direction: row !important;
                text-align: left;
                min-height: 280px;
                gap: 20px;
                align-items: center;
                justify-content: space-between;
                padding: 0 15px;
            }

            .hero-content-left {
                max-width: 55%;
                flex: 1.2;
            }

            .hero-content-right {
                max-width: 45%;
                flex: 0.8;
                text-align: right;
            }

            .hero-doctors-img {
                max-height: 280px;
                width: 100%;
                object-fit: contain;
            }

            .hero-main-title {
                font-size: 28px !important;
                margin-bottom: 12px !important;
            }

            .hero-full-image-wrapper {
                height: auto;
                border-radius: 15px;
            }

            .hero-full-image-img {
                height: auto;
                max-height: 400px;
                object-fit: cover;
                border-radius: 15px;
            }

            .hero-search-section {
                position: relative !important;
                left: auto !important;
                right: auto !important;
                bottom: auto !important;
                transform: none !important;
                margin-top: 15px !important;
            }

            .section-hero-doctime {
                padding-bottom: 20px !important;
            }

            .hero-search-bar {
                padding: 6px 10px !important;
                border-radius: 14px !important;
            }

            /* Doctor Search Form - Keep compact on one row */
            #normalSearchForm {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                width: 100% !important;
                gap: 10px !important;
            }

            #normalSearchForm .search-field {
                flex: 1 !important;
                width: auto !important;
                border-right: none !important;
                border-bottom: none !important;
                padding: 6px 5px !important;
            }

            #normalSearchForm .search-field .form-control {
                font-size: 13px !important;
            }

            #normalSearchForm .search-field i {
                font-size: 13px !important;
            }

            #normalSearchForm .btn-hero-search {
                width: 38px !important;
                height: 38px !important;
                margin-top: 0 !important;
                flex-shrink: 0 !important;
                border-radius: 8px !important;
                font-size: 14px !important;
            }

            /* Speciality Search Form - Stacked layout */
            #filterSearchForm {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 0 !important;
            }

            #filterSearchForm .search-field {
                flex: 1 1 100% !important;
                width: 100% !important;
                border-right: none !important;
                border-bottom: 1px solid #f1f5f9 !important;
                padding: 10px 5px !important;
            }

            #filterSearchForm .search-field input,
            #filterSearchForm .search-field .dropdown-search {
                font-size: 13px !important;
            }

            #filterSearchForm .search-field i {
                font-size: 13px !important;
            }

            #filterSearchForm .search-field:last-of-type {
                border-bottom: none !important;
            }

            #filterSearchForm .btn-hero-search {
                width: 100% !important;
                margin-top: 10px !important;
                height: 38px !important;
                border-radius: 8px !important;
                font-size: 14px !important;
            }
        }

        @media (max-width: 767px) {
            .hero-main-wrapper {
                min-height: 200px;
                gap: 15px;
            }
            .hero-doctors-img {
                max-height: 180px;
            }
            .hero-main-title {
                font-size: 18px !important;
                margin-bottom: 8px !important;
                line-height: 1.3 !important;
            }
            .hero-content-left p {
                font-size: 12px !important;
                margin-bottom: 10px !important;
                line-height: 1.4 !important;
            }
            .btn-hero-cta {
                padding: 8px 16px !important;
                font-size: 12px !important;
                border-radius: 6px !important;
            }
            .hero-trust-badge {
                padding: 6px 12px !important;
                font-size: 11px !important;
                margin-bottom: 10px !important;
            }
            .hero-full-image-img {
                max-height: 350px;
            }
        }

        @media (max-width: 575px) {
            .hero-main-wrapper {
                min-height: 160px;
                gap: 10px;
                padding: 0 10px;
            }
            .hero-doctors-img {
                max-height: 140px;
            }
            .hero-main-title {
                font-size: 14px !important;
                line-height: 1.2 !important;
                margin-bottom: 6px !important;
            }
            .hero-content-left p {
                font-size: 10px !important;
                margin-bottom: 8px !important;
                line-height: 1.3 !important;
            }
            .btn-hero-cta {
                padding: 6px 12px !important;
                font-size: 10px !important;
            }
            .hero-trust-badge {
                padding: 4px 8px !important;
                font-size: 10px !important;
                margin-bottom: 6px !important;
            }
            .hero-full-image-img {
                max-height: 300px;
            }

            /* Product Card Mobile 2-Column Responsive Styling */
            .product-card-modern .product-image-container {
                height: 130px !important;
            }
            .product-card-modern .product-image-link {
                padding: 8px !important;
            }
            .product-card-modern .product-details {
                padding: 10px !important;
            }
            .product-card-modern .stock-badge {
                top: 8px !important;
                left: 8px !important;
                padding: 2px 6px !important;
                font-size: 8px !important;
            }
            .product-card-modern .product-rating {
                font-size: 11px !important;
                margin-bottom: 4px !important;
            }
            .product-card-modern .product-brand {
                font-size: 10px !important;
                margin-bottom: 4px !important;
            }
            .product-card-modern .product-name {
                font-size: 12px !important;
                margin-bottom: 8px !important;
                min-height: 34px !important;
                -webkit-line-clamp: 2 !important;
            }
            .product-card-modern .product-footer {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px !important;
            }
            .product-card-modern .product-price-tag {
                flex-direction: row !important;
                align-items: baseline !important;
                gap: 6px !important;
            }
            .product-card-modern .price-current {
                font-size: 15px !important;
            }
            .product-card-modern .price-original {
                font-size: 11px !important;
            }
            .product-card-modern .btn-group-modern {
                width: 100% !important;
            }
            .product-card-modern .btn-buy-modern {
                padding: 0 10px !important;
                height: 32px !important;
                font-size: 12px !important;
                flex: 1 !important;
            }
            .product-card-modern .btn-cart-modern {
                width: 32px !important;
                height: 32px !important;
                border-radius: 6px !important;
            }
            .product-card-modern .btn-cart-modern i {
                font-size: 12px !important;
            }
        }

        @media (max-width: 480px) {
            .hero-main-wrapper {
                min-height: 130px;
            }
            .hero-doctors-img {
                max-height: 110px;
            }
            .hero-main-title {
                font-size: 13px !important;
                line-height: 1.2 !important;
                margin-bottom: 4px !important;
            }
            .hero-content-left p {
                display: none !important;
            }
            .hero-trust-badge {
                display: none !important;
            }
            .btn-hero-cta {
                padding: 4px 10px !important;
                font-size: 9px !important;
            }
            .hero-full-image-img {
                max-height: 250px;
            }
        }

            .custom-dropdown {
                position: relative !important;
            }

            .custom-dropdown .dropdown-menu {
                position: absolute !important;
                top: calc(100% + 6px) !important;
                left: 0 !important;
                transform: none !important;
                width: 100% !important;
                min-width: 220px !important;
                max-height: 300px !important;
                z-index: 1050 !important;
                background: #ffffff !important;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12) !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 10px !important;
                padding: 10px !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
                display: none;
                overflow: hidden !important;
                flex-direction: column !important;
            }

            .custom-dropdown.open .dropdown-menu,
            .custom-dropdown .dropdown-menu[style*="block"] {
                display: flex !important;
            }

            .dropdown-search {
                flex: 0 0 auto;
                margin-bottom: 6px;
            }

            .dropdown-list {
                flex: 1 1 auto;
                overflow-y: auto;
                max-height: 220px !important;
                padding-bottom: 0 !important;
                position: relative !important;
                min-height: 0;
                -webkit-overflow-scrolling: touch;
                overscroll-behavior: contain;
            }
    </style>
@endpush

@push('scripts')
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

        function playTelemedicineVideo() {
            const container = document.getElementById('telemedicineVideoContainer');
            container.innerHTML = `
                <iframe src="https://www.youtube.com/embed/zNHq9gD2uqc?autoplay=1&rel=0" title="Platform Introduction Video"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            `;
        }
    </script>
@endpush
