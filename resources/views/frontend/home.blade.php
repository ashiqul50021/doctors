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
            <!-- Combined Hero Wrapper -->
            <div class="hero-combined-wrapper">
                <!-- Hero Banner Skeleton (Hidden when Slick initializes) -->
                <div class="hero-banner-skeleton d-none" id="heroBannerSkeleton">
                    <div class="skeleton-hero-left">
                        <div class="skeleton-hero-title-1 skeleton-shimmer"></div>
                        <div class="skeleton-hero-title-2 skeleton-shimmer"></div>
                        <div class="skeleton-hero-badge skeleton-shimmer"></div>
                        <div class="skeleton-hero-btn skeleton-shimmer"></div>
                    </div>
                    <div class="skeleton-hero-right skeleton-shimmer"></div>
                </div>

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
                    <div class="search-card-wrapper mb-2 p-2 bg-white shadow-sm">
                        <h5 class="search-box-title d-flex align-items-center gap-2 mb-2 font-weight-bold">
                            <span class="icon-circle d-inline-flex align-items-center justify-content-center">
                                <i class="fas fa-user-md"></i>
                            </span> 
                            <span>Search by Doctor</span>
                        </h5>
                        <!-- Normal Search Bar (Doctor Name/Code) -->
                        <div class="hero-search-bar" id="normal-search-form">
                            <form action="{{ route('doctors.search') }}" class="hero-search-form d-flex align-items-center" id="normalSearchForm">
                                <div class="search-field search-keyword-full flex-grow-1 d-flex align-items-center px-2">
                                    <i class="fas fa-search search-lead-icon me-2"></i>
                                    <input type="text" name="keywords" placeholder="Search doctor by name, specialty, or code..." class="form-control border-0 bg-transparent shadow-none">
                                </div>
                                <button type="submit" class="btn btn-search-action text-white" aria-label="Search Doctor">
                                    <i class="fas fa-search"></i>
                                    <span class="d-none d-md-inline ms-1">Search</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Card 2: Search by Speciality & Location -->
                    <div class="search-card-wrapper p-2 bg-white shadow-sm">
                        <h5 class="search-box-title d-flex align-items-center gap-2 mb-2 font-weight-bold">
                            <span class="icon-circle d-inline-flex align-items-center justify-content-center">
                                <i class="fas fa-stethoscope"></i>
                            </span> 
                            <span>Search by Speciality & Location</span>
                        </h5>
                        <!-- Filter Search Bar (Location & Speciality) -->
                        <div class="hero-search-bar" id="filter-search-form">
                            <form action="{{ route('doctors.search') }}" class="hero-search-form d-flex align-items-center" id="filterSearchForm">
                                <!-- Speciality - Custom Searchable Dropdown -->
                                <div class="search-field search-select flex-grow-1 px-2 position-relative">
                                    <i class="fas fa-stethoscope search-lead-icon me-2"></i>
                                    <div class="custom-dropdown w-100 position-relative" id="specialityDropdown">
                                        <input type="hidden" name="speciality_id" id="speciality_value">
                                        <input type="text" class="dropdown-search border-0 bg-transparent shadow-none w-100" placeholder="Speciality (e.g. Cardiologist)"
                                            data-default-placeholder="Speciality (e.g. Cardiologist)" readonly id="speciality_display">
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

                                <div class="search-divider-line d-none d-md-block"></div>

                                <!-- District - Custom Searchable Dropdown -->
                                <div class="search-field search-select flex-grow-1 px-2 position-relative">
                                    <i class="fas fa-map-marker-alt search-lead-icon me-2"></i>
                                    <div class="custom-dropdown w-100 position-relative" id="districtDropdown">
                                        <input type="hidden" name="district_id" id="district_value">
                                        <input type="text" class="dropdown-search border-0 bg-transparent shadow-none w-100" placeholder="District (e.g. Dhaka)"
                                            data-default-placeholder="District (e.g. Dhaka)" readonly id="district_display">
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

                                <div class="search-divider-line d-none d-md-block"></div>

                                <!-- Area - Custom Searchable Dropdown -->
                                <div class="search-field search-select flex-grow-1 px-2 position-relative">
                                    <i class="fas fa-location-arrow search-lead-icon me-2"></i>
                                    <div class="custom-dropdown w-100 position-relative" id="areaDropdown">
                                        <input type="hidden" name="area_id" id="area_value">
                                        <input type="text" class="dropdown-search border-0 bg-transparent shadow-none w-100" placeholder="Area"
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

                                <button type="submit" class="btn btn-search-action text-white" aria-label="Search by Filters">
                                    <i class="fas fa-search"></i>
                                    <span class="d-none d-md-inline ms-1">Find</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Cards Removed -->
        </div>
    </section>
    <!-- /Home Banner -->

    @if(!empty($homeCampaign) && $homeCampaign->isRunning())
        <!-- Live Campaign Flash Deal Section -->
        <section class="section-home-campaign py-4 my-2">
            <div class="container">
                <div class="card border-0 rounded-4 overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #1e1b4b 0%, #31104b 50%, #4c0519 100%); color: #ffffff;">
                    <div class="p-4 p-md-5">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-5">
                                <span class="badge bg-danger text-white px-3 py-1.5 rounded-pill mb-2 fw-bold">
                                    <i class="fas fa-bolt me-1"></i> LIVE FLASH DEAL
                                </span>
                                <h2 class="fw-bold text-white display-6 mb-2">{{ $homeCampaign->title }}</h2>
                                <p class="text-light opacity-75 mb-3">{{ $homeCampaign->description ?: 'Exclusive mega discount offers for a limited time. Don’t miss out!' }}</p>

                                <!-- Live Countdown -->
                                <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-10 backdrop-blur p-2.5 rounded-3 border border-white border-opacity-20 mb-4">
                                    <span class="text-white small fw-bold me-1"><i class="fas fa-clock me-1"></i> Ends In:</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="badge bg-danger text-white px-2.5 py-1.5 fs-6" id="homeCampHours">00</span>
                                        <span class="text-white fw-bold">:</span>
                                        <span class="badge bg-danger text-white px-2.5 py-1.5 fs-6" id="homeCampMins">00</span>
                                        <span class="text-white fw-bold">:</span>
                                        <span class="badge bg-danger text-white px-2.5 py-1.5 fs-6" id="homeCampSecs">00</span>
                                    </div>
                                </div>

                                <div>
                                    <a href="{{ route('ecommerce.campaigns.show', $homeCampaign->slug) }}" class="btn btn-danger btn-lg rounded-pill px-4 fw-bold">
                                        View All Offers <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="row g-3">
                                    @foreach($homeCampaign->products->take(4) as $campProd)
                                        @php
                                            $customP = $campProd->pivot->campaign_price ? (float)$campProd->pivot->campaign_price : null;
                                            $cPrice = $homeCampaign->calculateCampaignPrice((float)$campProd->price, $customP);
                                            $rPrice = (float)$campProd->price;
                                            $dPct = ($rPrice > $cPrice && $rPrice > 0) ? round((($rPrice - $cPrice) / $rPrice) * 100) : 0;
                                            $pImg = $campProd->image ?: (is_array($campProd->gallery) ? ($campProd->gallery[0] ?? null) : null);
                                        @endphp
                                        <div class="col-6 col-md-6">
                                            <div class="card h-100 border-0 rounded-3 p-2 bg-white text-dark shadow-sm">
                                                <div class="position-relative bg-light rounded-2 p-2 text-center" style="aspect-ratio: 1/1;">
                                                    <a href="{{ route('ecommerce.products.show', $campProd->id) }}">
                                                        <img src="{{ $pImg ? asset($pImg) : asset('assets/img/products/default-product.png') }}" 
                                                             alt="{{ $campProd->name }}" class="img-fluid h-100 object-fit-contain">
                                                    </a>
                                                    @if($dPct > 0)
                                                        <span class="badge bg-danger position-absolute top-0 start-0 m-2 fw-bold">-{{ $dPct }}%</span>
                                                    @endif
                                                </div>
                                                <div class="pt-2 px-1">
                                                    <h6 class="text-truncate fw-bold mb-1">
                                                        <a href="{{ route('ecommerce.products.show', $campProd->id) }}" class="text-dark text-decoration-none">
                                                            {{ $campProd->name }}
                                                        </a>
                                                    </h6>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span class="text-danger fw-bold">৳{{ number_format($cPrice, 0) }}</span>
                                                        @if($cPrice < $rPrice)
                                                            <span class="text-muted text-decoration-line-through small">৳{{ number_format($rPrice, 0) }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const hCampEnd = new Date("{{ $homeCampaign->end_date->toIso8601String() }}").getTime();
                function tickHomeCamp() {
                    const now = new Date().getTime();
                    const dist = hCampEnd - now;
                    if (dist < 0) return;
                    const hrs = Math.floor(dist / (1000 * 60 * 60));
                    const mns = Math.floor((dist % (1000 * 60 * 60)) / (1000 * 60));
                    const scs = Math.floor((dist % (1000 * 60)) / 1000);
                    const hE = document.getElementById('homeCampHours');
                    const mE = document.getElementById('homeCampMins');
                    const sE = document.getElementById('homeCampSecs');
                    if (hE) hE.innerText = String(hrs).padStart(2, '0');
                    if (mE) mE.innerText = String(mns).padStart(2, '0');
                    if (sE) sE.innerText = String(scs).padStart(2, '0');
                }
                tickHomeCamp();
                setInterval(tickHomeCamp, 1000);
            });
        </script>
        @endpush
    @endif


    <!-- Video Section -->
    <section class="section-video py-4" style="background: transparent !important; margin: 0 !important;">
        <div class="container">
            <div class="row align-items-center g-4">
                <!-- Left Column: Content & Features -->
                <div class="col-lg-6">
                    <div class="video-content-left pe-lg-3">
                        <span class="badge px-2.5 py-1.5 mb-2 fw-semibold" style="background-color: rgba(37, 99, 235, 0.08); color: #2563eb; font-size: 12px; border-radius: 4px;">
                            <i class="fas fa-video me-1"></i> TELEMEDICINE CONSULTATION
                        </span>
                        <h2 class="fw-bold mb-2" style="color: #1e293b; font-size: 24px; line-height: 1.35;">Consult Specialist Doctors Online Anytime</h2>
                        <p class="mb-3" style="color: #64748b; font-size: 14px; line-height: 1.5;">Access top-tier medical care from the comfort of your home. Connect with verified specialists instantly via video calls for personalized consultations, diagnostics, and digital prescriptions 24/7.</p>
                        
                        <div class="video-features-list mb-3">
                            <div class="d-flex align-items-start mb-2.5">
                                <div class="feature-icon-box p-2 me-2.5 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background-color: rgba(37, 99, 235, 0.08); color: #2563eb; border-radius: 4px;">
                                    <i class="fas fa-clock" style="font-size: 14px;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0.5" style="color: #1e293b; font-size: 14px;">24/7 Service Availability</h6>
                                    <p class="mb-0" style="color: #64748b; font-size: 13px;">Get round-the-clock medical assistance whenever you need it.</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-2.5">
                                <div class="feature-icon-box p-2 me-2.5 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background-color: rgba(37, 99, 235, 0.08); color: #2563eb; border-radius: 4px;">
                                    <i class="fas fa-user-md" style="font-size: 14px;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0.5" style="color: #1e293b; font-size: 14px;">100% Verified Specialist Doctors</h6>
                                    <p class="mb-0" style="color: #64748b; font-size: 13px;">Consult with certified healthcare professionals across various departments.</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="feature-icon-box p-2 me-2.5 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background-color: rgba(37, 99, 235, 0.08); color: #2563eb; border-radius: 4px;">
                                    <i class="fas fa-file-medical" style="font-size: 14px;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0.5" style="color: #1e293b; font-size: 14px;">Instant Digital Prescription</h6>
                                    <p class="mb-0" style="color: #64748b; font-size: 13px;">Receive instant, downloadable e-prescriptions right after your consultation.</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('doctors.search') }}" class="btn text-white px-3 py-2 d-inline-flex align-items-center gap-2" style="background: #2563eb !important; border: 1.5px solid #2563eb !important; border-radius: 4px !important; font-size: 14px; font-weight: 600; box-shadow: 0 4px 10px rgba(37,99,235,0.2) !important;">
                                Consult a Doctor Now <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Video Container -->
                <div class="col-lg-6">
                    <div class="video-wrapper overflow-hidden border" style="border-radius: 0 !important;">
                        <div class="video-container" id="telemedicineVideoContainer" style="border-radius: 0 !important;">
                            <div class="video-cover-wrapper" style="background-image: url('{{ asset('uploads/settings/video_cover.png') }}'); border-radius: 0 !important;" onclick="playTelemedicineVideo()">
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
    <section class="section section-products py-4" style="background-color: transparent;">
        <div class="container">
            <div class="section-header text-center mb-3">
                <h2 class="fw-bold mb-1" style="color: #1e293b; font-size: 24px; line-height: 1.3;">Our Products</h2>
                <p class="sub-title mb-0" style="color: #64748b; font-size: 14px;">Order medicines and health products from our trusted pharmacy store.</p>
            </div>

            <div class="row g-3">
                <!-- Sidebar Filter -->
                <div class="col-lg-3 col-md-4 mb-3">
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
                            <div class="col-lg-3 col-md-4 col-sm-6 col-6 mb-3 product-grid-item">
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
    <section class="section section-doctor py-4" style="background-color: transparent;">
        <div class="container">
            <div class="section-header text-center mb-3">
                <h2 class="fw-bold mb-1" style="color: #1e293b; font-size: 24px; line-height: 1.3;">Book Our Doctors</h2>
                <p class="sub-title mb-0" style="color: #64748b; font-size: 14px;">Meet our expert doctors and book your appointment today</p>
            </div>

            <div class="row g-3">
                <!-- Sidebar Filter -->
                <div class="col-lg-3 col-md-4 mb-3">
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
    @php
        $packagesToDisplay = (isset($healthPackages) && $healthPackages->count() > 0) ? $healthPackages : collect([
            (object)[
                'is_featured' => false,
                'icon' => 'fas fa-heartbeat',
                'badge_label' => 'BASIC CHECKUP',
                'title' => 'Basic Health Checkup',
                'test_count' => 15,
                'features' => ['CBC Test', 'Blood Sugar Test', 'Lipid Profile', 'Doctor Consultation'],
                'price' => 1500,
                'price_label' => 'Per Package',
                'link' => route('ecommerce.products')
            ],
            (object)[
                'is_featured' => true,
                'icon' => 'fas fa-shield-alt',
                'badge_label' => 'RECOMMENDED',
                'title' => 'Comprehensive Health',
                'test_count' => 35,
                'features' => ['Full Body Checkup', 'ECG & X-Ray', 'Kidney & Liver Function', 'Free Follow-up'],
                'price' => 3500,
                'price_label' => 'Per Package',
                'link' => route('ecommerce.products')
            ],
            (object)[
                'is_featured' => false,
                'icon' => 'fas fa-user-md',
                'badge_label' => 'SENIOR CARE',
                'title' => 'Senior Citizen Package',
                'test_count' => 45,
                'features' => ['Complete Blood Profile', 'Cardiac Checkup', 'Bone Density Test', 'Specialist Advice'],
                'price' => 4800,
                'price_label' => 'Per Package',
                'link' => route('ecommerce.products')
            ],
            (object)[
                'is_featured' => false,
                'icon' => 'fas fa-female',
                'badge_label' => 'WOMEN CARE',
                'title' => 'Women Wellness Check',
                'test_count' => 25,
                'features' => ['Thyroid Profile', 'Vitamin D3 & B12', 'Gynaecology Consultation', 'USG Screening'],
                'price' => 2900,
                'price_label' => 'Per Package',
                'link' => route('ecommerce.products')
            ]
        ]);
    @endphp

    <section class="section section-health-packages py-4" style="background-color: transparent;">
        <div class="container">
            <!-- Section Header -->
            <div class="section-header text-center mb-3">
                <h2 class="fw-bold mb-1" style="color: #1e293b; font-size: 24px; line-height: 1.3;">Health Packages</h2>
                <p class="sub-title mb-0" style="color: #64748b; font-size: 14px;">Comprehensive health checkup packages at affordable prices</p>
            </div>

            <!-- Packages Grid -->
            <div class="row g-3 justify-content-center">
                @foreach($packagesToDisplay as $package)
                <div class="col-lg-3 col-md-6 mb-3">
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
    <!-- /Health Packages Section -->

    <!-- Health Courses Section -->
    <section class="section section-courses py-4" style="background-color: transparent;">
        <div class="container">
            <!-- Section Header -->
            <div class="section-header text-center mb-3">
                <h2 class="fw-bold mb-1" style="color: #1e293b; font-size: 24px; line-height: 1.3;">Health Education Courses</h2>
                <p class="sub-title mb-0" style="color: #64748b; font-size: 14px;">Free and paid courses to help you understand and manage your health better</p>
            </div>

            <!-- Courses Grid -->
            <div class="row g-3">
                <!-- Course 1 -->
                <div class="col-lg-4 col-md-6 mb-3">
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
                <div class="col-lg-4 col-md-6 mb-3">
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
                <div class="col-lg-4 col-md-6 mb-3">
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



    <!-- Blog Section -->
    <section class="section section-blogs py-4" style="background-color: transparent;">
        <div class="container">
            <div class="section-header text-center mb-3">
                <h2 class="fw-bold mb-1" style="color: #1e293b; font-size: 24px; line-height: 1.3;">Latest Blogs & News</h2>
                <p class="sub-title mb-0" style="color: #64748b; font-size: 14px;">Stay updated with our latest health tips and news.</p>
            </div>
            <div class="row g-3 justify-content-center">
                <div class="col-md-6 col-lg-3 col-sm-12 mb-3">
                    <div class="blog-grid">
                        <div class="blog-grid-img">
                            <a href="#">
                                <img src="{{ asset('assets/img/img-01.jpg') }}" class="img-fluid" alt="Blog Image">
                            </a>
                        </div>
                        <div class="blog-grid-info">
                            <div class="blog-date">05 SEP 2025</div>
                            <h4 class="blog-title"><a href="#">How to Handle Patient Health?</a></h4>
                            <p class="blog-text">Learn the best practices for managing patient health effectively...</p>
                            <a href="#" class="read-more-btn">Read More <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 col-sm-12 mb-3">
                    <div class="blog-grid">
                        <div class="blog-grid-img">
                            <a href="#">
                                <img src="{{ asset('assets/img/img-02.jpg') }}" class="img-fluid" alt="Blog Image">
                            </a>
                        </div>
                        <div class="blog-grid-info">
                            <div class="blog-date">06 SEP 2025</div>
                            <h4 class="blog-title"><a href="#">The Benefits of Regular Checkups</a></h4>
                            <p class="blog-text">Regular health checkups are vital for early detection and prevention...</p>
                            <a href="#" class="read-more-btn">Read More <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 col-sm-12 mb-3">
                    <div class="blog-grid">
                        <div class="blog-grid-img">
                            <a href="#">
                                <img src="{{ asset('assets/img/img-03.jpg') }}" class="img-fluid" alt="Blog Image">
                            </a>
                        </div>
                        <div class="blog-grid-info">
                            <div class="blog-date">07 SEP 2025</div>
                            <h4 class="blog-title"><a href="#">Healthy Living Tips</a></h4>
                            <p class="blog-text">Simple lifestyle changes can lead to significant health improvements...</p>
                            <a href="#" class="read-more-btn">Read More <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 col-sm-12 mb-3">
                    <div class="blog-grid">
                        <div class="blog-grid-img">
                            <a href="#">
                                <img src="{{ asset('assets/img/img-04.jpg') }}" class="img-fluid" alt="Blog Image">
                            </a>
                        </div>
                        <div class="blog-grid-info">
                            <div class="blog-date">08 SEP 2025</div>
                            <h4 class="blog-title"><a href="#">Understanding Mental Health</a></h4>
                            <p class="blog-text">Mental health is just as important as physical health. Find out why...</p>
                            <a href="#" class="read-more-btn">Read More <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="#" class="btn-view-all-arrow">
                    View All Blogs <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
    <!-- /Blog Section -->



    <!-- Testimonials Section -->
    <section class="section section-testimonials py-4" style="background-color: transparent;">
        <div class="container">
            <div class="section-header text-center mb-3">
                <h2 class="fw-bold mb-1" style="color: #1e293b; font-size: 24px; line-height: 1.3;">What Our Patients Say</h2>
                <p class="sub-title mb-0" style="color: #64748b; font-size: 14px;">Real feedback from our valued patients</p>
            </div>
            <div class="row g-3 justify-content-center">
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="testimonial-card">
                        <div class="testimonial-rating mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="testimonial-text">"Excellent service! The doctor was very professional and the
                            booking process was seamless. Highly recommend abcsheba to everyone."</p>
                        <div class="testimonial-author mt-auto pt-2 border-top">
                            <img src="{{ asset('assets/img/patients/patient1.jpg') }}" class="rounded-circle"
                                alt="Patient">
                            <div>
                                <h6 class="author-name mb-0">Sarah Johnson</h6>
                                <small class="author-role text-muted">Cardiology Patient</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="testimonial-card">
                        <div class="testimonial-rating mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="testimonial-text">"Found the best dentist through abcsheba. The platform is easy to
                            use and the doctor profiles are very detailed. Great experience!"</p>
                        <div class="testimonial-author mt-auto pt-2 border-top">
                            <img src="{{ asset('assets/img/patients/patient2.jpg') }}" class="rounded-circle"
                                alt="Patient">
                            <div>
                                <h6 class="author-name mb-0">Michael Chen</h6>
                                <small class="author-role text-muted">Dental Patient</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="testimonial-card">
                        <div class="testimonial-rating mb-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                        </div>
                        <p class="testimonial-text">"Very convenient way to book appointments. No more waiting in
                            long queues. The reminder system is also very helpful."</p>
                        <div class="testimonial-author mt-auto pt-2 border-top">
                            <img src="{{ asset('assets/img/patients/patient3.jpg') }}" class="rounded-circle"
                                alt="Patient">
                            <div>
                                <h6 class="author-name mb-0">Emily Davis</h6>
                                <small class="author-role text-muted">General Checkup</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /Testimonials Section -->


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

            // Hero Slider Initialization Function
            function initHeroSlider() {
                var $slider = $('.hero-slider');
                if ($slider.length === 0) return;

                if ($slider.hasClass('slick-initialized')) {
                    $slider.slick('unslick');
                }

                $slider.slick({
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

                setTimeout(function () {
                    if ($slider.hasClass('slick-initialized')) {
                        $slider.slick('setPosition');
                    }
                }, 100);
            }

            // Initialize on Document Ready
            initHeroSlider();

            // Re-initialize on Livewire SPA Navigation
            $(document).on('livewire:navigated', function () {
                initHeroSlider();
            });

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

            function renderProductSkeletons(count = 8) {
                var grid = $('#productsGrid');
                grid.empty();
                var skeletonHtml = '';
                for (var i = 0; i < count; i++) {
                    skeletonHtml += `
                        <div class="col-lg-3 col-md-4 col-sm-6 col-6 mb-3 product-skeleton-item">
                            <div class="product-card-skeleton">
                                <div class="skeleton-img-wrap">
                                    <div class="skeleton-badge skeleton-shimmer"></div>
                                </div>
                                <div class="skeleton-details">
                                    <div class="skeleton-rating-row">
                                        <div class="skeleton-rating-stars skeleton-shimmer"></div>
                                        <div class="skeleton-rating-text skeleton-shimmer"></div>
                                    </div>
                                    <div class="skeleton-brand skeleton-shimmer"></div>
                                    <div class="skeleton-title-1 skeleton-shimmer"></div>
                                    <div class="skeleton-title-2 skeleton-shimmer"></div>
                                    <div class="skeleton-footer">
                                        <div class="skeleton-price-block">
                                            <div class="skeleton-price-main skeleton-shimmer"></div>
                                            <div class="skeleton-price-sub skeleton-shimmer"></div>
                                        </div>
                                        <div class="skeleton-btn-group">
                                            <div class="skeleton-btn-cart skeleton-shimmer"></div>
                                            <div class="skeleton-btn-buy skeleton-shimmer"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
                grid.html(skeletonHtml);
            }

            function filterProducts() {
                var category = $('input[name="product_category"]:checked').val();
                var search = $('#productSearchInput').val();

                renderProductSkeletons(8);

                $.ajax({
                    url: '/api/products/filter',
                    type: 'GET',
                    data: { category: category, search: search },
                    success: function (products) {
                        renderProducts(products);
                    },
                    error: function () {
                        $('#productsGrid').html('<div class="col-12"><div class="alert alert-danger text-center">Failed to load products. Please try again.</div></div>');
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
                        <div class="col-lg-3 col-md-4 col-sm-6 col-6 mb-3 product-grid-item">
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

            function renderDoctorSkeletons(count = 6) {
                var grid = $('#doctorsGrid');
                grid.empty();
                var skeletonHtml = '';
                for (var i = 0; i < count; i++) {
                    skeletonHtml += `
                        <div class="col-lg-4 col-md-6 col-sm-6 mb-4 doctor-skeleton-item">
                            <div class="doctor-card-skeleton">
                                <div class="skeleton-doc-img-wrap">
                                    <div class="skeleton-doc-fee skeleton-shimmer"></div>
                                    <div class="skeleton-doc-fav skeleton-shimmer"></div>
                                </div>
                                <div class="skeleton-doc-info">
                                    <div class="skeleton-doc-speciality skeleton-shimmer"></div>
                                    <div class="skeleton-doc-name skeleton-shimmer"></div>
                                    <div class="skeleton-doc-rating skeleton-shimmer"></div>
                                    <div class="skeleton-doc-location skeleton-shimmer"></div>
                                    <div class="skeleton-doc-buttons">
                                        <div class="skeleton-doc-btn skeleton-shimmer"></div>
                                        <div class="skeleton-doc-btn skeleton-shimmer"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
                grid.html(skeletonHtml);
            }

            function filterDoctors() {
                var speciality = $('#doctorSpecialitySelect').val();
                var search = $('#doctorSearchInput').val();

                renderDoctorSkeletons(6);

                $.ajax({
                    url: '{{ route('api.doctors.filter') }}',
                    type: 'GET',
                    data: { speciality: speciality, search: search },
                    success: function (doctors) {
                        renderDoctors(doctors);
                    },
                    error: function () {
                        $('#doctorsGrid').html('<div class="col-12"><div class="alert alert-danger text-center">Failed to load doctors. Please try again.</div></div>');
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
                                        <span>${doctor.clinic_name || doctor.area || 'Dhaka'}</span>
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
    @include('ecommerce::components.skeletons.styles')
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

        /* ===================================================
           PREMIUM HERO SEARCH BOX DESIGN SYSTEM
           =================================================== */
        .hero-search-section {
            margin-top: 10px;
            margin-bottom: 25px;
        }

        .search-card-wrapper {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0 !important;
            padding: 8px 14px !important;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04) !important;
            transition: all 0.25s ease;
        }

        .search-card-wrapper:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.08) !important;
        }

        .search-box-title {
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            letter-spacing: -0.2px;
            margin-bottom: 6px !important;
        }

        .icon-circle {
            width: 22px;
            height: 22px;
            background: #eff6ff !important;
            color: #2563eb !important;
            border-radius: 0 !important;
            font-size: 11px;
        }

        .hero-search-bar {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0 !important;
            padding: 3px !important;
            box-shadow: none !important;
            transition: all 0.2s ease;
            min-height: unset !important;
        }

        .hero-search-bar:focus-within {
            background: #ffffff !important;
            border-color: #2563eb !important;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15) !important;
        }

        .hero-search-form {
            display: flex !important;
            align-items: center !important;
            min-height: unset !important;
            height: 32px !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .search-field {
            display: flex !important;
            align-items: center !important;
            min-height: unset !important;
            height: 32px !important;
            padding: 0 8px !important;
            margin: 0 !important;
            border: none !important;
            background: transparent !important;
        }

        .search-lead-icon {
            color: #64748b !important;
            font-size: 13px !important;
            flex-shrink: 0 !important;
            line-height: 1 !important;
            margin-right: 6px !important;
        }

        .hero-search-form input.form-control {
            min-height: unset !important;
            height: 32px !important;
            line-height: 32px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            padding: 0 !important;
            margin: 0 !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .hero-search-form input.form-control::placeholder {
            color: #94a3b8 !important;
            font-weight: 400 !important;
            font-size: 13px !important;
            line-height: 32px !important;
        }

        .search-divider-line {
            width: 1px !important;
            height: 18px !important;
            background: #e2e8f0 !important;
            flex-shrink: 0 !important;
            margin: 0 4px !important;
        }

        .custom-dropdown {
            position: relative !important;
            width: 100% !important;
            height: 32px !important;
            display: flex !important;
            align-items: center !important;
        }

        .custom-dropdown .dropdown-search {
            min-height: unset !important;
            height: 32px !important;
            line-height: 32px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            padding: 0 16px 0 0 !important;
            margin: 0 !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            cursor: pointer !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            width: 100% !important;
            border-radius: 0 !important;
        }

        .custom-dropdown .dropdown-search::placeholder {
            color: #94a3b8 !important;
            font-weight: 400 !important;
            font-size: 13px !important;
            line-height: 32px !important;
        }

        .custom-dropdown .dropdown-arrow {
            position: absolute !important;
            right: 0 !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            color: #94a3b8 !important;
            font-size: 10px !important;
            pointer-events: none !important;
            transition: transform 0.2s ease !important;
        }

        .custom-dropdown.open .dropdown-arrow {
            transform: translateY(-50%) rotate(180deg) !important;
            color: #2563eb !important;
        }

        .custom-dropdown .dropdown-menu {
            position: absolute !important;
            top: calc(100% + 4px) !important;
            left: 0 !important;
            width: 100% !important;
            min-width: 220px !important;
            max-height: 260px !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0 !important;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12) !important;
            z-index: 1050 !important;
            padding: 6px !important;
            margin: 0 !important;
            display: none;
            flex-direction: column !important;
        }

        .custom-dropdown.open .dropdown-menu {
            display: flex !important;
        }

        .custom-dropdown .dropdown-filter {
            width: 100% !important;
            min-height: unset !important;
            height: 28px !important;
            line-height: 28px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0 !important;
            padding: 2px 8px !important;
            font-size: 12px !important;
            margin-bottom: 4px !important;
            outline: none !important;
            box-sizing: border-box !important;
        }

        .custom-dropdown .dropdown-filter:focus {
            border-color: #2563eb !important;
        }

        .custom-dropdown .dropdown-list {
            flex: 1 1 auto !important;
            max-height: 180px !important;
            overflow-y: auto !important;
        }

        .custom-dropdown .dropdown-item {
            padding: 5px 8px !important;
            font-size: 12.5px !important;
            color: #334155 !important;
            border-radius: 0 !important;
            cursor: pointer !important;
            line-height: 1.3 !important;
            transition: all 0.15s ease !important;
        }

        .custom-dropdown .dropdown-item:hover,
        .custom-dropdown .dropdown-item.selected {
            background: #eff6ff !important;
            color: #2563eb !important;
            font-weight: 600 !important;
        }

        .btn-search-action {
            background: #2563eb !important;
            color: #ffffff !important;
            border: 1px solid #2563eb !important;
            border-radius: 0 !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            padding: 0 16px !important;
            height: 32px !important;
            min-height: 32px !important;
            line-height: 32px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.15) !important;
            flex-shrink: 0 !important;
        }

        .btn-search-action:hover {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25) !important;
            color: #ffffff !important;
        }

        @media (max-width: 767px) {
            .hero-search-form {
                flex-direction: column !important;
                gap: 8px !important;
                align-items: stretch !important;
            }
            .search-divider-line {
                display: none !important;
            }
            .search-field {
                border-bottom: 1px solid #e2e8f0;
                padding-bottom: 4px;
            }
            .btn-search-action {
                width: 100% !important;
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
            margin: 0 auto 20px auto;
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
            background: transparent !important;
            padding: 40px 0;
            margin: 0;
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
            max-width: 100%;
            margin: 0 auto;
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            /* 16:9 aspect ratio */
            height: 0;
            overflow: hidden;
            border-radius: 0;
            box-shadow: none;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 0;
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
            border-radius: 0;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            padding: 15px;
            position: sticky;
            top: 100px;
            border: 1px solid #f0f0f0;
        }

        .filter-section {
            margin-bottom: 15px;
        }

        .filter-section:last-child {
            margin-bottom: 0;
        }

        .filter-title {
            font-size: 14px;
            font-weight: 600;
            color: #272b41;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-title i {
            color: #1D4ED8;
        }

        .search-input-wrapper input {
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 13px;
            height: 36px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
        }

        .search-input-wrapper input:focus {
            border-color: #1D4ED8;
            box-shadow: 0 0 0 2px rgba(0, 102, 255, 0.1);
        }

        /* Category List */
        .category-list {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .speciality-list .category-item {
            padding: 5px 6px;
            gap: 6px;
        }

        .speciality-list .category-item input[type="radio"] {
            width: 14px;
            height: 14px;
            flex: 0 0 14px;
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
            gap: 6px;
            padding: 5px 6px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            margin: 0;
        }

        .category-item:hover {
            background: #f5f8ff;
        }

        .category-item input[type="radio"] {
            width: 14px;
            height: 14px;
            accent-color: #1D4ED8;
        }

        .category-item .category-name {
            font-size: 13px;
            color: #475569;
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
            border-radius: 4px;
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
            padding-left: 10px;
            border-left: 2px solid #e2e8f0;
            margin-left: 8px;
            margin-top: 2px;
            margin-bottom: 4px;
        }

        .home-subsub-list {
            padding-left: 8px;
            border-left: 2px dashed #cbd5e1;
            margin-left: 6px;
        }

        .home-cat-sub {
            font-size: 12.5px !important;
            padding: 4px 6px !important;
            color: #475569;
        }

        .home-cat-sub:hover {
            background: #f0f9ff;
            color: #2563eb;
        }

        .home-cat-subsub {
            font-size: 12px !important;
            padding: 3px 6px !important;
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
            border-radius: 0;
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
            top: 8px;
            left: 8px;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
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
            height: 135px;
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
            padding: 8px;
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
            padding: 10px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Rating */
        .product-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 4px;
            font-size: 11px;
        }

        .product-rating i {
            color: #ffc107;
            font-size: 11px;
        }

        .product-rating .rating-value {
            font-weight: 600;
            color: #333;
        }

        .product-rating .review-count {
            color: #999;
            font-size: 11px;
        }

        /* Brand */
        .product-brand {
            font-size: 10px;
            color: #1D4ED8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        /* Product Name */
        .product-name {
            font-size: 13px;
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 34px;
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
            font-size: 15px;
            font-weight: 700;
            color: #272b41;
        }

        .price-original {
            font-size: 11px;
            color: #999;
            text-decoration: line-through;
        }

        /* Button Group */
        .product-actions-form {
            display: flex;
        }

        .btn-group-modern {
            display: flex;
            gap: 4px;
        }

        .btn-cart-modern {
            width: 32px;
            height: 32px;
            border-radius: 4px;
            border: 1.5px solid #1D4ED8;
            background: transparent;
            color: #1D4ED8;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
        }

        .btn-cart-modern:hover {
            background: #1D4ED8;
            color: #fff;
        }

        .btn-buy-modern {
            padding: 0 12px;
            height: 32px;
            border-radius: 4px;
            border: none;
            background: linear-gradient(135deg, #1D4ED8 0%, #60A5FA 100%);
            color: #fff;
            font-weight: 600;
            font-size: 12px;
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
            gap: 8px;
            padding: 6px 16px;
            background: #2563eb !important;
            border: 1.5px solid #2563eb !important;
            color: #fff !important;
            font-size: 13px;
            font-weight: 600;
            border-radius: 4px !important;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2) !important;
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
            border-radius: 0;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid #f0f0f0;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(29, 78, 216, 0.12);
            border-color: #1D4ED8;
        }

        .course-thumbnail {
            position: relative;
            height: 135px;
            overflow: hidden;
        }

        .course-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .course-card:hover .course-thumbnail img {
            transform: scale(1.05);
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
            font-size: 40px;
            color: #fff;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
        }

        .course-card:hover .play-overlay i {
            transform: scale(1.1);
        }

        .course-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
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
            padding: 10px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .course-meta {
            display: flex;
            gap: 12px;
            margin-bottom: 6px;
        }

        .course-meta span {
            font-size: 11px;
            color: #6b7280;
        }

        .course-meta i {
            color: #1D4ED8;
            margin-right: 4px;
        }

        .course-title {
            font-size: 13px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .course-desc {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.4;
            margin-bottom: 10px;
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
            padding-top: 10px;
            border-top: 1px solid #f3f4f6;
        }

        .course-instructor {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .course-instructor img {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
        }

        .course-instructor span {
            font-size: 11px;
            font-weight: 600;
            color: #4b5563;
        }

        .btn-enroll {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 12px;
            background: linear-gradient(135deg, #1D4ED8 0%, #3B82F6 100%);
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            border-radius: 4px;
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
        /* Health Package Card */
        .health-package-card {
            background: #fff;
            border-radius: 0;
            padding: 15px 12px;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            border: 1px solid #f0f0f0;
        }

        .health-package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(29, 78, 216, 0.12);
            border-color: #1D4ED8;
        }

        .health-package-card.featured {
            border: 1.5px solid #1D4ED8;
            transform: none;
        }

        .health-package-card.featured:hover {
            transform: translateY(-5px);
        }

        .featured-ribbon {
            position: absolute;
            top: 10px;
            right: -30px;
            background: linear-gradient(135deg, #1D4ED8 0%, #3B82F6 100%);
            color: #fff;
            padding: 3px 30px;
            font-size: 9px;
            font-weight: 600;
            transform: rotate(45deg);
            box-shadow: 0 2px 8px rgba(29, 78, 216, 0.3);
        }

        .package-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }

        .package-icon i {
            font-size: 20px;
            color: #1D4ED8;
        }

        .package-badge {
            display: inline-block;
            background: #EEF2FF;
            color: #1D4ED8;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            margin-left: auto;
            margin-right: auto;
            width: fit-content;
        }

        .package-title {
            font-size: 14px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .package-tests {
            color: #6b7280;
            font-size: 11px;
            margin-bottom: 10px;
        }

        .package-tests i {
            color: #1D4ED8;
            margin-right: 4px;
        }

        .package-features {
            list-style: none;
            padding: 0;
            margin: 0 0 10px 0;
            text-align: left;
        }

        .package-features li {
            padding: 4px 0;
            font-size: 11px;
            color: #4b5563;
            border-bottom: 1px solid #f3f4f6;
        }

        .package-features li:last-child {
            border-bottom: none;
        }

        .package-features li i {
            color: #10b981;
            margin-right: 6px;
            font-size: 11px;
        }

        .package-price {
            margin: auto 0 10px 0;
            padding-top: 8px;
        }

        .package-price .price {
            font-size: 20px;
            font-weight: 700;
            color: #1D4ED8;
        }

        .package-price .period {
            display: block;
            font-size: 11px;
            color: #9ca3af;
            margin-top: 1px;
        }

        .btn-package {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 12px;
            background: linear-gradient(135deg, #1D4ED8 0%, #3B82F6 100%);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.3s ease;
            width: 100%;
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
            border-radius: 0;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            padding: 15px;
            position: sticky;
            top: 100px;
            border: 1px solid #f0f0f0;
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
            border-radius: 0;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            transition: all 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid #f0f0f0;
        }

        .doctor-card-new:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 102, 255, 0.12);
            border-color: #1D4ED8;
        }

        .doctor-img-wrapper {
            position: relative;
            height: 135px;
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
            top: 8px;
            left: 8px;
            background: linear-gradient(135deg, #1D4ED8, #60A5FA);
            color: #fff;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 11px;
            box-shadow: 0 2px 8px rgba(0, 102, 255, 0.2);
        }

        .doctor-info {
            padding: 10px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .doctor-speciality {
            font-size: 10px;
            color: #1D4ED8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            font-weight: 600;
            display: inline-block;
            background: #e8f4ff;
            padding: 3px 8px;
            border-radius: 4px;
            width: fit-content;
        }

        .doctor-name {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 4px;
            line-height: 1.3;
            min-height: 20px;
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
            font-size: 12px;
            margin-left: 3px;
        }

        .doctor-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 4px;
            font-size: 11px;
        }

        .doctor-rating i {
            color: #ffc107;
            font-size: 11px;
        }

        .doctor-rating .rating-count {
            color: #888;
            font-size: 11px;
        }

        .doctor-location {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
            font-size: 11px;
            color: #666;
        }

        .doctor-location i {
            color: #1D4ED8;
        }

        /* Doctor Buttons Container */
        .doctor-buttons {
            display: flex;
            gap: 6px;
            margin-top: auto;
        }

        .btn-view-details {
            flex: 1;
            padding: 6px 4px;
            background: transparent;
            border: 1.5px solid #1D4ED8;
            border-radius: 4px;
            color: #1D4ED8;
            font-weight: 600;
            font-size: 11px;
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
            margin-right: 2px;
        }

        .btn-book-appointment {
            flex: 1;
            padding: 6px 4px;
            background: linear-gradient(135deg, #1D4ED8, #60A5FA);
            border: none;
            border-radius: 4px;
            color: #fff;
            font-weight: 600;
            font-size: 11px;
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
            padding: 10px 0 40px 0 !important;
            min-height: auto;
        }

        .section-hero-doctime .container {
            position: relative;
        }

        .hero-slider {
            margin-bottom: 0;
        }

        .hero-main-wrapper {
            min-height: 330px;
            max-height: none;
            overflow: hidden;
            border-radius: 0;
        }

        .hero-search-section {
            position: relative !important;
            left: auto !important;
            right: auto !important;
            bottom: auto !important;
            transform: none !important;
            margin-top: 15px !important;
            margin-bottom: 0 !important;
            z-index: 100 !important;
        }

        .hero-doctors-img {
            max-height: 330px;
            width: auto;
            object-fit: contain;
        }

        /* Blog Grid Modern Styles (Matching Course Section) */
        .blog-grid {
            background: #fff;
            border-radius: 0;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid #f0f0f0;
        }

        .blog-grid:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(29, 78, 216, 0.12);
            border-color: #1D4ED8;
        }

        .blog-grid-img {
            position: relative;
            height: 135px;
            overflow: hidden;
        }

        .blog-grid-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .blog-grid:hover .blog-grid-img img {
            transform: scale(1.05);
        }

        .blog-grid-info {
            padding: 10px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .blog-date {
            font-size: 10px;
            color: #1D4ED8;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .blog-title {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .blog-title a {
            color: #1f2937;
            text-decoration: none;
            transition: color 0.2s;
        }

        .blog-title a:hover {
            color: #1D4ED8;
        }

        .blog-text {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.4;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .read-more-btn {
            margin-top: auto;
            font-size: 11px;
            font-weight: 600;
            color: #1D4ED8;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }

        .read-more-btn:hover {
            color: #1e40af;
            gap: 6px;
        }

        /* Testimonial Card Modern Styles */
        .testimonial-card {
            background: #fff;
            border-radius: 0;
            padding: 15px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid #f0f0f0;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(29, 78, 216, 0.12);
            border-color: #1D4ED8;
        }

        .testimonial-rating i {
            font-size: 11px;
        }

        .testimonial-text {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 12px;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .testimonial-author img {
            width: 32px;
            height: 32px;
            object-fit: cover;
        }

        .author-name {
            font-size: 12px;
            font-weight: 700;
            color: #1f2937;
        }

        .author-role {
            font-size: 10px;
            color: #6b7280;
        }

        /* Responsive image-only banner style */
        .hero-full-image-wrapper {
            width: 100%;
            height: 330px;
            border-radius: 0;
            overflow: hidden;
            position: relative;
        }

        .hero-full-image-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            border-radius: 0;
            display: block;
        }

        /* Mobile Dropdown Popup Style */
        @media (max-width: 991px) {
            .section-hero-doctime {
                padding: 8px 0 10px 0 !important;
            }

            .hero-main-wrapper {
                flex-direction: row !important;
                text-align: left;
                min-height: 240px;
                gap: 20px;
                align-items: center;
                justify-content: space-between;
                padding: 0 15px;
                border-radius: 0;
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
                max-height: 240px;
                width: 100%;
                object-fit: contain;
            }

            .hero-main-title {
                font-size: 28px !important;
                margin-bottom: 12px !important;
            }

            .hero-full-image-wrapper {
                height: auto;
                border-radius: 0;
            }

            .hero-full-image-img {
                height: auto;
                max-height: 320px;
                object-fit: cover;
                border-radius: 0;
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
                max-height: 260px;
                border-radius: 0;
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
                max-height: 210px;
                border-radius: 0;
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
                max-height: 180px;
                border-radius: 0;
            }
        }

            .custom-dropdown {
                position: relative !important;
            }

            .dropdown-list {
                flex: 1 1 auto;
                overflow-y: auto;
                max-height: 200px !important;
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
