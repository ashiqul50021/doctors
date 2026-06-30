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
    $countdownTitle = $landingSettings['countdown_title'] ?? 'আজকের বিশেষ ছাড় অফার!';
    $countdownSubtitle = $landingSettings['countdown_subtitle'] ?? 'অফারটি শেষ হতে আর মাত্র সময় বাকি আছে:';
    $countdownHours = (int) ($landingSettings['countdown_hours'] ?? 3);

    $trustTitle = $landingSettings['trust_title'] ?? 'আমাদের থেকে কেন সংগ্রহ করবেন?';

    // 4 Badges
    $badges = [];
    for ($i = 1; $i <= 4; $i++) {
        $defaultIcons = [1 => 'fas fa-undo-alt', 2 => 'fas fa-hand-holding-usd', 3 => 'fas fa-headset', 4 => 'fas fa-shipping-fast'];
        $defaultTitles = [1 => '৭ দিনের রিটার্ন', 2 => 'হাতে পেয়ে পেমেন্ট', 3 => 'অনলাইন সাপোর্ট', 4 => 'সারাদেশে ডেলিভারি'];
        $defaultDescs = [1 => 'সহজ এক্সচেঞ্জ সুবিধা', 2 => 'ক্যাশ অন ডেলিভারি', 3 => '২৪/৭ কাস্টমার কেয়ার', 4 => 'দ্রুত ও নিরাপদ ডেলিভারি'];

        $badges[$i] = [
            'icon' => $landingSettings["badge_{$i}_icon"] ?? $defaultIcons[$i],
            'title' => $landingSettings["badge_{$i}_title"] ?? $defaultTitles[$i],
            'desc' => $landingSettings["badge_{$i}_desc"] ?? $defaultDescs[$i],
        ];
    }

    // 4 Trust Features
    $trustFeatures = [];
    for ($i = 1; $i <= 4; $i++) {
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

        $trustFeatures[$i] = [
            'title' => $landingSettings["feature_{$i}_title"] ?? $defaultFeatureTitles[$i],
            'desc' => $landingSettings["feature_{$i}_desc"] ?? $defaultFeatureDescs[$i],
        ];
    }
@endphp

<div class="content product-single-page">
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
            <a href="{{ route('home') }}">Home</a>
            <span><i class="fas fa-angle-right"></i></span>
            <a href="{{ route('ecommerce.products') }}">Products</a>
            <span><i class="fas fa-angle-right"></i></span>
            <a href="{{ route('ecommerce.products', ['category' => $product->product_category_id]) }}">
                {{ $product->category->name ?? 'General' }}
            </a>
            <span><i class="fas fa-angle-right"></i></span>
            <span>{{ $product->name }}</span>
        </nav>

        <!-- Urgency Countdown & Pricing Banner -->
        <div class="landing-urgency-banner mb-4">
            <div class="row align-items-center g-3">
                <div class="col-md-7 text-center text-md-start">
                    <span class="offer-badge-pill">সীমিত সময়ের অফার! (Limited Time Offer)</span>
                    <h3 class="mb-1 text-white fw-bold">{{ $countdownTitle }}</h3>
                    <p class="text-white-50 mb-0">{{ $countdownSubtitle }}</p>
                </div>
                <div class="col-md-5 d-flex justify-content-center justify-content-md-end">
                    <div class="countdown-timer-wrapper" id="landingCountdown">
                        <div class="time-block">
                            <span class="time-val" id="timer-hours">02</span>
                            <span class="time-lbl">Hours</span>
                        </div>
                        <div class="time-block">
                            <span class="time-val" id="timer-minutes">45</span>
                            <span class="time-lbl">Min</span>
                        </div>
                        <div class="time-block">
                            <span class="time-val" id="timer-seconds">30</span>
                            <span class="time-lbl">Sec</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="product-hero-card">
            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <div class="product-gallery-shell product-card-modern">
                        <div id="productStockBadge" class="stock-badge {{ $stockQty > 0 ? 'in-stock' : 'out-of-stock' }}">
                            <span id="productStockBadgeText">
                            {{ $stockQty > 0 ? 'IN STOCK' : 'OUT OF STOCK' }}
                            </span>
                        </div>

                        <div id="productOfferBadge" class="detail-offer-badge" style="{{ $discountPercentage > 0 ? '' : 'display:none;' }}">
                            {{ $discountPercentage }}% OFF
                        </div>

                        <div class="product-image-container product-image-main detail-main-image product-zoom-frame">
                            <img id="activeProductImage"
                                src="{{ $mainImage }}"
                                class="product-main-img"
                                alt="{{ $product->name }}"
                                onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                        </div>

                        @if($galleryImages->isNotEmpty())
                            <div class="product-thumb-strip">
                                @foreach($galleryImages as $image)
                                    <button type="button"
                                        class="product-thumb {{ $loop->first ? 'is-active' : '' }}"
                                        data-image="{{ $image }}"
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

                <div class="col-lg-5">
                    <aside class="product-summary-card">
                        <div class="product-rating product-rating-large">
                            <i class="fas fa-star"></i>
                            <span class="rating-value">{{ number_format($averageRating, 1) }}</span>
                            <span class="review-count">({{ $reviewCount }} reviews)</span>
                        </div>

                        <div class="product-brand">{{ $brandName }}</div>

                        <div class="summary-head">
                            <h1>{{ $product->name }}</h1>
                            <p class="summary-copy">{{ $summaryCopy }}</p>
                        </div>

                        <div class="summary-price-box">
                            <div class="product-price-tag">
                                <span id="productCurrentPrice" class="price-current">৳{{ number_format($displayPrice, 0) }}</span>
                                <span id="productOriginalPrice" class="price-original" style="{{ $displayPrice < $regularPrice ? '' : 'display:none;' }}">
                                    ৳{{ number_format($regularPrice, 0) }}
                                </span>
                            </div>

                            <div id="productPriceMeta" class="price-meta">
                                @if($displayPrice < $regularPrice)
                                    <span class="price-save">Save ৳{{ number_format($discountAmount, 0) }}</span>
                                @elseif($hasVariants)
                                    <span class="price-note">Selected variant price</span>
                                @else
                                    <span class="price-note">Standard listed price</span>
                                @endif
                            </div>
                        </div>

                        <div class="summary-status-grid">
                            <div class="status-card">
                                <span class="status-label">Availability</span>
                                <strong id="productAvailabilityValue" class="{{ $stockQty > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $stockQty > 0 ? 'In stock' : 'Out of stock' }}
                                </strong>
                            </div>
                            <div class="status-card">
                                <span class="status-label">SKU</span>
                                <strong id="productSkuValue">{{ $sku }}</strong>
                            </div>
                            <div class="status-card">
                                <span class="status-label">Category</span>
                                <strong>{{ $product->category->name ?? 'General' }}</strong>
                            </div>
                            <div class="status-card">
                                <span class="status-label">Price Type</span>
                                <strong id="productPriceType">{{ $displayPrice < $regularPrice ? 'Discounted' : ($hasVariants ? 'Variant' : 'Regular') }}</strong>
                            </div>
                        </div>

                        @if($summaryPoints->isNotEmpty())
                            <ul class="summary-feature-list">
                                @foreach($summaryPoints as $point)
                                    <li><i class="fas fa-check"></i> {{ $point }}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if($tagList->isNotEmpty())
                            <div class="product-tag-list">
                                @foreach($tagList->take(6) as $tag)
                                    <span>{{ ucfirst($tag) }}</span>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('ecommerce.cart.add') }}" method="POST" class="purchase-form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="purchase-controls">
                                @if($hasVariants)
                                    <div class="variant-box">
                                        <label for="productVariant">Choose Variant</label>
                                        <select id="productVariant" name="variant_id" class="variant-select" required>
                                            @foreach($activeVariants as $variant)
                                                <option value="{{ $variant->id }}"
                                                    data-price="{{ $variant->currentPrice() }}"
                                                    data-regular-price="{{ $variant->regularPrice() }}"
                                                    data-stock="{{ $variant->stock }}"
                                                    data-sku="{{ $variant->sku ?: ($product->sku ?: strtoupper($product->slug ?: ('PRO-' . $product->id))) }}"
                                                    data-label="{{ $variant->display_label }}"
                                                    {{ $selectedVariant && $selectedVariant->id === $variant->id ? 'selected' : '' }}>
                                                    {{ $variant->display_label }} | ৳{{ number_format($variant->currentPrice(), 0) }} | Stock: {{ $variant->stock }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="quantity-box">
                                    <label for="productQuantity">Quantity</label>
                                    <div class="quantity-control">
                                        <button type="button" class="qty-btn" data-action="decrease" {{ $stockQty < 1 ? 'disabled' : '' }}>-</button>
                                        <input id="productQuantity"
                                            type="number"
                                            name="quantity"
                                            class="qty-input"
                                            value="1"
                                            min="1"
                                            max="{{ max(1, $stockQty) }}"
                                            {{ $stockQty < 1 ? 'disabled' : '' }}>
                                        <button type="button" class="qty-btn" data-action="increase" {{ $stockQty < 1 ? 'disabled' : '' }}>+</button>
                                    </div>
                                </div>

                                <div class="support-note">
                                    <i class="fas fa-phone-alt"></i>
                                    <span>Need help with prescription or bulk order quantity? Contact support before checkout.</span>
                                </div>
                            </div>

                            <div class="product-footer detail-product-footer">
                                <div class="btn-group-modern detail-btn-group">
                                    <button type="submit" class="btn-cart-modern detail-cart-btn" title="Add to Cart" {{ $stockQty < 1 ? 'disabled' : '' }}>
                                        <i class="fas fa-shopping-cart"></i>
                                        <span>Add to Cart</span>
                                    </button>
                                    <button type="submit" name="buy_now" value="1" class="btn-buy-modern detail-buy-btn" {{ $stockQty < 1 ? 'disabled' : '' }}>
                                        Buy Now
                                    </button>
                                </div>
                            </div>
                            
                            @if($stockQty > 0)
                                <div class="mt-3">
                                    <a href="#direct-checkout-section" class="btn btn-landing-cta btn-landing-cta-pulsing w-100 text-center text-white d-block py-3 text-decoration-none">
                                        <i class="fas fa-shopping-bag me-2"></i> সরাসরি অর্ডার করতে এখানে ক্লিক করুন
                                    </a>
                                </div>
                            @endif
                        </form>

                        <a href="{{ route('ecommerce.cart') }}" class="detail-view-cart">
                            <i class="fas fa-eye"></i>
                            <span>View Cart</span>
                        </a>

                        <div class="summary-assurance">
                            <div class="assurance-item">
                                <i class="fas fa-wallet"></i>
                                <span>Cash on delivery available</span>
                            </div>
                            <div class="assurance-item">
                                <i class="fas fa-undo"></i>
                                <span>Easy replacement on eligible issues</span>
                            </div>
                            <div class="assurance-item">
                                <i class="fas fa-lock"></i>
                                <span>Secure checkout experience</span>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>

        <!-- Trust Highlights Grid -->
        <div class="row g-3 mb-4 mt-2">
            @foreach($badges as $badge)
                <div class="col-6 col-md-3">
                    <div class="landing-badge-card">
                        <i class="{{ $badge['icon'] }} text-primary d-block mb-2 fs-3"></i>
                        <h5 class="fw-bold mb-1">{{ $badge['title'] }}</h5>
                        <p class="text-muted small mb-0">{{ $badge['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        @if($stockQty > 0)
            <div class="text-center my-4">
                <a href="#direct-checkout-section" class="btn btn-landing-cta px-5 py-3 fs-5 text-decoration-none">
                    <i class="fas fa-shopping-cart me-2"></i> ঝটপট অর্ডার করতে এখানে ক্লিক করুন
                </a>
            </div>
        @endif

        <section class="product-detail-sections">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="info-card">
                        <div class="section-heading">
                            <span class="section-tag">Product Details</span>
                            <h2>About this product</h2>
                        </div>

                        <div class="info-copy">{!! $detailDescription !!}</div>

                        <div class="info-chip-list">
                            <span>{{ $brandName }}</span>
                            <span>{{ $product->category->name ?? 'General' }}</span>
                            <span id="productInfoSku">{{ $sku }}</span>
                            <span id="productInfoStock">{{ $stockQty > 0 ? $stockQty . ' units available' : 'Currently unavailable' }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="info-card info-side-card">
                        <div class="section-heading">
                            <span class="section-tag">Order Support</span>
                            <h2>Before you checkout</h2>
                        </div>

                        <ul class="summary-feature-list compact-feature-list">
                            <li><i class="fas fa-check"></i> Adjust quantity before adding the item to your cart.</li>
                            <li><i class="fas fa-check"></i> Use Buy Now for a direct checkout flow.</li>
                            <li><i class="fas fa-check"></i> Browse related products below for similar options.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us / কেন আমাদের থেকে অর্ডার করবেন? -->
        <section class="landing-trust-section mb-4">
            <div class="info-card p-4 text-center rounded-3" style="background: #f0f7ff; border: 1px solid #dbeafe;">
                <h3 class="text-primary mb-4 fw-bold">{{ $trustTitle }}</h3>
                <div class="row g-4 text-start">
                    @foreach($trustFeatures as $feature)
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <span class="text-primary fs-5"><i class="fas fa-check-circle"></i></span>
                                <div>
                                    <strong class="text-dark d-block mb-1">{{ $feature['title'] }}</strong>
                                    <span class="text-muted small">{{ $feature['desc'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Product Real Gallery showcase -->
        @if($galleryImages->isNotEmpty())
            <section class="landing-gallery-section my-5">
                <div class="section-heading text-center mb-4">
                    <span class="section-tag">Showcase</span>
                    <h3 class="fw-bold">পণ্যটির কিছু বাস্তব ছবি (Real Gallery)</h3>
                </div>
                <div class="row g-3 justify-content-center">
                    @foreach($galleryImages as $image)
                        <div class="col-md-6 col-lg-4">
                            <div class="gallery-image-wrapper card border-0 shadow-sm overflow-hidden p-2 bg-white">
                                <img src="{{ $image }}" class="img-fluid rounded" alt="{{ $product->name }} gallery {{ $loop->iteration }}" style="height: 250px; object-fit: contain; background: #f8fafc;">
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if($stockQty > 0)
            <div class="text-center my-4">
                <a href="#direct-checkout-section" class="btn btn-landing-cta px-5 py-3 fs-5 text-decoration-none">
                    <i class="fas fa-shopping-cart me-2"></i> সরাসরি অর্ডার করতে এখানে ক্লিক করুন
                </a>
            </div>
        @endif

        <section class="product-reviews-section">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="info-card review-form-card">
                        <div class="section-heading">
                            <span class="section-tag">Customer Reviews</span>
                            <h2>Share your experience</h2>
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

                        @auth
                            @if(auth()->user()->role === 'patient')
                                <form action="{{ route('ecommerce.products.reviews.store', $product->id) }}" method="POST" class="review-form">
                                    @csrf
                                    <div class="form-group">
                                        <label>Your rating</label>
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
                                        <label for="reviewTitle">Review title</label>
                                        <input id="reviewTitle"
                                            type="text"
                                            name="title"
                                            class="form-control"
                                            value="{{ old('title', $currentUserReview->title ?? '') }}"
                                            placeholder="Short summary (optional)">
                                    </div>

                                    <div class="form-group">
                                        <label for="reviewComment">Your review</label>
                                        <textarea id="reviewComment"
                                            name="comment"
                                            class="form-control"
                                            rows="5"
                                            required
                                            placeholder="Write what was helpful, quality, delivery, or usage experience...">{{ old('comment', $currentUserReview->comment ?? '') }}</textarea>
                                    </div>

                                    <button type="submit" class="btn-buy-modern review-submit-btn">
                                        {{ $currentUserReview ? 'Update Review' : 'Submit Review' }}
                                    </button>
                                </form>
                            @else
                                <div class="review-access-card">
                                    <strong>Patient account required</strong>
                                    <span>Only patient accounts can write product reviews.</span>
                                </div>
                            @endif
                        @else
                            <div class="review-access-card">
                                <strong>Login to write a review</strong>
                                <span>Sign in with a patient account to share your product experience.</span>
                                <a href="{{ route('login') }}" class="btn-view-all-arrow">Login</a>
                            </div>
                        @endauth
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="info-card review-list-card">
                        <div class="section-heading">
                            <span class="section-tag">Feedback</span>
                            <h2>What customers say</h2>
                        </div>

                        <div class="review-list">
                            @forelse($productReviews as $review)
                                @php
                                    $reviewerName = $review->patient?->user?->name ?? 'Patient';
                                    $reviewerInitial = strtoupper(substr($reviewerName, 0, 1));
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
                                            <h3>{{ $review->title }}</h3>
                                        @endif

                                        <p>{{ $review->comment }}</p>
                                    </div>
                                </article>
                            @empty
                                <div class="review-empty-state">
                                    <i class="far fa-star"></i>
                                    <strong>No reviews yet</strong>
                                    <span>Be the first patient to review this product.</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>

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
                                    <a href="{{ route('ecommerce.products.show', $relProduct->id) }}" class="product-image-link">
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
                                        <a href="{{ route('ecommerce.products.show', $relProduct->id) }}">{{ $relProduct->name }}</a>
                                    </h3>

                                    <div class="product-footer">
                                        <div class="product-price-tag">
                                            <span class="price-current">৳{{ number_format($relatedPrice, 0) }}</span>
                                            @if($relatedPrice < $relatedRegularPrice)
                                                <span class="price-original">৳{{ number_format($relatedRegularPrice, 0) }}</span>
                                            @endif
                                        </div>

                                        @if($relatedHasVariants)
                                            <a href="{{ route('ecommerce.products.show', $relProduct->id) }}" class="btn-buy-modern btn-link-modern">
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

        @if($stockQty > 0)
            <!-- Embedded Direct Checkout Form -->
            <section id="direct-checkout-section" class="direct-checkout-section my-5">
                <div class="card checkout-card border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                    <div class="checkout-card-header text-white py-3 px-4" style="background: linear-gradient(135deg, var(--primary-blue-dark) 0%, var(--primary-blue) 100%) !important;">
                        <h4 class="mb-0 text-white fw-bold"><i class="fas fa-shopping-cart me-2"></i> সরাসরি অর্ডার করতে নিচের ফর্মটি পূরণ করুন</h4>
                    </div>
                    <div class="checkout-card-body p-4">
                        <form action="{{ route('ecommerce.order.place') }}" method="POST" id="landingDirectCheckoutForm">
                            @csrf
                            <input type="hidden" name="direct_order" value="1">
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="variant_id" id="directFormVariantId" value="{{ $selectedVariant?->id }}">
                            <input type="hidden" name="quantity" id="directFormQuantity" value="1">
                            <input type="hidden" name="shipping_charge" id="directFormShippingCharge" value="80">

                            <div class="row g-4">
                                <!-- Shipping Info Form (Left) -->
                                <div class="col-lg-7">
                                    <h4 class="mb-3 border-bottom pb-2 fw-bold" style="color: var(--primary-blue) !important; border-bottom-color: #dbeafe !important;">শিপিংয়ের তথ্য (Billing/Shipping)</h4>
                                    
                                    <div class="form-group mb-3 text-start">
                                        <label class="form-label fw-bold">আপনার নাম (Full Name) <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control form-control-lg shadow-none" placeholder="এখানে আপনার নাম লিখুন" required value="{{ old('name', Auth::user()->name ?? '') }}">
                                    </div>

                                    <div class="form-group mb-3 text-start">
                                        <label class="form-label fw-bold">মোবাইল নাম্বার (Phone Number) <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" id="directFormPhone" class="form-control form-control-lg shadow-none" placeholder="১১ ডিজিটের সচল মোবাইল নাম্বার" required value="{{ old('phone', Auth::user()->patient->phone ?? '') }}">
                                    </div>

                                    <div class="form-group mb-3 text-start">
                                        <label class="form-label fw-bold">ইমেইল এড্রেস (Email Address) <span class="text-muted">(ঐচ্ছিক - Optional)</span></label>
                                        <input type="email" name="email" id="directFormEmail" class="form-control form-control-lg shadow-none" placeholder="যেমন: name@domain.com" value="{{ old('email', Auth::user()->email ?? '') }}">
                                    </div>

                                    <div class="form-group mb-3 text-start">
                                        <label class="form-label fw-bold">সম্পূর্ণ ঠিকানা (Full Shipping Address) <span class="text-danger">*</span></label>
                                        <textarea name="address" class="form-control form-control-lg shadow-none" rows="3" placeholder="জেলা, থানা, গ্রাম/রোড এবং বাসা নাম্বার উল্লেখ করুন" required>{{ old('address', Auth::user()->patient->address ?? '') }}</textarea>
                                    </div>

                                    <div class="form-group mb-3 text-start">
                                        <label class="form-label fw-bold d-block">ডেলিভারি এরিয়া নির্বাচন করুন (Delivery Area) <span class="text-danger">*</span></label>
                                        <div class="delivery-options-grid mt-2">
                                            <label class="delivery-radio-card active">
                                                <input type="radio" name="delivery_area" value="inside" checked>
                                                <div class="delivery-radio-content">
                                                    <span class="delivery-title">ঢাকার ভিতরে (Inside Dhaka)</span>
                                                    <span class="delivery-price">৳৮০</span>
                                                </div>
                                            </label>
                                            <label class="delivery-radio-card">
                                                <input type="radio" name="delivery_area" value="outside">
                                                <div class="delivery-radio-content">
                                                    <span class="delivery-title">ঢাকার বাইরে (Outside Dhaka)</span>
                                                    <span class="delivery-price">৳১৫০</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3 text-start">
                                        <label class="form-label fw-bold">অর্ডার নোট (Order Notes - Optional)</label>
                                        <textarea name="notes" class="form-control shadow-none" rows="2" placeholder="অর্ডার সংক্রান্ত কোনো নির্দেশনা থাকলে লিখতে পারেন"></textarea>
                                    </div>
                                </div>

                                <!-- Order Summary (Right) -->
                                <div class="col-lg-5">
                                    <div class="summary-wrapper p-4 rounded-3 bg-light border border-2 text-start" style="border-color: #dbeafe !important;">
                                        <h4 class="mb-3 border-bottom pb-2 fw-bold" style="color: var(--primary-blue) !important; border-bottom-color: #dbeafe !important;">আপনার অর্ডার (Your Order)</h4>
                                        
                                        <!-- Product Row -->
                                        <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                                            <div class="direct-checkout-thumb rounded border overflow-hidden">
                                                <img id="directFormProductThumb" src="{{ $mainImage }}" alt="{{ $product->name }}" class="img-fluid">
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold text-dark" style="font-size: 15px;">{{ $product->name }}</h6>
                                                @if($hasVariants)
                                                    <span class="badge bg-secondary mb-1" id="directFormVariantLabel" style="font-size: 11px;">{{ $selectedVariant?->display_label }}</span>
                                                @endif
                                                
                                                <div class="qty-changer-widget d-flex align-items-center gap-2 mt-1">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 fw-bold" id="directQtyDec">-</button>
                                                    <span class="fw-bold" id="directQtyDisplay">1</span>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 fw-bold" id="directQtyInc">+</button>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <strong class="fs-5" id="directFormPriceDisplay" style="color: var(--primary-blue) !important;">৳{{ number_format($displayPrice, 0) }}</strong>
                                            </div>
                                        </div>

                                        <!-- Live Calculations -->
                                        <div class="calculation-rows">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">সাবটোটাল (Subtotal)</span>
                                                <strong id="directSubtotal" class="text-dark">৳{{ number_format($displayPrice, 0) }}</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">ডেলিভারি চার্জ (Delivery)</span>
                                                <strong id="directShipping" class="text-dark">৳৮০</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 text-danger" id="directDiscountRow" style="display: none !important;">
                                                <span class="text-muted">ডিসকাউন্ট (Discount) <small class="text-muted" id="directCouponCodeDisplay"></small></span>
                                                <strong class="text-danger">-৳<span id="directDiscount">0</span></strong>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between mb-4">
                                                <span class="fs-5 fw-bold text-dark">সর্বমোট (Total)</span>
                                                <strong class="fs-4 fw-bold" id="directTotal" style="color: var(--primary-blue) !important;">৳{{ number_format($displayPrice + 80, 0) }}</strong>
                                            </div>
                                        </div>

                                        <!-- Coupon Code -->
                                        <div class="coupon-widget mb-3">
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control shadow-none" id="directCouponInput" placeholder="কুপন কোড লিখুন (Coupon)">
                                                <button class="btn btn-dark" type="button" id="applyDirectCouponBtn">Apply</button>
                                            </div>
                                            <div id="directCouponMessage" class="small mt-1" style="display: none;"></div>
                                            <input type="hidden" name="coupon_code" id="directAppliedCouponCode" value="">
                                        </div>

                                        <!-- Payment Assurance -->
                                        <div class="payment-method-badge p-3 bg-white border rounded-3 mb-4 text-center">
                                            <i class="fas fa-truck text-success fs-4 mb-2"></i>
                                            <h6 class="mb-1 fw-bold text-success" style="font-size: 15px;">ক্যাশ অন ডেলিভারি (Cash on Delivery)</h6>
                                            <p class="small text-muted mb-0" style="font-size: 12px;">পণ্য হাতে পেয়ে টাকা পরিশোধ করুন। কোনো অগ্রিম পেমেন্ট লাগবেনা।</p>
                                        </div>

                                        <!-- Place Order Button -->
                                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold py-3 fs-5 text-white shadow-sm hover-grow" id="directSubmitBtn" style="background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-light) 100%); border: none;">
                                            <i class="fas fa-check-circle me-2"></i> অর্ডার কনফার্ম করুন (Confirm Order)
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        @else
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
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-light) 100%) !important;
        color: #fff !important;
        border: none !important;
        box-shadow: 0 8px 24px rgba(29, 78, 216, 0.25) !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        animation: landing-pulse 2s infinite;
        padding: 14px 28px !important;
        border-radius: 12px !important;
    }

    .btn-landing-cta:hover {
        background: linear-gradient(135deg, var(--primary-blue-dark) 0%, var(--primary-blue) 100%) !important;
        box-shadow: 0 12px 30px rgba(29, 78, 216, 0.45) !important;
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
        background: linear-gradient(180deg, #f6faff 0%, #ffffff 42%, #f8fbff 100%);
        padding: 26px 0 70px;
    }

    .product-breadcrumb {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 18px;
        color: #64748b;
        font-size: 13px;
    }

    .product-breadcrumb a {
        color: #1d4ed8;
        text-decoration: none;
        font-weight: 500;
    }

    .product-hero-card {
        margin-bottom: 28px;
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
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
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
        let directShippingCharge = 80;
        let directDiscount = 0;
        let directCouponType = null;
        let directCouponAmount = 0;

        // Smooth scroll for landing CTA buttons
        document.querySelectorAll('.btn-landing-cta').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.getElementById('direct-checkout-section');
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Looping countdown timer
        function startLandingCountdown() {
            const timerKey = 'landing_countdown_target_time_product_{{ $product->id }}';
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
                    directShippingCharge = 150;
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

            // Update quantity max limit based on variant stock
            const stock = Number(selectedOption.dataset.stock || 0);
            if (quantityInput) {
                quantityInput.max = Math.max(1, stock);
            }

            updateDirectQuantity(directQuantity);
        }

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
        }

        if (variantSelect) {
            variantSelect.addEventListener('change', syncVariantDetails);
            syncVariantDetails();
        }
    });
</script>
@endpush
