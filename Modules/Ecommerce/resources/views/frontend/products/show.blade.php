@extends('layouts.app')

@section('title', ($product->meta_title ?: $product->name) . ' - abcsheba.com')

@section('content')
@php
    $fallbackImage = asset('assets/img/products/default-product.png');
    $activeVariants = $product->activeVariantItems();
    $hasVariants = $product->hasActiveVariants();
    $selectedVariant = $activeVariants->firstWhere('id', (int) old('variant_id'))
        ?: ($activeVariants->first(fn ($variant) => $variant->stock > 0) ?? $activeVariants->first());
    $stockQty = $selectedVariant ? (int) $selectedVariant->stock : $product->availableStock();
    $regularPrice = $selectedVariant ? $selectedVariant->regularPrice() : $product->effectiveRegularPrice();
    $displayPrice = $selectedVariant ? $selectedVariant->currentPrice() : $product->effectivePrice();
    $discountPercentage = $regularPrice > 0 && $displayPrice < $regularPrice
        ? round((($regularPrice - $displayPrice) / $regularPrice) * 100)
        : 0;
    $discountAmount = max(0, $regularPrice - $displayPrice);
    $galleryImages = collect($product->gallery ?? [])
        ->prepend($product->image)
        ->filter()
        ->map(fn ($image) => \Illuminate\Support\Str::startsWith($image, ['http://', 'https://']) ? $image : asset($image))
        ->unique()
        ->values();
    $mainImage = $galleryImages->first() ?: $fallbackImage;
    $brandName = $product->brand ?: ($product->category->name ?? 'ABCSheba');
    $sku = $selectedVariant?->sku ?: ($product->sku ?: strtoupper($product->slug ?: ('PRO-' . $product->id)));
    $productReviews = $product->approvedProductReviews->sortByDesc('created_at')->values();
    $reviewCount = (int) ($product->reviews_count ?? $productReviews->count());
    $averageRating = $reviewCount > 0 ? (float) ($product->rating ?? 0) : 0;
    $currentUserReview = auth()->check() && auth()->user()->patient
        ? $productReviews->firstWhere('patient_id', auth()->user()->patient->id)
        : null;
    $selectedReviewRating = (int) old('rating', $currentUserReview->rating ?? 5);
    $tagList = collect(preg_split('/\s*,\s*/', (string) $product->tags))
        ->map(fn ($tag) => trim($tag))
        ->filter()
        ->values();
    $summaryCopy = $product->meta_description
        ?: (\Illuminate\Support\Str::limit(strip_tags((string) $product->description), 140, '...') ?: 'Reliable healthcare product with clear pricing, stock status, and simple ordering support.');
    $detailDescription = trim((string) $product->description) !== ''
        ? nl2br(e($product->description))
        : 'This product page shows the full item overview, pricing, availability, and checkout actions in the same visual system as the product listing.';
    $summaryPoints = collect([
        $product->brand ? 'Brand: ' . $brandName : null,
        $product->category ? 'Category: ' . $product->category->name : null,
        $hasVariants ? $activeVariants->count() . ' variant option(s) available' : null,
        $stockQty > 0 ? 'Available stock: ' . $stockQty . ' units' : 'This item is currently out of stock',
        $displayPrice < $regularPrice ? 'Current sale price is active' : 'Standard listed price',
    ])->filter()->values();

    // Landing settings fallbacks
    $landingSettings = $product->landing_settings ?? [];
    $showCountdown = ($landingSettings['show_countdown'] ?? '1') == '1';
    $countdownTitle = $landingSettings['countdown_title'] ?? 'আজকের বিশেষ ছাড় অফার!';
    $countdownSubtitle = $landingSettings['countdown_subtitle'] ?? 'অফারটি শেষ হতে আর মাত্র সময় বাকি আছে:';
    $countdownHours = (int) ($landingSettings['countdown_hours'] ?? 3);

    $trustTitle = $landingSettings['trust_title'] ?? 'আমাদের থেকে কেন সংগ্রহ করবেন?';

    // 4 Badges (Dynamic with Legacy Fallback)
    $badges = [];
    if (isset($landingSettings['trust_badges']) && is_array($landingSettings['trust_badges'])) {
        $badges = $landingSettings['trust_badges'];
    } else {
        $defaultIcons = [1 => 'fas fa-undo-alt', 2 => 'fas fa-hand-holding-usd', 3 => 'fas fa-headset', 4 => 'fas fa-shipping-fast'];
        $defaultTitles = [1 => '৭ দিনের রিটার্ন', 2 => 'হাতে পেয়ে পেমেন্ট', 3 => 'অনলাইন সাপোর্ট', 4 => 'সারাদেশে ডেলিভারি'];
        $defaultDescs = [1 => 'সহজ এক্সচেঞ্জ সুবিধা', 2 => 'ক্যাশ অন ডেলিভারি', 3 => '২৪/৭ কাস্টমার কেয়ার', 4 => 'দ্রুত ও নিরাপদ ডেলিভারি'];
        for ($i = 1; $i <= 4; $i++) {
            $badges[] = [
                'icon' => $landingSettings["badge_{$i}_icon"] ?? $defaultIcons[$i],
                'title' => $landingSettings["badge_{$i}_title"] ?? $defaultTitles[$i],
                'desc' => $landingSettings["badge_{$i}_desc"] ?? $defaultDescs[$i],
            ];
        }
    }

    // 4 Trust Features (Dynamic with Legacy Fallback)
    $trustFeatures = [];
    if (isset($landingSettings['trust_features']) && is_array($landingSettings['trust_features'])) {
        $trustFeatures = $landingSettings['trust_features'];
    } else {
        $defaultFeatureTitles = [
            1 => '১০০% আসল প্রোডাক্ট (100% Original)',
            2 => 'নিরাপদ প্যাকেজিং ও ডেলিভারি (Secure Shipping)',
            3 => 'সহজ রিটার্ন পলিসি (Easy Returns)',
            4 => '২৪/৭ কাস্টমার সাপোর্ট (Dedicated Hotline)'
        ];
        $defaultFeatureDescs = [
            1 => 'আমরা কোনো নকল পণ্য বিক্রি করি না। সরাসরি ভেরিফাইড ব্র্যান্ড ও ইমপোর্টার থেকে পণ্য সংগ্রহ করি।',
            2 => 'আপনার পণ্যটি যাতে অক্ষত অবস্থায় পৌঁছায়, সেজন্য আমাদের রয়েছে নিখুঁত বাবল-র‍্যাপড প্যাকেজিং ব্যবস্থা।',
            3 => 'পণ্য গ্রহণের পর কোনো ত্রুটি পেলে ৭ দিনের মধ্যে আমাদের সাথে যোগাযোগ করে রিফান্ড বা এক্সচেঞ্জ করতে পারবেন।',
            4 => 'অর্ডার করার আগে বা পরে যেকোনো গাইডলাইনের জন্য আমাদের কাস্টমার কেয়ার হেল্পলাইন সর্বদা উন্মুক্ত।'
        ];
        for ($i = 1; $i <= 4; $i++) {
            $trustFeatures[] = [
                'title' => $landingSettings["feature_{$i}_title"] ?? $defaultFeatureTitles[$i],
                'desc' => $landingSettings["feature_{$i}_desc"] ?? $defaultFeatureDescs[$i],
            ];
        }
    }
@endphp

<div class="content product-single-page" style="background-color: {{ $landingSettings['page_bg_color'] ?? '#ffffff' }} !important; color: {{ $landingSettings['page_text_color'] ?? '#1e293b' }} !important;">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <nav class="product-breadcrumb">
            <a href="{{ route('home') }}" wire:navigate>Home</a>
            <span><i class="fas fa-angle-right"></i></span>
            <a href="{{ route('ecommerce.products') }}" wire:navigate>Products</a>
            <span><i class="fas fa-angle-right"></i></span>
            <a href="{{ route('ecommerce.products', ['category' => $product->product_category_id]) }}" wire:navigate>
                {{ $product->category->name ?? 'General' }}
            </a>
            <span><i class="fas fa-angle-right"></i></span>
            <span>{{ $product->name }}</span>
        </nav>

        <section class="product-hero-card">
            <div class="row g-4 align-items-start">
                <!-- Left: Product Image & Gallery with Floating Actions -->
                <div class="col-lg-6">
                    <div class="ref-gallery-wrapper">
                        <!-- Main Image Container -->
                        <div class="ref-main-image-box">
                            <!-- Floating Share & Wishlist Icons -->
                            <div class="ref-floating-actions">
                                <button type="button" class="ref-action-btn" id="refShareBtn" title="Share Product" onclick="navigator.clipboard && navigator.clipboard.writeText(window.location.href).then(() => toastr.success('Product link copied!'))">
                                    <i class="fas fa-arrow-up-from-bracket"></i>
                                </button>
                                <button type="button" class="ref-action-btn fav-btn" data-id="{{ $product->id }}" title="Save to Wishlist">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>

                            <!-- Image Slider Arrow (Next) on image -->
                            @if($galleryImages->count() > 1)
                            <button type="button" class="ref-nav-arrow ref-arrow-next" id="refNextImageBtn" aria-label="Next Image">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <button type="button" class="ref-nav-arrow ref-arrow-prev" id="refPrevImageBtn" aria-label="Previous Image">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            @endif

                            <div id="productStockBadge" class="stock-badge {{ $stockQty > 0 ? 'in-stock' : 'out-of-stock' }}">
                                <span id="productStockBadgeText">{{ $stockQty > 0 ? 'IN STOCK' : 'OUT OF STOCK' }}</span>
                            </div>

                            <div id="productOfferBadge" class="detail-offer-badge" style="{{ $discountPercentage > 0 ? '' : 'display:none;' }}">
                                {{ $discountPercentage }}% OFF
                            </div>

                            <div class="ref-image-inner product-zoom-frame">
                                <img id="activeProductImage"
                                    src="{{ $mainImage }}"
                                    class="product-main-img"
                                    alt="{{ $product->name }}"
                                    onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                            </div>
                        </div>

                        <!-- Thumbnails Row -->
                        @if($galleryImages->isNotEmpty())
                            <div class="ref-thumb-row">
                                @foreach($galleryImages as $index => $image)
                                    <button type="button"
                                        class="ref-thumb-item product-thumb {{ $loop->first ? 'is-active' : '' }}"
                                        data-image="{{ $image }}"
                                        data-index="{{ $index }}"
                                        aria-label="Preview image {{ $loop->iteration }}">
                                        <img src="{{ $image }}"
                                            alt="{{ $product->name }} thumbnail {{ $loop->iteration }}"
                                            onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right: Product Summary & Purchase Form -->
                <div class="col-lg-6">
                    <aside class="ref-summary-card">
                        <!-- Category & Seller Badge Row -->
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            @if(!empty($product->category?->name))
                                <div class="badge bg-light text-primary border px-2.5 py-1 fw-semibold" style="font-size: 12px; border-radius: 6px;">
                                    <i class="fas fa-folder me-1"></i> {{ $product->category->name }}
                                </div>
                            @endif
                            <div class="seller-store-badge px-2.5 py-1 rounded bg-light border text-muted small ms-auto">
                                <i class="fas fa-store text-primary me-1"></i> Sold by: <strong class="text-dark">{{ $product->seller->name ?? 'ABCSheba Official' }}</strong>
                            </div>
                        </div>

                        <!-- Product Title -->
                        <h1 class="ref-product-title">{{ $product->name }}</h1>

                        <!-- Price, Sold & Rating Row -->
                        <div class="ref-price-rating-row">
                            <div class="ref-price-group">
                                <span id="productOriginalPrice" class="ref-price-original" style="{{ $displayPrice < $regularPrice ? '' : 'display:none;' }}">
                                    ৳{{ number_format($regularPrice, 2) }}
                                </span>
                                <span id="productCurrentPrice" class="ref-price-current">
                                    ৳{{ number_format($displayPrice, 2) }}
                                </span>
                            </div>

                            <div class="ref-meta-group">
                                @php
                                    $soldCount = (int) ($product->orderItems()->whereHas('order', function($q) {
                                        $q->whereIn('status', ['completed', 'delivered', 'processing', 'pending']);
                                    })->sum('quantity') ?? 0);
                                @endphp
                                <span class="ref-sold-count">{{ number_format($soldCount) }} Sold</span>
                                <span class="ref-meta-dot">•</span>
                                <div class="ref-rating-badge">
                                    <i class="fas fa-star text-warning"></i>
                                    <span class="ref-rating-val">{{ number_format($averageRating > 0 ? $averageRating : 4.5, 1) }}</span>
                                </div>
                            </div>
                        </div>

                        <hr class="ref-divider">

                        <!-- Purchase Form -->
                        <form action="{{ route('ecommerce.cart.add') }}" method="POST" class="ref-purchase-form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <!-- Variant Chips (Color / Size / Potency) -->
                            @if($hasVariants)
                                <div class="ref-variant-section">
                                    <div class="ref-variant-header">
                                        <label class="ref-variant-label">
                                            Variant: <strong id="selectedVariantLabel">{{ $selectedVariant ? $selectedVariant->display_label : 'Select Option' }}</strong>
                                        </label>
                                        @if($product->is_medical)
                                            <span class="ref-guide-link"><i class="fas fa-prescription me-1"></i>Rx Product</span>
                                        @else
                                            <span class="ref-guide-link">In Stock</span>
                                        @endif
                                    </div>

                                    <!-- Hidden Select for form submit & JS synchronization -->
                                    <select id="productVariant" name="variant_id" class="d-none" required>
                                        @foreach($activeVariants as $variant)
                                            <option value="{{ $variant->id }}"
                                                data-price="{{ $variant->currentPrice() }}"
                                                data-regular-price="{{ $variant->regularPrice() }}"
                                                data-stock="{{ $variant->stock }}"
                                                data-sku="{{ $variant->sku ?: ($product->sku ?: strtoupper($product->slug ?: ('PRO-' . $product->id))) }}"
                                                data-label="{{ $variant->display_label }}"
                                                {{ $selectedVariant && $selectedVariant->id === $variant->id ? 'selected' : '' }}>
                                                {{ $variant->display_label }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <!-- Clickable Visual Chips -->
                                    <div class="ref-variant-chips">
                                        @foreach($activeVariants as $variant)
                                            <button type="button" 
                                                class="ref-chip-btn {{ $selectedVariant && $selectedVariant->id === $variant->id ? 'is-active' : '' }}"
                                                data-variant-id="{{ $variant->id }}"
                                                data-label="{{ $variant->display_label }}">
                                                {{ $variant->display_label }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Quantity Counter -->
                            <div class="ref-quantity-section">
                                <label class="ref-qty-label">Quantity</label>
                                <div class="ref-qty-control">
                                    <button type="button" class="ref-qty-btn qty-btn" data-action="decrease" {{ $stockQty < 1 ? 'disabled' : '' }}>
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input id="productQuantity"
                                        type="number"
                                        name="quantity"
                                        class="ref-qty-input qty-input"
                                        value="1"
                                        min="1"
                                        max="{{ max(1, $stockQty) }}"
                                        readonly
                                        {{ $stockQty < 1 ? 'disabled' : '' }}>
                                    <button type="button" class="ref-qty-btn qty-btn" data-action="increase" {{ $stockQty < 1 ? 'disabled' : '' }}>
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Buttons: Add to Cart & Buy Now (Styled like Header Login & Sign Up) -->
                            <div class="ref-action-buttons">
                                <button type="submit" class="ref-btn-cart" title="Add to Cart" {{ $stockQty < 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    <span>Add To Cart</span>
                                </button>
                                <button type="submit" name="buy_now" value="1" class="ref-btn-buy-now" {{ $stockQty < 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-bolt me-2"></i>
                                    <span>Buy Now</span>
                                </button>
                            </div>

                            <!-- Delivery T&C Link -->
                            <div class="ref-footer-note mt-3">
                                <a href="#delivery-info" class="ref-delivery-link">Delivery T&C</a>
                            </div>
                        </form>
                    </aside>
                </div>
            </div>
        </section>

        <!-- Product Full Description Block Section -->
        @if(!empty($product->description))
            <section class="product-full-description-section mb-4">
                <div class="info-card p-4 p-md-5 rounded-4 shadow-sm bg-white border" style="border-radius: 16px !important;">
                    <h4 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i class="fas fa-align-left me-2 text-primary"></i> Product Description (বিবরণ)
                    </h4>
                    <div class="rich-description-area text-secondary" style="font-size: 15px; line-height: 1.7;">
                        {!! $product->description !!}
                    </div>
                </div>
            </section>
        @endif

        <!-- Dynamic Layout Sections -->
        @php
            $sections = $landingSettings['sections'] ?? [];
            if (!is_array($sections)) {
                $sections = [];
            }
            $hasReviewsSection = false;
        @endphp

        @foreach($sections as $section)
            @php
                $secIsActive = isset($section['is_active']) ? (string)$section['is_active'] : '1';
                if ($secIsActive !== '1' && $secIsActive !== 'true' && $secIsActive !== 1) {
                    continue;
                }

                $secType = $section['type'] ?? 'custom';
                if ($secType === 'reviews') {
                    $hasReviewsSection = true;
                }
                $secTitle = $section['title'] ?? '';
                $secTag = $section['tag'] ?? '';
                $bgColor = !empty($section['bg_color']) ? $section['bg_color'] : '#ffffff';
                $textColor = !empty($section['text_color']) ? $section['text_color'] : '#1e293b';
                $fontSize = !empty($section['font_size']) ? $section['font_size'] : '16px';
                $lineHeight = !empty($section['line_height']) ? $section['line_height'] : '1.6';

                $secShowBtn = isset($section['show_button']) ? (string)$section['show_button'] : '0';
                $secBtnText = !empty($section['button_text']) ? $section['button_text'] : 'অর্ডার করতে ক্লিক করুন';
            @endphp

            @if($secType === 'badges')
                @php
                    $secBadges = $section['badges'] ?? $badges;
                @endphp
                @if(!empty($secBadges))
                    <section class="landing-badges-section mb-4">
                        <div class="info-card p-4 rounded-4" style="border-radius: 20px !important; background-color: {{ $bgColor }} !important; color: {{ $textColor }} !important; font-size: {{ $fontSize }} !important; line-height: {{ $lineHeight }} !important;">
                            <div class="text-center mb-4">
                                @if(!empty($secTag))
                                    <span class="section-tag mb-2 d-inline-block">{{ $secTag }}</span>
                                @endif
                                <h3 class="mt-2" style="color: {{ $textColor }} !important; font-size: 24px; font-weight: 700;">{{ $secTitle }}</h3>
                            </div>
                            <div class="row g-3 mb-2 mt-2 justify-content-center">
                                @foreach($secBadges as $badge)
                                    <div class="col-6 col-md-3">
                                        <div class="landing-badge-card p-3.5 rounded-3 text-center border h-100 shadow-sm" style="background: #ffffff; border: 1px solid #e2e8f0 !important; border-radius: 12px !important;">
                                            <i class="{{ $badge['icon'] ?? 'fas fa-check-circle' }} d-block mb-2 fs-3" style="color: #2563eb !important;"></i>
                                            <h5 class="mb-1 fw-bold" style="color: #1e293b !important; font-size: 15px;">{{ $badge['title'] ?? '' }}</h5>
                                            <p class="small mb-0 text-muted" style="font-size: 13px;">{{ $badge['desc'] ?? '' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

            @elseif($secType === 'faq')
                @php
                    $faqItems = $section['faqs'] ?? [];
                @endphp
                @if(!empty($faqItems))
                    <section class="landing-faqs-section mb-4">
                        <div class="info-card p-4 rounded-3 border shadow-sm" style="border-radius: 20px !important; background-color: {{ $bgColor }} !important; color: {{ $textColor }} !important; font-size: {{ $fontSize }} !important; line-height: {{ $lineHeight }} !important;">
                            <div class="section-heading text-center mb-4">
                                @if(!empty($secTag))
                                    <span class="section-tag">{{ $secTag }}</span>
                                @endif
                                <h3 class="mt-2" style="color: {{ $textColor }} !important; font-size: 24px; font-weight: 700;">{{ $secTitle }}</h3>
                            </div>
                            <div class="accordion accordion-flush mx-auto" id="landingFAQAccordion{{ $loop->index }}" style="max-width: 800px;">
                                @foreach($faqItems as $faqIndex => $faq)
                                    <div class="accordion-item border rounded-3 mb-2 overflow-hidden shadow-sm">
                                        <h2 class="accordion-header" id="faqHeading{{ $loop->parent->index }}_{{ $faqIndex }}">
                                            <button class="accordion-button collapsed fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $loop->parent->index }}_{{ $faqIndex }}" aria-expanded="false" aria-controls="faqCollapse{{ $loop->parent->index }}_{{ $faqIndex }}" style="background: rgba(255,255,255,0.95); color: {{ $textColor }} !important; font-weight: 600; outline: none; box-shadow: none;">
                                                <i class="fas fa-question-circle text-primary me-2"></i> {{ $faq['q'] ?? '' }}
                                            </button>
                                        </h2>
                                        <div id="faqCollapse{{ $loop->parent->index }}_{{ $faqIndex }}" class="accordion-collapse collapse" aria-labelledby="faqHeading{{ $loop->parent->index }}_{{ $faqIndex }}" data-bs-parent="#landingFAQAccordion{{ $loop->index }}">
                                            <div class="accordion-body text-start" style="font-size: {{ $fontSize }}; line-height: {{ $lineHeight }}; background: #fff; text-align: left; color: {{ $textColor }} !important;">
                                                {{ $faq['a'] ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif

            @elseif($secType === 'gallery')
                @php
                    $secGalleryImages = !empty($section['images']) && is_array($section['images']) && count(array_filter($section['images'])) > 0
                        ? collect(array_filter($section['images']))
                        : $galleryImages;
                @endphp
                @if($secGalleryImages->count() > 0)
                    <section class="landing-gallery-section my-4">
                        <div class="info-card p-4 rounded-3 border shadow-sm" style="border-radius: 20px !important; background-color: {{ $bgColor }} !important; color: {{ $textColor }} !important;">
                            <div class="section-heading text-center mb-4">
                                @if(!empty($secTag))
                                    <span class="section-tag">{{ $secTag }}</span>
                                @endif
                                <h3 class="mt-2" style="color: {{ $textColor }} !important; font-size: 24px; font-weight: 700;">{{ $secTitle }}</h3>
                            </div>
                            <div id="realGalleryGrid{{ $loop->index }}">
                                <div class="row g-3 justify-content-center">
                                    @foreach($secGalleryImages as $image)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="gallery-image-wrapper card border-0 shadow-sm overflow-hidden p-2 bg-white" style="border-radius: 14px;">
                                                <img src="{{ Str::startsWith($image, ['http://', 'https://', 'data:']) ? $image : asset($image) }}" class="img-fluid rounded"
                                                    alt="Gallery image" style="height: 220px; object-fit: cover; width: 100%;">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

            @elseif($secType === 'video')
                @php
                    $videoUrl = $section['youtube_video_url'] ?? ($landingSettings['youtube_video_url'] ?? '');
                    $videoId = null;
                    if (preg_match('%(?:youtube(?:-nocookie)?\\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|win/.+|watch\\?v=)|youtu\\.be/)([^"&?/\\s]{11})%i', $videoUrl, $match)) {
                        $videoId = $match[1];
                    }
                @endphp
                @if($videoId)
                    <section class="landing-video-section mb-4">
                        <div class="info-card p-4 rounded-3 border shadow-sm" style="border-radius: 20px !important; background-color: {{ $bgColor }} !important; color: {{ $textColor }} !important;">
                            <div class="section-heading text-center mb-4">
                                @if(!empty($secTag))
                                    <span class="section-tag">{{ $secTag }}</span>
                                @endif
                                <h3 class="mt-2" style="color: {{ $textColor }} !important; font-size: 24px; font-weight: 700;">{{ $secTitle }}</h3>
                            </div>
                            <div class="video-container-wrapper mx-auto" style="max-width: 800px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border-radius: 16px; overflow: hidden;">
                                <div class="ratio ratio-16x9">
                                    <iframe src="https://www.youtube.com/embed/{{ $videoId }}" 
                                            title="Product Video" 
                                            allowfullscreen 
                                            style="border: 0;"></iframe>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

            @elseif($secType === 'custom')
                @php
                    $customItems = $section['items'] ?? [];
                    $shortDesc = $section['short_desc'] ?? '';
                @endphp
                @if(!empty($customItems) || !empty($secTitle) || !empty($shortDesc))
                    <section class="landing-custom-section mb-4">
                        <div class="info-card p-4 rounded-3 border shadow-sm" style="border-radius: 20px !important; background-color: {{ $bgColor }} !important; color: {{ $textColor }} !important; font-size: {{ $fontSize }} !important; line-height: {{ $lineHeight }} !important;">
                            <div class="section-heading text-center mb-4">
                                @if(!empty($secTag))
                                    <span class="section-tag">{{ $secTag }}</span>
                                @endif
                                <h3 class="mt-2" style="color: {{ $textColor }} !important; font-size: 24px; font-weight: 700;">{{ $secTitle }}</h3>
                            </div>

                            @if(!empty($shortDesc))
                                <div class="custom-short-desc text-center mx-auto mb-4" style="max-width: 800px; font-size: {{ $fontSize }}; line-height: {{ $lineHeight }}; color: {{ $textColor }} !important; opacity: 0.9;">
                                    {!! nl2br(e($shortDesc)) !!}
                                </div>
                            @endif

                            @if(!empty($customItems))
                                <div class="custom-list-wrapper mx-auto" style="max-width: 800px;">
                                    <ul class="list-unstyled ps-0 d-flex flex-column gap-3">
                                        @foreach($customItems as $itemText)
                                            <li class="d-flex align-items-start gap-3 p-3 rounded-3 shadow-sm border" style="background: rgba(255,255,255,0.9); border-left: 4px solid var(--primary-blue) !important;">
                                                <span class="fs-5 text-primary" style="line-height: 1.2;">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>
                                                <span class="text-start" style="font-size: {{ $fontSize }}; line-height: {{ $lineHeight }}; color: {{ $textColor }} !important;">{{ $itemText }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </section>
                @endif

            @elseif($secType === 'order_form')
                <!-- Custom Order Form Section -->
                <section class="landing-order-form-section my-5" id="order-form">
                    <div class="info-card p-4 p-md-5 rounded-4 shadow-sm border" style="background-color: {{ $bgColor }} !important; color: {{ $textColor }} !important; font-size: {{ $fontSize }} !important; line-height: {{ $lineHeight }} !important;">
                        <div class="section-heading text-center mb-4">
                            @if(!empty($secTag))
                                <span class="section-tag">{{ $secTag }}</span>
                            @endif
                            <h2 class="mt-2 fw-bold" style="color: {{ $textColor }} !important;">{{ $secTitle ?: 'অর্ডার করতে আপনার সঠিক তথ্য দিয়ে ফর্মটি পূরণ করুন' }}</h2>
                            <p class="text-muted small">ক্যাশ অন ডেলিভারিতে পণ্য বুঝে পেয়ে মূল্য পরিশোধ করুন।</p>
                        </div>

                        <form action="{{ route('ecommerce.order.place') }}" method="POST" id="landingDirectCheckoutForm" class="mx-auto" style="max-width: 900px;">
                            @csrf
                            <input type="hidden" name="direct_order" value="1">
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="variant_id" id="directFormVariantId" value="{{ $selectedVariant ? $selectedVariant->id : '' }}">
                            <input type="hidden" name="quantity" id="directFormQuantity" value="1">
                            <input type="hidden" name="shipping_charge" id="directFormShippingCharge" value="130">
                            <input type="hidden" name="coupon_code" id="directAppliedCouponCode" value="">

                            <div class="row g-4">
                                <!-- Customer Billing Information -->
                                <div class="col-lg-6">
                                    <div class="p-3 p-md-4 rounded-3 border bg-light h-100">
                                        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                                            <i class="fas fa-user-circle me-2 text-primary"></i>গ্রাহকের তথ্য
                                        </h5>

                                        <div class="form-group mb-3">
                                            <label class="form-label small fw-bold text-dark">আপনার সম্পূর্ণ নাম <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" placeholder="যেমন: মোঃ আশিকুল ইসলাম" value="{{ old('name', Auth::user()->name ?? '') }}" required>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label small fw-bold text-dark">মোবাইল নম্বর <span class="text-danger">*</span></label>
                                            <input type="text" name="phone" id="directFormPhone" class="form-control" placeholder="যেমন: 017xxxxxxxx" value="{{ old('phone', Auth::user()->patient->phone ?? '') }}" required>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label small fw-bold text-dark">সম্পূর্ণ ঠিকানা (বাসা/রোড/এলাকা/জেলা) <span class="text-danger">*</span></label>
                                            <textarea name="address" class="form-control" rows="2" placeholder="আপনার বিস্তারিত ঠিকানা লিখুন..." required>{{ old('address', Auth::user()->patient->address ?? '') }}</textarea>
                                        </div>

                                        <!-- Hidden dummy email field if guest -->
                                        <input type="hidden" name="email" id="directFormEmail" value="{{ Auth::user()->email ?? '' }}">

                                        <!-- Delivery Area Selector -->
                                        <div class="form-group mb-3">
                                            <label class="form-label small fw-bold text-dark mb-2">ডেলিভারি লোকেশন নির্বাচন করুন <span class="text-danger">*</span></label>
                                            <div class="delivery-options-grid">
                                                <label class="delivery-radio-card">
                                                    <input type="radio" name="delivery_area" value="inside">
                                                    <div class="delivery-radio-content">
                                                        <span class="delivery-title">ঢাকার ভিতরে</span>
                                                        <span class="delivery-price">৳৮০</span>
                                                    </div>
                                                </label>
                                                <label class="delivery-radio-card active">
                                                    <input type="radio" name="delivery_area" value="outside" checked>
                                                    <div class="delivery-radio-content">
                                                        <span class="delivery-title">ঢাকার বাইরে</span>
                                                        <span class="delivery-price">৳১৩০</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Summary & Confirmation -->
                                <div class="col-lg-6">
                                    <div class="p-3 p-md-4 rounded-3 border bg-white shadow-sm h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                                                <i class="fas fa-shopping-bag me-2 text-primary"></i>অর্ডার সামারি
                                            </h5>

                                            <!-- Product overview in order form -->
                                            <div class="d-flex align-items-center gap-3 p-2 rounded border bg-light mb-3">
                                                <div class="direct-checkout-thumb rounded border bg-white">
                                                    <img src="{{ $mainImage }}" alt="{{ $product->name }}">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold text-dark fs-6">{{ Str::limit($product->name, 35) }}</h6>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge bg-primary" id="directFormVariantLabel" style="{{ $selectedVariant ? '' : 'display:none;' }}">
                                                            {{ $selectedVariant ? $selectedVariant->display_label : '' }}
                                                        </span>
                                                        <strong class="text-primary fs-6" id="directFormPriceDisplay">৳{{ number_format($displayPrice, 0) }}</strong>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Quantity selector in order form -->
                                            <div class="d-flex align-items-center justify-content-between p-2 rounded border mb-3">
                                                <span class="small fw-bold text-dark">পরিমাণ (Quantity):</span>
                                                <div class="qty-changer-widget d-flex align-items-center gap-2">
                                                    <button type="button" class="btn btn-sm btn-light border-0 py-0 px-2" id="directQtyDec">-</button>
                                                    <span class="fw-bold px-2" id="directQtyDisplay">1</span>
                                                    <button type="button" class="btn btn-sm btn-light border-0 py-0 px-2" id="directQtyInc">+</button>
                                                </div>
                                            </div>

                                            <!-- Variant Selector in Order Form if Variants Exist -->
                                            @if($hasVariants)
                                                <div class="mb-3">
                                                    <label class="small fw-bold text-dark mb-1">ভ্যারিয়েন্ট নির্বাচন করুন:</label>
                                                    <div class="variant-options-grid">
                                                        @foreach($activeVariants as $vItem)
                                                            <label class="variant-radio-card {{ $selectedVariant && $selectedVariant->id === $vItem->id ? 'active' : '' }}">
                                                                <input type="radio" name="direct_variant_select" value="{{ $vItem->id }}" data-price="{{ $vItem->currentPrice() }}" data-label="{{ $vItem->display_label }}" {{ $selectedVariant && $selectedVariant->id === $vItem->id ? 'checked' : '' }}>
                                                                <div class="d-flex justify-content-between w-100 align-items-center">
                                                                    <span class="fw-semibold text-dark">{{ $vItem->display_label }}</span>
                                                                    <strong class="text-primary">৳{{ number_format($vItem->currentPrice(), 0) }}</strong>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Coupon Form Box -->
                                            <div class="input-group input-group-sm mb-3">
                                                <input type="text" class="form-control" id="directCouponInput" placeholder="কুপন কোড (যদি থাকে)">
                                                <button class="btn btn-outline-primary" type="button" id="applyDirectCouponBtn">প্রয়োগ করুন</button>
                                            </div>
                                            <div id="directCouponMessage" style="display: none;"></div>

                                            <!-- Cost Breakdown -->
                                            <div class="border-top pt-2 mt-2">
                                                <div class="d-flex justify-content-between mb-1 small">
                                                    <span class="text-muted">সাবটোটাল:</span>
                                                    <strong class="text-dark" id="directSubtotal">৳{{ number_format($displayPrice, 0) }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1 small text-success" id="directDiscountRow" style="display: none !important;">
                                                    <span>ডিসকাউন্ট <span id="directCouponCodeDisplay"></span>:</span>
                                                    <strong>-৳<span id="directDiscount">0</span></strong>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2 small">
                                                    <span class="text-muted">ডেলিভারি চার্জ:</span>
                                                    <strong class="text-dark" id="directShipping">৳১৩০</strong>
                                                </div>
                                                <div class="d-flex justify-content-between border-top pt-2 mb-3">
                                                    <span class="h6 mb-0 fw-bold text-dark">সর্বমোট প্রদেয়:</span>
                                                    <span class="h5 mb-0 fw-bold text-primary" id="directTotal">৳{{ number_format($displayPrice + 130, 0) }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Place Order Button -->
                                        <button type="submit" id="directSubmitBtn" class="btn btn-primary btn-lg w-100 fw-bold py-3 shadow hover-grow" {{ $stockQty < 1 ? 'disabled' : '' }}>
                                            <i class="fas fa-shopping-cart me-2"></i> অর্ডার কনফার্ম করুন
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>

            @elseif($secType === 'reviews')
                <!-- Product Reviews Section -->
                <section class="product-reviews-section my-5">
                    <div class="row g-4">
                        <div class="col-lg-5">
                            <div class="info-card review-form-card p-4 rounded-4 shadow-sm border" style="background-color: {{ $bgColor }} !important; color: {{ $textColor }} !important;">
                                <div class="section-heading">
                                    <span class="section-tag">{{ $secTag ?: 'Customer Reviews' }}</span>
                                    <h3 class="fw-bold mt-2" style="color: {{ $textColor }} !important;">{{ $secTitle ?: 'Write a Review' }}</h3>
                                </div>

                                <div class="review-score-card">
                                    <strong>{{ number_format($averageRating, 1) }}</strong>
                                    <div>
                                        <div class="review-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= round($averageRating) ? 'is-filled' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <span>{{ $reviewCount }} customer {{ $reviewCount === 1 ? 'review' : 'reviews' }}</span>
                                    </div>
                                </div>

                                <form action="{{ route('ecommerce.products.reviews.store', $product->id) }}" method="POST" class="review-form">
                                    @csrf
                                    @if(!auth()->check() || !auth()->user()->patient)
                                        <div class="form-group">
                                            <label for="reviewerName" class="small fw-bold">Your Name <span class="text-danger">*</span></label>
                                            <input id="reviewerName" type="text" name="reviewer_name" class="form-control" required value="{{ old('reviewer_name', auth()->check() ? auth()->user()->name : '') }}" placeholder="Enter your name">
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label class="small fw-bold">Select Rating</label>
                                        <div class="review-rating-options">
                                            @for($rating = 5; $rating >= 1; $rating--)
                                                <label class="review-rating-choice">
                                                    <input type="radio" name="rating" value="{{ $rating }}" {{ $selectedReviewRating === $rating ? 'checked' : '' }}>
                                                    <span>
                                                        @for($star = 1; $star <= 5; $star++)
                                                            <i class="fas fa-star {{ $star <= $rating ? 'is-filled' : '' }}"></i>
                                                        @endfor
                                                        {{ $rating }}/5
                                                    </span>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="reviewTitle" class="small fw-bold">Review Title</label>
                                        <input id="reviewTitle" type="text" name="title" class="form-control" value="{{ old('title', $currentUserReview->title ?? '') }}" placeholder="Short headline (Optional)">
                                    </div>

                                    <div class="form-group">
                                        <label for="reviewComment" class="small fw-bold">Your Detailed Review</label>
                                        <textarea id="reviewComment" name="comment" class="form-control" rows="4" required placeholder="Share your experience about product quality, delivery, etc..."></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 review-submit-btn fw-bold">
                                        {{ $currentUserReview ? 'Update Review' : 'Submit Review' }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="col-lg-7">
                            <div class="info-card review-list-card p-4 rounded-4 shadow-sm border" style="background-color: {{ $bgColor }} !important; color: {{ $textColor }} !important;">
                                <div class="section-heading mb-4">
                                    <span class="section-tag">Feedback</span>
                                    <h3 class="fw-bold mt-2" style="color: {{ $textColor }} !important;">Customer Reviews</h3>
                                </div>

                                <div class="review-list">
                                    @forelse($productReviews as $review)
                                        @php
                                            $reviewerName = $review->reviewer_name ?? ($review->patient?->user?->name ?? 'Customer');
                                            $reviewerInitial = !empty($reviewerName) ? strtoupper(substr($reviewerName, 0, 1)) : 'C';
                                        @endphp
                                        <article class="review-item-card">
                                            <div class="review-avatar">{{ $reviewerInitial }}</div>
                                            <div class="review-content">
                                                <div class="review-meta-row">
                                                    <div>
                                                        <strong>{{ $reviewerName }}</strong>
                                                        <span>{{ $review->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    @if($review->is_verified_purchase)
                                                        <em class="review-verified-badge">Verified purchase</em>
                                                    @endif
                                                </div>

                                                <div class="review-stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $review->rating ? 'is-filled' : '' }}"></i>
                                                    @endfor
                                                </div>

                                                @if($review->title)
                                                    <h5 class="fw-bold mt-2 mb-1">{{ $review->title }}</h5>
                                                @endif

                                                <p class="mb-0">{{ $review->comment }}</p>
                                            </div>
                                        </article>
                                    @empty
                                        <div class="review-empty-state text-center py-5">
                                            <i class="far fa-star fs-1 text-muted mb-2"></i>
                                            <strong>No reviews found</strong>
                                            <span class="text-muted d-block">Be the first to share your thoughts by reviewing this product.</span>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <!-- Add Buy Now Button if enabled for this section -->
            @if(!in_array($secType, ['order_form', 'reviews']) && ($secShowBtn === '1' || $secShowBtn === 1 || $secShowBtn === 'true') && $stockQty > 0)
                <div class="text-center my-4">
                    <a href="#order-form" class="btn btn-primary btn-lg detail-buy-btn text-decoration-none d-inline-flex align-items-center justify-content-center mx-auto shadow" style="min-width: 250px; padding: 12px 36px; border-radius: 8px;">
                        <i class="fas fa-bolt me-2"></i>
                        <span>{{ $secBtnText }}</span>
                    </a>
                </div>
            @endif

        @endforeach

        @if(!$hasReviewsSection)
            <!-- Default Product Reviews Section -->
            <section class="product-reviews-section my-5">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="info-card review-form-card p-4 rounded-4 shadow-sm border" style="background-color: #ffffff !important; color: #1e293b !important;">
                            <div class="section-heading">
                                <span class="section-tag">Customer Reviews</span>
                                <h3 class="fw-bold mt-2" style="color: #1e293b !important;">Write a Review</h3>
                            </div>

                            <div class="review-score-card">
                                <strong>{{ number_format($averageRating, 1) }}</strong>
                                <div>
                                    <div class="review-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= round($averageRating) ? 'is-filled' : '' }}"></i>
                                        @endfor
                                    </div>
                                    <span>{{ $reviewCount }} customer {{ $reviewCount === 1 ? 'review' : 'reviews' }}</span>
                                </div>
                            </div>

                            <form action="{{ route('ecommerce.products.reviews.store', $product->id) }}" method="POST" class="review-form">
                                @csrf
                                @if(!auth()->check() || !auth()->user()->patient)
                                    <div class="form-group">
                                        <label for="reviewerNameDefault" class="small fw-bold">Your Name <span class="text-danger">*</span></label>
                                        <input id="reviewerNameDefault" type="text" name="reviewer_name" class="form-control" required value="{{ old('reviewer_name', auth()->check() ? auth()->user()->name : '') }}" placeholder="Enter your name">
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label class="small fw-bold">Select Rating</label>
                                    <div class="review-rating-options">
                                        @for($rating = 5; $rating >= 1; $rating--)
                                            <label class="review-rating-choice">
                                                <input type="radio" name="rating" value="{{ $rating }}" {{ $selectedReviewRating === $rating ? 'checked' : '' }}>
                                                <span>
                                                    @for($star = 1; $star <= 5; $star++)
                                                        <i class="fas fa-star {{ $star <= $rating ? 'is-filled' : '' }}"></i>
                                                    @endfor
                                                    {{ $rating }}/5
                                                </span>
                                            </label>
                                        @endfor
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="reviewTitleDefault" class="small fw-bold">Review Title</label>
                                    <input id="reviewTitleDefault" type="text" name="title" class="form-control" value="{{ old('title', $currentUserReview->title ?? '') }}" placeholder="Short headline (Optional)">
                                </div>

                                <div class="form-group">
                                    <label for="reviewCommentDefault" class="small fw-bold">Your Detailed Review</label>
                                    <textarea id="reviewCommentDefault" name="comment" class="form-control" rows="4" required placeholder="Share your experience about product quality, delivery, etc..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 review-submit-btn fw-bold">
                                    {{ $currentUserReview ? 'Update Review' : 'Submit Review' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="info-card review-list-card p-4 rounded-4 shadow-sm border" style="background-color: #ffffff !important; color: #1e293b !important;">
                            <div class="section-heading mb-4">
                                <span class="section-tag">Feedback</span>
                                <h3 class="fw-bold mt-2" style="color: #1e293b !important;">Customer Reviews</h3>
                            </div>

                            <div class="review-list">
                                @forelse($productReviews as $review)
                                    @php
                                        $reviewerName = $review->reviewer_name ?? ($review->patient?->user?->name ?? 'Customer');
                                        $reviewerInitial = !empty($reviewerName) ? strtoupper(substr($reviewerName, 0, 1)) : 'C';
                                    @endphp
                                    <article class="review-item-card">
                                        <div class="review-avatar">{{ $reviewerInitial }}</div>
                                        <div class="review-content">
                                            <div class="review-meta-row">
                                                <div>
                                                    <strong>{{ $reviewerName }}</strong>
                                                    <span>{{ $review->created_at->diffForHumans() }}</span>
                                                </div>
                                                @if($review->is_verified_purchase)
                                                    <em class="review-verified-badge">Verified purchase</em>
                                                @endif
                                            </div>

                                            <div class="review-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $review->rating ? 'is-filled' : '' }}"></i>
                                                @endfor
                                            </div>

                                            @if($review->title)
                                                <h5 class="fw-bold mt-2 mb-1">{{ $review->title }}</h5>
                                            @endif

                                            <p class="mb-0">{{ $review->comment }}</p>
                                        </div>
                                    </article>
                                @empty
                                    <div class="review-empty-state text-center py-5">
                                        <i class="far fa-star fs-1 text-muted mb-2"></i>
                                        <strong>No reviews found</strong>
                                        <span class="text-muted d-block">Be the first to share your thoughts by reviewing this product.</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        @if($relatedProducts->count() > 0)
            <section class="related-products-section">
                <div class="related-head">
                    <div>
                        <span class="section-tag">More options</span>
                        <h2>Related products</h2>
                    </div>
                    <a href="{{ route('ecommerce.products') }}" class="btn-view-all-arrow">
                        View all products <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="row g-4">
                    @foreach($relatedProducts as $relProduct)
                        @php
                            $relatedImage = $relProduct->image ?: 'assets/img/products/default-product.png';
                            $relatedPrice = $relProduct->effectivePrice();
                            $relatedRegularPrice = $relProduct->effectiveRegularPrice();
                            $relatedHasVariants = $relProduct->hasActiveVariants();
                            $relatedStock = $relProduct->availableStock();
                            $relatedReviews = (int) ($relProduct->reviews_count ?? 0);
                            $relatedRating = $relatedReviews > 0 ? (float) ($relProduct->rating ?? 0) : 0;
                        @endphp
                        <div class="col-md-6 col-xl-3">
                            <div class="product-card-modern">
                                <div class="stock-badge {{ $relatedStock > 0 ? 'in-stock' : 'out-of-stock' }}">
                                    {{ $relatedStock > 0 ? 'IN STOCK' : 'OUT OF STOCK' }}
                                </div>

                                <div class="product-image-container">
                                    <a href="{{ route('ecommerce.products.show', $relProduct->id) }}" wire:navigate class="product-image-link">
                                        <img src="{{ asset($relatedImage) }}"
                                            class="product-main-img"
                                            alt="{{ $relProduct->name }}"
                                            onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                                    </a>
                                </div>

                                <div class="product-details">
                                    <div class="product-rating">
                                        <i class="fas fa-star"></i>
                                        <span class="rating-value">{{ number_format($relatedRating, 1) }}</span>
                                        <span class="review-count">({{ $relatedReviews }})</span>
                                    </div>

                                    <div class="product-brand">{{ $relProduct->category->name ?? 'Healthcare' }}</div>

                                    <h3 class="product-name">
                                        <a href="{{ route('ecommerce.products.show', $relProduct->id) }}" wire:navigate>{{ $relProduct->name }}</a>
                                    </h3>

                                    <div class="product-footer">
                                        <div class="product-price-tag">
                                            <span class="price-current">৳{{ number_format($relatedPrice, 0) }}</span>
                                            @if($relatedPrice < $relatedRegularPrice)
                                                <span class="price-original">৳{{ number_format($relatedRegularPrice, 0) }}</span>
                                            @endif
                                        </div>

                                        @if($relatedHasVariants)
                                            <a href="{{ route('ecommerce.products.show', $relProduct->id) }}" wire:navigate class="btn-buy-modern btn-link-modern">
                                                Select Options
                                            </a>
                                        @else
                                            <form action="{{ route('ecommerce.cart.add') }}" method="POST" class="product-actions-form">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $relProduct->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <div class="btn-group-modern">
                                                    <button type="submit" class="btn-cart-modern" title="Add to Cart" {{ $relatedStock < 1 ? 'disabled' : '' }}>
                                                        <i class="fas fa-shopping-cart"></i>
                                                    </button>
                                                    <button type="submit" name="buy_now" value="1" class="btn-buy-modern" {{ $relatedStock < 1 ? 'disabled' : '' }}>
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
            </section>
        @endif

        @if($stockQty <= 0)
            <div class="alert alert-warning text-center py-4 my-5 rounded-3 shadow-sm border-0">
                <i class="fas fa-exclamation-triangle me-2 fs-4 text-warning"></i>
                <span class="fw-bold fs-5 text-dark">দুঃখিত! এই পণ্যটি বর্তমানে স্টক আউট আছে। স্টক আসলে আপনাকে জানানো হবে।</span>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Landing Page Elements */
    .landing-urgency-banner {
        background: linear-gradient(135deg, var(--primary-blue-dark) 0%, var(--primary-blue) 100%);
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(29, 78, 216, 0.15);
    }

    .countdown-timer-wrapper {
        display: flex;
        gap: 12px;
    }

    .countdown-timer-wrapper .time-block {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        border-radius: 12px;
        padding: 10px 14px;
        text-align: center;
        min-width: 70px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .countdown-timer-wrapper .time-val {
        display: block;
        font-size: 24px;
        font-weight: 800;
        line-height: 1;
        color: #ffffff;
    }

    .countdown-timer-wrapper .time-lbl {
        display: block;
        font-size: 10px;
        text-transform: uppercase;
        margin-top: 4px;
        color: rgba(255, 255, 255, 0.7);
    }

    .offer-badge-pill {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .landing-badge-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px 14px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
        transition: all 0.3s ease;
        height: 100%;
    }

    .landing-badge-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.1);
    }

    .landing-badge-card i {
        font-size: 28px;
        margin-bottom: 12px;
        color: #2563eb !important;
    }

    .landing-badge-card h5 {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .landing-badge-card p {
        font-size: 11px;
        color: #64748b;
        margin-bottom: 0;
    }

    /* Primary CTA Pulsing Button */
    .btn-landing-cta {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: #fff !important;
        border: none !important;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.25) !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        animation: landing-pulse 2.5s infinite;
        padding: 14px 28px !important;
        border-radius: 12px !important;
    }

    .btn-landing-cta:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        box-shadow: 0 12px 30px rgba(16, 185, 129, 0.45) !important;
        transform: translateY(-2px) !important;
    }

    @keyframes landing-pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.03);
            box-shadow: 0 12px 30px rgba(29, 78, 216, 0.45);
        }
        100% {
            transform: scale(1);
        }
    }

    /* Delivery Options Radio Grid */
    .delivery-options-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .delivery-radio-card {
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fff;
        transition: all 0.2s ease;
        margin-bottom: 0;
    }

    .delivery-radio-card input[type="radio"] {
        width: 18px;
        height: 18px;
        accent-color: #1d4ed8;
    }

    .delivery-radio-card.active {
        border-color: #1d4ed8;
        background: #eff6ff;
    }

    .delivery-radio-content {
        display: flex;
        flex-direction: column;
    }

    .delivery-title {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }

    .delivery-price {
        font-size: 15px;
        font-weight: 800;
        color: #1d4ed8;
    }

    .direct-checkout-thumb {
        width: 64px;
        height: 64px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        padding: 4px;
    }

    .direct-checkout-thumb img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .qty-changer-widget {
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 2px 6px;
        width: fit-content;
    }

    .qty-changer-widget button {
        font-weight: 800;
        font-size: 14px;
    }

    .hover-grow {
        transition: all 0.2s ease;
    }

    .hover-grow:hover {
        transform: translateY(-2px);
    }

    .product-single-page {
        background: #fdfdfd;
        padding: 20px 0 70px;
    }

    /* Reference Breadcrumbs */
    .product-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 24px;
        color: #94a3b8;
        font-size: 13px;
        font-weight: 500;
    }

    .product-breadcrumb a {
        color: #475569;
        text-decoration: none;
        transition: color 0.2s;
    }

    .product-breadcrumb a:hover {
        color: #1d4ed8;
    }

    .product-breadcrumb span {
        display: inline-flex;
        align-items: center;
    }

    .product-breadcrumb span i {
        font-size: 10px;
        color: #cbd5e1;
    }

    /* Reference Gallery Layout */
    .ref-gallery-wrapper {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .ref-main-image-box {
        position: relative;
        width: 100%;
        height: 520px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    .ref-image-inner {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ref-image-inner img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    /* Floating Action Buttons (Share / Wishlist) */
    .ref-floating-actions {
        position: absolute;
        top: 16px;
        right: 16px;
        z-index: 10;
        display: flex;
        gap: 8px;
    }

    .ref-action-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #475569;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .ref-action-btn:hover {
        background: #f8fafc;
        color: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }

    .ref-action-btn.fav-btn.active,
    .ref-action-btn.fav-btn:hover {
        color: #ef4444;
    }

    /* Image Navigation Arrows on Main Frame */
    .ref-nav-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #334155;
        font-size: 13px;
        cursor: pointer;
        z-index: 9;
        transition: all 0.2s ease;
    }

    .ref-nav-arrow:hover {
        background: #1d4ed8;
        color: #ffffff;
        border-color: #1d4ed8;
    }

    .ref-arrow-prev {
        left: 14px;
    }

    .ref-arrow-next {
        right: 14px;
    }

    /* Thumbnails Row */
    .ref-thumb-row {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 4px;
    }

    .ref-thumb-item {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        background: #f8fafc;
        border: 2px solid transparent;
        padding: 6px;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ref-thumb-item img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .ref-thumb-item:hover {
        border-color: #cbd5e1;
    }

    .ref-thumb-item.is-active {
        border-color: #1d4ed8;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(29, 78, 216, 0.15);
    }

    /* Right Summary Card */
    .ref-summary-card {
        padding: 6px 12px;
    }

    .ref-brand-label {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 6px;
        text-transform: capitalize;
    }

    .ref-product-title {
        font-size: 32px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        margin-bottom: 16px;
    }

    /* Price, Sold & Rating Row */
    .ref-price-rating-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 18px;
    }

    .ref-price-group {
        display: flex;
        align-items: baseline;
        gap: 10px;
    }

    .ref-price-original {
        font-size: 18px;
        color: #94a3b8;
        text-decoration: line-through;
        font-weight: 500;
    }

    .ref-price-current {
        font-size: 30px;
        font-weight: 800;
        color: #0f172a;
    }

    .ref-meta-group {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }

    .ref-meta-dot {
        color: #cbd5e1;
        font-size: 14px;
    }

    .ref-rating-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-weight: 600;
        color: #0f172a;
    }

    .ref-divider {
        border-color: #f1f5f9;
        margin: 18px 0;
    }

    /* Description Block */
    .ref-description-block {
        margin-bottom: 22px;
    }

    .ref-desc-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .ref-desc-text {
        font-size: 14px;
        line-height: 1.65;
        color: #64748b;
        margin-bottom: 0;
    }

    /* Variant Section (Color/Size Chips) */
    .ref-variant-section {
        margin-bottom: 22px;
    }

    .ref-variant-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .ref-variant-label {
        font-size: 14px;
        color: #475569;
        margin-bottom: 0;
    }

    .ref-variant-label strong {
        color: #0f172a;
    }

    .ref-guide-link {
        font-size: 13px;
        font-weight: 600;
        color: #1d4ed8;
        text-decoration: underline;
        cursor: pointer;
    }

    .ref-variant-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .ref-chip-btn {
        min-width: 44px;
        height: 42px;
        padding: 0 16px;
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        background: #ffffff;
        color: #1e293b;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .ref-chip-btn:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .ref-chip-btn.is-active {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }

    /* Quantity Control */
    .ref-quantity-section {
        margin-bottom: 26px;
    }

    .ref-qty-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
    }

    .ref-qty-control {
        display: inline-flex;
        align-items: center;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        background: #ffffff;
        overflow: hidden;
    }

    .ref-qty-btn {
        width: 40px;
        height: 40px;
        border: none;
        background: #ffffff;
        color: #475569;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
    }

    .ref-qty-btn:hover:not(:disabled) {
        background: #f1f5f9;
        color: #0f172a;
    }

    .ref-qty-input {
        width: 48px;
        height: 40px;
        border: none;
        text-align: center;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        background: transparent;
    }

    /* Action Buttons (Styled Exactly Like Header Sign Up & Login Buttons) */
    .ref-action-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .ref-btn-buy-now {
        height: 48px;
        border-radius: 4px !important;
        background: #2563eb !important;
        color: #ffffff !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        border: 1.5px solid #2563eb !important;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2) !important;
    }

    .ref-btn-buy-now:hover:not(:disabled) {
        background: #1d4ed8 !important;
        border-color: #1d4ed8 !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(37, 99, 235, 0.3) !important;
        color: #ffffff !important;
    }

    .ref-btn-cart {
        height: 48px;
        border-radius: 4px !important;
        background: #ffffff !important;
        color: #2563eb !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        border: 1.5px solid #2563eb !important;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .ref-btn-cart:hover:not(:disabled) {
        background: #eff6ff !important;
        color: #1d4ed8 !important;
        border-color: #1d4ed8 !important;
        transform: translateY(-1px);
    }

    .ref-delivery-link {
        color: #64748b;
        font-size: 13px;
        text-decoration: underline;
        font-weight: 500;
    }

    .ref-delivery-link:hover {
        color: #1d4ed8;
    }

    .product-hero-card {
        margin-bottom: 28px;
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

    .product-gallery-shell {
        padding: 18px;
        gap: 18px;
    }

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

    .detail-offer-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 10;
        padding: 6px 12px;
        border-radius: 999px;
        background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.05em;
    }

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

    .detail-main-image {
        height: 440px;
        border-radius: 18px;
        background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
        padding: 20px;
    }

    .product-zoom-frame {
        cursor: zoom-in;
    }

    .product-zoom-frame::after {
        content: 'Hover to zoom';
        position: absolute;
        right: 18px;
        bottom: 18px;
        z-index: 5;
        border-radius: 999px;
        padding: 7px 12px;
        background: rgba(15, 23, 42, 0.72);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        opacity: 0;
        transform: translateY(8px);
        transition: opacity 0.2s ease, transform 0.2s ease;
        pointer-events: none;
    }

    .product-zoom-frame:hover::after {
        opacity: 1;
        transform: translateY(0);
    }

    .product-zoom-frame.is-zooming {
        cursor: zoom-out;
    }

    .product-zoom-frame .product-main-img {
        will-change: transform, transform-origin;
    }

    .product-zoom-frame.is-zooming .product-main-img {
        transform: scale(1.9);
        transition-duration: 0.12s;
    }

    .product-image-link {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        text-decoration: none;
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

    .product-thumb-strip {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .product-thumb {
        width: 78px;
        height: 78px;
        border-radius: 18px;
        background: #fff;
        padding: 8px;
        transition: all .25s ease;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
    }

    .product-thumb img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .product-thumb:hover,
    .product-thumb.is-active {
        box-shadow: 0 12px 24px rgba(59, 130, 246, 0.18);
        transform: translateY(-2px);
    }

    .detail-trust-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .detail-trust-card {
        background: #f8fbff;
        border-radius: 16px;
        padding: 14px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .detail-trust-card i,
    .summary-feature-list i,
    .assurance-item i {
        color: #2563eb;
        margin-top: 2px;
    }

    .detail-trust-card strong {
        display: block;
        color: #0f172a;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .detail-trust-card span {
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .product-summary-card {
        position: sticky;
        top: 96px;
        background: #fff;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        border: 1px solid #eef2f7;
    }

    .product-rating {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .product-rating-large {
        margin-bottom: 14px;
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

    .product-brand {
        font-size: 11px;
        color: #1D4ED8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .summary-head h1 {
        margin-bottom: 12px;
        color: #0f172a;
        font-size: clamp(28px, 3vw, 40px);
        line-height: 1.15;
    }

    .summary-copy {
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
        margin-bottom: 0;
    }

    .summary-price-box {
        margin-top: 22px;
        padding: 20px;
        border-radius: 18px;
        background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%);
    }

    .product-price-tag {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .price-current {
        color: #0f172a;
        font-size: 36px;
        font-weight: 800;
        line-height: 1.05;
    }

    .price-original {
        color: #94a3b8;
        font-size: 16px;
        text-decoration: line-through;
    }

    .price-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 12px;
    }

    .price-save {
        color: #ea580c;
        background: rgba(249, 115, 22, 0.12);
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
    }

    .price-note {
        color: #334155;
        font-size: 14px;
        font-weight: 600;
    }

    .summary-status-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }

    .status-card {
        background: #f8fbff;
        border-radius: 18px;
        padding: 14px;
    }

    .status-label {
        display: block;
        color: #64748b;
        font-size: 12px;
        margin-bottom: 6px;
    }

    .status-card strong {
        color: #0f172a;
        font-size: 15px;
    }

    .summary-feature-list {
        list-style: none;
        padding: 0;
        margin: 20px 0 0;
        display: grid;
        gap: 10px;
    }

    .summary-feature-list li {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        color: #334155;
        font-size: 14px;
    }

    .product-tag-list {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .product-tag-list span {
        background: #eff6ff;
        color: #1d4ed8;
        border-radius: 999px;
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 600;
    }

    .purchase-form {
        margin-top: 24px;
    }

    .purchase-controls {
        display: grid;
        gap: 14px;
    }

    .variant-box label {
        display: block;
        margin-bottom: 10px;
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
    }

    .variant-select {
        width: 100%;
        min-height: 52px;
        border-radius: 14px;
        border: 1px solid #dbeafe;
        background: #f8fbff;
        color: #0f172a;
        padding: 12px 14px;
        font-weight: 600;
    }

    .variant-select:focus {
        outline: none;
        border-color: #60a5fa;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
    }

    .quantity-box label {
        display: block;
        margin-bottom: 10px;
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
    }

    .quantity-control {
        display: inline-flex;
        align-items: center;
        border-radius: 16px;
        overflow: hidden;
        background: #f8fbff;
        border: 1px solid #dbeafe;
    }

    .qty-btn {
        width: 48px;
        height: 48px;
        border: 0;
        background: #f8fbff;
        color: #0f172a;
        font-size: 22px;
    }

    .qty-input {
        width: 86px;
        border: 0;
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        box-shadow: none;
        appearance: textfield;
        -moz-appearance: textfield;
    }

    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .support-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 14px;
        border-radius: 16px;
        background: #f8fbff;
        color: #475569;
        font-size: 13px;
    }

    .product-footer {
        margin-top: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .detail-product-footer {
        justify-content: flex-start;
    }

    .product-actions-form {
        display: flex;
    }

    .btn-group-modern {
        display: flex;
        gap: 6px;
    }

    .detail-btn-group {
        width: 100%;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 10px;
    }

    .btn-cart-modern,
    .btn-buy-modern {
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cart-modern {
        width: 38px;
        height: 38px;
        border: 2px solid #1D4ED8;
        background: transparent;
        color: #1D4ED8;
    }

    .btn-buy-modern {
        padding: 0 20px;
        height: 38px;
        background: linear-gradient(135deg, #1D4ED8 0%, #60A5FA 100%);
        color: #fff;
        font-weight: 600;
        font-size: 13px;
    }

    .btn-cart-modern:hover {
        background: #1D4ED8;
        color: #fff;
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

    .detail-cart-btn,
    .detail-buy-btn {
        width: 100%;
        height: 52px;
        border-radius: 14px;
        font-size: 15px;
        font-weight: 700;
        gap: 8px;
    }

    .detail-cart-btn {
        background: #fff;
        border: 2px solid #1D4ED8;
        color: #1D4ED8;
    }

    .detail-buy-btn {
        flex: 1;
    }

    .detail-cart-btn:hover {
        background: #1D4ED8;
        color: #fff;
    }

    .detail-view-cart {
        margin-top: 12px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #334155;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
    }

    .detail-view-cart:hover {
        color: #1D4ED8;
    }

    .btn-cart-modern:disabled,
    .btn-buy-modern:disabled {
        background: #cbd5e1;
        color: #64748b;
        border-color: #cbd5e1;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .summary-assurance {
        display: grid;
        gap: 10px;
        margin-top: 20px;
        padding-top: 18px;
        border-top: 1px solid #edf2f7;
    }

    .assurance-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #334155;
        font-size: 14px;
        font-weight: 500;
    }

    .product-detail-sections {
        margin-bottom: 28px;
    }

    .info-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #eef2f7;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.05);
        padding: 24px;
        height: 100%;
    }

    .section-heading {
        margin-bottom: 16px;
    }

    .section-tag {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        padding: 7px 12px;
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
        margin-bottom: 10px;
    }

    .section-heading h2 {
        color: #0f172a;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 0;
    }

    .info-copy {
        color: #475569;
        line-height: 1.8;
        margin-bottom: 0;
    }

    .info-copy p:last-child {
        margin-bottom: 0;
    }

    .info-chip-list {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .info-chip-list span {
        background: #f8fbff;
        border: 1px solid #dbeafe;
        color: #1e3a8a;
        border-radius: 999px;
        padding: 9px 14px;
        font-size: 13px;
        font-weight: 600;
    }

    .compact-feature-list {
        margin-top: 0;
    }

    .product-reviews-section {
        margin-bottom: 28px;
    }

    .review-form-card,
    .review-list-card {
        height: 100%;
    }

    .review-score-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 18px;
        border-radius: 18px;
        background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%);
        margin-bottom: 20px;
    }

    .review-score-card strong {
        color: #0f172a;
        font-size: 42px;
        line-height: 1;
    }

    .review-score-card span {
        display: block;
        color: #64748b;
        font-size: 13px;
        margin-top: 4px;
    }

    .review-stars {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        color: #cbd5e1;
        font-size: 13px;
    }

    .review-stars .is-filled,
    .review-rating-choice .is-filled {
        color: #f59e0b;
    }

    .review-form {
        display: grid;
        gap: 16px;
    }

    .review-form label {
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .review-form .form-control {
        min-height: 48px;
        border: 1px solid #dbeafe;
        border-radius: 14px;
        background: #f8fbff;
        color: #0f172a;
        box-shadow: none;
    }

    .review-form textarea.form-control {
        min-height: 130px;
        resize: vertical;
    }

    .review-rating-options {
        display: grid;
        gap: 8px;
    }

    .review-rating-choice {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
        cursor: pointer;
        margin: 0;
    }

    .review-rating-choice span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
    }

    .review-submit-btn {
        width: 100%;
        height: 50px;
        border-radius: 14px;
    }

    .review-access-card,
    .review-empty-state {
        display: grid;
        gap: 10px;
        padding: 18px;
        border-radius: 18px;
        background: #f8fbff;
        border: 1px solid #dbeafe;
        color: #64748b;
    }

    .review-access-card strong,
    .review-empty-state strong {
        color: #0f172a;
        font-size: 16px;
    }

    .review-access-card .btn-view-all-arrow {
        width: fit-content;
        margin-top: 4px;
    }

    .review-list {
        display: grid;
        gap: 16px;
    }

    .review-item-card {
        display: flex;
        gap: 14px;
        padding: 18px;
        border: 1px solid #eef2f7;
        border-radius: 18px;
        background: #fff;
    }

    .review-avatar {
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #1d4ed8 0%, #60a5fa 100%);
        color: #fff;
        font-weight: 800;
    }

    .review-content {
        flex: 1;
        min-width: 0;
    }

    .review-meta-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 8px;
    }

    .review-meta-row strong {
        display: block;
        color: #0f172a;
        font-size: 15px;
    }

    .review-meta-row span {
        color: #94a3b8;
        font-size: 12px;
    }

    .review-verified-badge {
        align-self: flex-start;
        border-radius: 999px;
        background: #dcfce7;
        color: #15803d;
        padding: 5px 10px;
        font-size: 11px;
        font-style: normal;
        font-weight: 800;
    }

    .review-content h3 {
        margin: 10px 0 6px;
        color: #0f172a;
        font-size: 16px;
        font-weight: 800;
    }

    .review-content p {
        color: #475569;
        line-height: 1.7;
        margin: 8px 0 0;
    }

    .review-empty-state {
        text-align: center;
        justify-items: center;
        padding: 34px 18px;
    }

    .review-empty-state i {
        color: #cbd5e1;
        font-size: 28px;
    }

    .related-products-section {
        margin-top: 0;
    }

    .related-head h2 {
        color: #0f172a;
        margin-bottom: 10px;
        font-size: 24px;
        font-weight: 700;
    }

    .related-head {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    .btn-view-all-arrow {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 22px;
        border-radius: 999px;
        background: linear-gradient(135deg, #1d4ed8 0%, #60a5fa 100%);
        color: #fff;
        text-decoration: none;
        font-weight: 700;
        box-shadow: 0 10px 24px rgba(29, 78, 216, 0.22);
    }

    .btn-view-all-arrow:hover {
        color: #fff;
        transform: translateY(-2px);
    }

    .product-details {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

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

    @media (max-width: 1199px) {
        .summary-head h1 {
            font-size: 32px;
        }
    }

    @media (hover: none), (pointer: coarse) {
        .product-zoom-frame {
            cursor: default;
        }

        .product-zoom-frame::after {
            display: none;
        }

        .product-zoom-frame.is-zooming .product-main-img {
            transform: none;
        }
    }

    @media (max-width: 991px) {
        .product-single-page {
            padding-top: 18px;
        }

        .detail-main-image {
            height: 340px;
        }

        .detail-trust-grid {
            grid-template-columns: 1fr;
        }

        .product-summary-card {
            position: static;
        }

        .related-head {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media (max-width: 575px) {
        .product-summary-card,
        .product-gallery-shell,
        .info-card,
        .product-card-modern {
            border-radius: 20px;
        }

        .summary-status-grid,
        .detail-btn-group {
            grid-template-columns: 1fr;
        }

        .detail-btn-group {
            display: grid;
        }

        .product-thumb {
            width: 68px;
            height: 68px;
            border-radius: 16px;
        }

        .detail-main-image {
            height: 280px;
        }

        .detail-cart-btn {
            width: 100%;
        }

        .review-item-card {
            flex-direction: column;
        }
    }

    /* Variant Options Radio Grid */
    .variant-options-grid {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .variant-radio-card {
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fff;
        transition: all 0.2s ease;
        margin-bottom: 0;
    }

    .variant-radio-card input[type="radio"] {
        width: 18px;
        height: 18px;
        accent-color: var(--primary-blue);
        flex-shrink: 0;
    }

    .variant-radio-card.active {
        border-color: var(--primary-blue);
        background: #eff6ff;
    }

    .variant-item-image {
        width: 50px;
        height: 50px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 2px;
        overflow: hidden;
    }

    .variant-item-image img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Core elements
        const activeImage = document.getElementById('activeProductImage');
        const zoomFrame = document.querySelector('.product-zoom-frame');
        const thumbs = document.querySelectorAll('.product-thumb');
        const variantSelect = document.getElementById('productVariant');
        const quantityInput = document.getElementById('productQuantity');
        const qtyButtons = document.querySelectorAll('.qty-btn');
        const currentPrice = document.getElementById('productCurrentPrice');
        const originalPrice = document.getElementById('productOriginalPrice');
        const offerBadge = document.getElementById('productOfferBadge');
        const priceMeta = document.getElementById('productPriceMeta');
        const availabilityValue = document.getElementById('productAvailabilityValue');
        const skuValue = document.getElementById('productSkuValue');
        const infoSku = document.getElementById('productInfoSku');
        const infoStock = document.getElementById('productInfoStock');
        const stockBadge = document.getElementById('productStockBadge');
        const stockBadgeText = document.getElementById('productStockBadgeText');
        const priceType = document.getElementById('productPriceType');
        const submitButtons = document.querySelectorAll('.detail-cart-btn, .detail-buy-btn');

        // Direct Checkout Logic
        const directFormVariantId = document.getElementById('directFormVariantId');
        const directFormQuantity = document.getElementById('directFormQuantity');
        const directFormShippingCharge = document.getElementById('directFormShippingCharge');
        const directFormVariantLabel = document.getElementById('directFormVariantLabel');
        const directQtyDisplay = document.getElementById('directQtyDisplay');
        const directSubtotal = document.getElementById('directSubtotal');
        const directShipping = document.getElementById('directShipping');
        const directDiscountRow = document.getElementById('directDiscountRow');
        const directDiscountVal = document.getElementById('directDiscount');
        const directTotal = document.getElementById('directTotal');
        const directAppliedCouponCode = document.getElementById('directAppliedCouponCode');
        const directCouponInput = document.getElementById('directCouponInput');
        const applyDirectCouponBtn = document.getElementById('applyDirectCouponBtn');
        const directCouponMessage = document.getElementById('directCouponMessage');
        const directFormPhone = document.getElementById('directFormPhone');
        const directFormEmail = document.getElementById('directFormEmail');

        let directQuantity = 1;
        let directPrice = {{ (float) $displayPrice }};
        let directShippingCharge = 130;
        let directDiscount = 0;
        let directCouponType = null;
        let directCouponAmount = 0;

        // Submit main purchase form on landing CTA click
        document.querySelectorAll('.landing-buy-now').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const mainForm = document.querySelector('.purchase-form');
                if (mainForm) {
                    let buyNowInput = mainForm.querySelector('input[name="buy_now"]');
                    if (!buyNowInput) {
                        buyNowInput = document.createElement('input');
                        buyNowInput.type = 'hidden';
                        buyNowInput.name = 'buy_now';
                        buyNowInput.value = '1';
                        mainForm.appendChild(buyNowInput);
                    } else {
                        buyNowInput.value = '1';
                    }
                    mainForm.submit();
                }
            });
        });

        @if($showCountdown)
        // Looping countdown timer
        function startLandingCountdown() {
            const timerKey = 'landing_countdown_target_time_product_{{ $product->id }}_{{ $countdownHours }}';
            let targetTime = localStorage.getItem(timerKey);
            const now = new Date().getTime();
            const durationMs = {{ $countdownHours }} * 60 * 60 * 1000;

            if (!targetTime || now > Number(targetTime)) {
                targetTime = now + durationMs;
                localStorage.setItem(timerKey, targetTime);
            } else {
                targetTime = Number(targetTime);
            }

            const hrsEl = document.getElementById('timer-hours');
            const minsEl = document.getElementById('timer-minutes');
            const secsEl = document.getElementById('timer-seconds');

            function updateTime() {
                const currentTime = new Date().getTime();
                const diff = targetTime - currentTime;

                if (diff <= 0) {
                    const newTarget = currentTime + durationMs;
                    localStorage.setItem(timerKey, newTarget);
                    targetTime = newTarget;
                    return;
                }

                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                if (hrsEl) hrsEl.textContent = String(hours).padStart(2, '0');
                if (minsEl) minsEl.textContent = String(minutes).padStart(2, '0');
                if (secsEl) secsEl.textContent = String(seconds).padStart(2, '0');
            }

            updateTime();
            setInterval(updateTime, 1000);
        }
        startLandingCountdown();
        @endif

        // Calculate totals dynamically
        function recalculateDirectOrder() {
            // Subtotal
            const subtotal = directPrice * directQuantity;
            if (directSubtotal) {
                directSubtotal.textContent = formatMoney(subtotal);
            }

            // Coupon Discount
            if (directCouponType === 'fixed') {
                directDiscount = directCouponAmount;
            } else if (directCouponType === 'percentage') {
                directDiscount = (subtotal * directCouponAmount) / 100;
            } else {
                directDiscount = 0;
            }

            if (directDiscount > 0) {
                if (directDiscountRow) directDiscountRow.style.setProperty('display', 'flex', 'important');
                if (directDiscountVal) directDiscountVal.textContent = Math.round(directDiscount);
            } else {
                if (directDiscountRow) directDiscountRow.style.setProperty('display', 'none', 'important');
                if (directDiscountVal) directDiscountVal.textContent = '0';
            }

            // Shipping
            if (directShipping) {
                directShipping.textContent = formatMoney(directShippingCharge);
            }
            if (directFormShippingCharge) {
                directFormShippingCharge.value = directShippingCharge;
            }

            // Grand Total
            const total = Math.max(0, subtotal - directDiscount + directShippingCharge);
            if (directTotal) {
                directTotal.textContent = formatMoney(total);
            }

            // Dynamic Order Button Price Update
            const directSubmitBtn = document.getElementById('directSubmitBtn');
            if (directSubmitBtn) {
                directSubmitBtn.innerHTML = '<i class="fas fa-shopping-cart me-2"></i> অর্ডার করুন  ' + formatMoney(total);
            }
        }

        // Delivery Area Selection
        const deliveryRadios = document.querySelectorAll('input[name="delivery_area"]');
        const deliveryRadioCards = document.querySelectorAll('.delivery-radio-card');

        deliveryRadios.forEach(radio => {
            radio.addEventListener('change', function () {
                deliveryRadioCards.forEach(card => card.classList.remove('active'));
                
                const card = this.closest('.delivery-radio-card');
                if (card) {
                    card.classList.add('active');
                }

                if (this.value === 'inside') {
                    directShippingCharge = 80;
                } else {
                    directShippingCharge = 130;
                }

                recalculateDirectOrder();
            });
        });

        // Quantity Increment / Decrement
        const directQtyDisplayVal = document.getElementById('directQtyDisplay');
        const directQtyDecBtn = document.getElementById('directQtyDec');
        const directQtyIncBtn = document.getElementById('directQtyInc');

        function updateDirectQuantity(qty) {
            const stockMax = quantityInput ? Number(quantityInput.max || 999) : 999;
            directQuantity = Math.max(1, Math.min(stockMax, qty));

            if (directQtyDisplayVal) {
                directQtyDisplayVal.textContent = directQuantity;
            }
            if (directFormQuantity) {
                directFormQuantity.value = directQuantity;
            }

            // Sync with upper quantity field if it exists
            if (quantityInput) {
                quantityInput.value = directQuantity;
            }

            recalculateDirectOrder();
        }

        if (directQtyDecBtn) {
            directQtyDecBtn.addEventListener('click', function () {
                updateDirectQuantity(directQuantity - 1);
            });
        }

        if (directQtyIncBtn) {
            directQtyIncBtn.addEventListener('click', function () {
                updateDirectQuantity(directQuantity + 1);
            });
        }

        // Sync with top quantity changes
        if (quantityInput) {
            quantityInput.addEventListener('input', function () {
                updateDirectQuantity(Number(this.value || 1));
            });
            quantityInput.addEventListener('change', function () {
                updateDirectQuantity(Number(this.value || 1));
            });
        }

        // Sync with variant changes
        function syncLandingVariantDetails() {
            if (!variantSelect) return;
            const selectedOption = variantSelect.options[variantSelect.selectedIndex];
            if (!selectedOption) return;

            const price = Number(selectedOption.dataset.price || 0);
            const variantId = selectedOption.value;
            const label = selectedOption.dataset.label || '';

            directPrice = price;
            
            if (directFormVariantId) {
                directFormVariantId.value = variantId;
            }
            if (directFormVariantLabel) {
                directFormVariantLabel.textContent = label;
                directFormVariantLabel.style.display = label ? '' : 'none';
            }
            
            const directFormPriceDisplay = document.getElementById('directFormPriceDisplay');
            if (directFormPriceDisplay) {
                directFormPriceDisplay.textContent = formatMoney(price);
            }

            // Sync checkout form radio buttons
            const matchingRadio = document.querySelector(`input[name="direct_variant_select"][value="${variantId}"]`);
            if (matchingRadio) {
                matchingRadio.checked = true;
                document.querySelectorAll('.variant-radio-card').forEach(card => card.classList.remove('active'));
                const card = matchingRadio.closest('.variant-radio-card');
                if (card) {
                    card.classList.add('active');
                }
            }

            // Update quantity max limit based on variant stock
            const stock = Number(selectedOption.dataset.stock || 0);
            if (quantityInput) {
                quantityInput.max = Math.max(1, stock);
            }

            updateDirectQuantity(directQuantity);
        }

        // Handle checkout variant radio card selection
        const directVariantRadios = document.querySelectorAll('input[name="direct_variant_select"]');
        directVariantRadios.forEach(radio => {
            radio.addEventListener('change', function () {
                const variantId = this.value;
                const price = Number(this.dataset.price || 0);
                const label = this.dataset.label || '';

                // Update active card styling
                document.querySelectorAll('.variant-radio-card').forEach(card => card.classList.remove('active'));
                const card = this.closest('.variant-radio-card');
                if (card) {
                    card.classList.add('active');
                }

                // Sync with upper dropdown if exists
                if (variantSelect) {
                    variantSelect.value = variantId;
                    variantSelect.dispatchEvent(new Event('change'));
                }

                // Update direct checkout form state
                directPrice = price;
                if (directFormVariantId) {
                    directFormVariantId.value = variantId;
                }
                if (directFormVariantLabel) {
                    directFormVariantLabel.textContent = label;
                    directFormVariantLabel.style.display = label ? '' : 'none';
                }
                const directFormPriceDisplay = document.getElementById('directFormPriceDisplay');
                if (directFormPriceDisplay) {
                    directFormPriceDisplay.textContent = formatMoney(price);
                }

                recalculateDirectOrder();
            });
        });

        if (variantSelect) {
            // Also call sync on original select change
            variantSelect.addEventListener('change', syncLandingVariantDetails);
            syncLandingVariantDetails();
        }

        // Coupon AJAX for Direct Checkout
        if (applyDirectCouponBtn) {
            applyDirectCouponBtn.addEventListener('click', function () {
                const code = directCouponInput.value.trim();
                if (!code) {
                    showCouponMessage('Please enter a coupon code.', 'danger');
                    return;
                }

                $.ajax({
                    url: '{{ route("ecommerce.cart.coupon") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        coupon_code: code
                    },
                    success: function (response) {
                        if (response.success) {
                            directCouponType = response.type;
                            directCouponAmount = parseFloat(response.amount);
                            
                            if (directAppliedCouponCode) {
                                directAppliedCouponCode.value = response.code;
                            }
                            
                            const dispCodeEl = document.getElementById('directCouponCodeDisplay');
                            if (dispCodeEl) {
                                dispCodeEl.textContent = '(' + response.code + ')';
                            }

                            showCouponMessage(response.message, 'success');
                            recalculateDirectOrder();
                        } else {
                            showCouponMessage(response.message, 'danger');
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Failed to apply coupon';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showCouponMessage(msg, 'danger');
                    }
                });
            });
        }

        function showCouponMessage(msg, type) {
            if (!directCouponMessage) return;
            directCouponMessage.style.setProperty('display', 'block', 'important');
            directCouponMessage.className = 'small mt-1 text-' + (type === 'success' ? 'success' : 'danger');
            directCouponMessage.innerHTML = (type === 'success' ? '<i class="fas fa-check-circle me-1"></i>' : '<i class="fas fa-exclamation-circle me-1"></i>') + msg;
        }

        // Form Submit Handler to auto-populate dummy email if blank
        const landingDirectCheckoutForm = document.getElementById('landingDirectCheckoutForm');
        if (landingDirectCheckoutForm) {
            landingDirectCheckoutForm.addEventListener('submit', function (e) {
                if (directFormEmail && !directFormEmail.value) {
                    // Generate a dummy email to pass validation if email is omitted
                    const phoneVal = directFormPhone ? directFormPhone.value.trim() : 'guest';
                    directFormEmail.value = 'guest_' + phoneVal.replace(/[^0-9a-zA-Z]/g, '') + '@abcsheba.com';
                }
            });
        }


        function updateZoomOrigin(event) {
            if (!zoomFrame || !activeImage) {
                return;
            }

            const rect = zoomFrame.getBoundingClientRect();
            const x = Math.max(0, Math.min(100, ((event.clientX - rect.left) / rect.width) * 100));
            const y = Math.max(0, Math.min(100, ((event.clientY - rect.top) / rect.height) * 100));

            activeImage.style.transformOrigin = x.toFixed(2) + '% ' + y.toFixed(2) + '%';
        }

        if (zoomFrame && activeImage && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
            zoomFrame.addEventListener('mouseenter', function (event) {
                zoomFrame.classList.add('is-zooming');
                updateZoomOrigin(event);
            });

            zoomFrame.addEventListener('mousemove', updateZoomOrigin);

            zoomFrame.addEventListener('mouseleave', function () {
                zoomFrame.classList.remove('is-zooming');
                activeImage.style.transformOrigin = 'center center';
            });
        }

        thumbs.forEach((thumb) => {
            thumb.addEventListener('click', function () {
                if (!activeImage || !this.dataset.image) {
                    return;
                }

                activeImage.src = this.dataset.image;
                thumbs.forEach((item) => item.classList.remove('is-active'));
                this.classList.add('is-active');
            });
        });

        qtyButtons.forEach((button) => {
            button.addEventListener('click', function () {
                if (!quantityInput) {
                    return;
                }

                const currentValue = Number(quantityInput.value || 1);
                const min = Number(quantityInput.min || 1);
                const max = Number(quantityInput.max || currentValue);
                const nextValue = this.dataset.action === 'increase'
                    ? Math.min(max, currentValue + 1)
                    : Math.max(min, currentValue - 1);

                quantityInput.value = nextValue;
            });
        });

        function formatMoney(amount) {
            return '৳' + Math.round(Number(amount || 0)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        function syncVariantDetails() {
            if (!variantSelect || !quantityInput) {
                return;
            }

            const selectedOption = variantSelect.options[variantSelect.selectedIndex];

            if (!selectedOption) {
                return;
            }

            const current = Number(selectedOption.dataset.price || 0);
            const regular = Number(selectedOption.dataset.regularPrice || current);
            const stock = Number(selectedOption.dataset.stock || 0);
            const sku = selectedOption.dataset.sku || '';

            if (currentPrice) {
                currentPrice.textContent = formatMoney(current);
            }

            if (originalPrice) {
                if (current < regular) {
                    originalPrice.style.display = '';
                    originalPrice.textContent = formatMoney(regular);
                } else {
                    originalPrice.style.display = 'none';
                    originalPrice.textContent = '';
                }
            }

            if (offerBadge) {
                if (current < regular && regular > 0) {
                    offerBadge.style.display = '';
                    offerBadge.textContent = Math.round(((regular - current) / regular) * 100) + '% OFF';
                } else {
                    offerBadge.style.display = 'none';
                    offerBadge.textContent = '';
                }
            }

            if (priceMeta) {
                if (current < regular) {
                    priceMeta.innerHTML = '<span class="price-save">Save ' + formatMoney(regular - current) + '</span>';
                } else {
                    priceMeta.innerHTML = '<span class="price-note">Selected variant price</span>';
                }
            }

            if (availabilityValue) {
                availabilityValue.textContent = stock > 0 ? 'In stock' : 'Out of stock';
                availabilityValue.classList.toggle('text-success', stock > 0);
                availabilityValue.classList.toggle('text-danger', stock < 1);
            }

            if (stockBadge && stockBadgeText) {
                stockBadge.classList.toggle('in-stock', stock > 0);
                stockBadge.classList.toggle('out-of-stock', stock < 1);
                stockBadgeText.textContent = stock > 0 ? 'IN STOCK' : 'OUT OF STOCK';
            }

            if (skuValue) {
                skuValue.textContent = sku;
            }

            if (infoSku) {
                infoSku.textContent = sku;
            }

            if (infoStock) {
                infoStock.textContent = stock > 0 ? stock + ' units available' : 'Currently unavailable';
            }

            if (priceType) {
                priceType.textContent = current < regular ? 'Discounted' : 'Variant';
            }

            quantityInput.max = Math.max(1, stock);
            if (Number(quantityInput.value || 1) > Math.max(1, stock)) {
                quantityInput.value = Math.max(1, stock);
            }

            const disabled = stock < 1;
            quantityInput.disabled = disabled;

            qtyButtons.forEach((button) => {
                button.disabled = disabled;
            });

            submitButtons.forEach((button) => {
                button.disabled = disabled;
            });

            if (variantSelect) {
                const selectedOpt = variantSelect.options[variantSelect.selectedIndex];
                const label = selectedOpt ? (selectedOpt.dataset.label || selectedOpt.text) : '';
                const selectedVariantLabel = document.getElementById('selectedVariantLabel');
                if (selectedVariantLabel && label) {
                    selectedVariantLabel.textContent = label;
                }
            }
        }

        // Variant Visual Chip Buttons
        const chipButtons = document.querySelectorAll('.ref-chip-btn');
        chipButtons.forEach((chip) => {
            chip.addEventListener('click', function () {
                const variantId = this.dataset.variantId;
                chipButtons.forEach((btn) => btn.classList.remove('is-active'));
                this.classList.add('is-active');

                if (variantSelect) {
                    variantSelect.value = variantId;
                    variantSelect.dispatchEvent(new Event('change'));
                }
            });
        });

        // Prev / Next Image Navigation Arrow Buttons
        const prevArrowBtn = document.getElementById('refPrevImageBtn');
        const nextArrowBtn = document.getElementById('refNextImageBtn');
        if (thumbs.length > 1) {
            function navigateImage(direction) {
                const activeThumb = document.querySelector('.ref-thumb-item.is-active') || thumbs[0];
                let currentIndex = Array.from(thumbs).indexOf(activeThumb);
                if (currentIndex === -1) currentIndex = 0;

                let nextIndex = direction === 'next' ? currentIndex + 1 : currentIndex - 1;
                if (nextIndex >= thumbs.length) nextIndex = 0;
                if (nextIndex < 0) nextIndex = thumbs.length - 1;

                const targetThumb = thumbs[nextIndex];
                if (targetThumb) {
                    targetThumb.click();
                }
            }

            if (prevArrowBtn) {
                prevArrowBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    navigateImage('prev');
                });
            }

            if (nextArrowBtn) {
                nextArrowBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    navigateImage('next');
                });
            }
        }

        if (variantSelect) {
            variantSelect.addEventListener('change', syncVariantDetails);
            syncVariantDetails();
        }

        // Initial recalculate direct order totals on load
        recalculateDirectOrder();
    });

    // Gallery toggle function
    function toggleGallerySection() {
        const grid = document.getElementById('realGalleryGrid');
        const btn = document.getElementById('galleryToggleBtn');
        if (!grid || !btn) return;
        if (grid.style.display === 'none') {
            grid.style.display = 'block';
            btn.innerHTML = '<i class="fas fa-times me-1"></i> Hide Photos';
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-outline-secondary');
        } else {
            grid.style.display = 'none';
            btn.innerHTML = '<i class="fas fa-images me-1"></i> View All Photos';
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-outline-primary');
        }
    }

    // Set main product image from gallery click
    function setMainImage(src) {
        const activeImage = document.getElementById('activeProductImage');
        if (activeImage && src) {
            activeImage.src = src;
            // Also update active thumb state
            document.querySelectorAll('.product-thumb').forEach(function(t) {
                t.classList.remove('is-active');
                if (t.dataset.image === src) t.classList.add('is-active');
            });
            // Scroll to top of product image
            const gallery = document.querySelector('.product-gallery-shell');
            if (gallery) gallery.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
</script>
@endpush
