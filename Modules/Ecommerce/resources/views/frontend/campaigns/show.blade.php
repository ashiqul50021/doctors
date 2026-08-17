@extends('layouts.app')

@section('title', $campaign->title . ' - Flash Deals')

@push('styles')
@include('ecommerce::components.skeletons.styles')
<style>
    .campaign-page-wrap {
        background: #f8fafc;
        min-height: 80vh;
        padding-bottom: 60px;
    }

    .campaign-hero-banner {
        position: relative;
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #31104b 100%);
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 30px;
        color: #ffffff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.1);
    }

    .campaign-banner-bg-img {
        width: 100%;
        max-height: 320px;
        object-fit: cover;
        opacity: 0.35;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
    }

    .campaign-hero-content {
        position: relative;
        z-index: 2;
        padding: 40px 30px;
    }

    .campaign-tag-badge {
        background: #ef4444;
        color: #ffffff;
        font-size: 12px;
        font-weight: 800;
        padding: 5px 14px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 12px;
    }

    .campaign-main-title {
        font-size: 32px;
        font-weight: 900;
        margin-bottom: 10px;
        letter-spacing: -0.5px;
    }

    .campaign-desc-lead {
        color: #cbd5e1;
        font-size: 15px;
        max-width: 600px;
        margin-bottom: 25px;
    }

    /* Live Countdown Box */
    .countdown-widget-box {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 12px 20px;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .countdown-cell {
        background: #ffffff;
        color: #0f172a;
        padding: 8px 12px;
        border-radius: 10px;
        text-align: center;
        min-width: 52px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .countdown-cell-num {
        font-size: 20px;
        font-weight: 900;
        line-height: 1;
        display: block;
        color: #ef4444;
    }

    .countdown-cell-lbl {
        font-size: 10px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-top: 2px;
        display: block;
    }

    .countdown-colon {
        font-size: 22px;
        font-weight: 900;
        color: #ffffff;
    }

    /* Product Card */
    .campaign-product-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
    }

    .campaign-product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 14px 30px rgba(239, 68, 68, 0.12);
        border-color: #fca5a5;
    }

    .camp-card-img-wrap {
        position: relative;
        background: #f8fafc;
        padding: 16px;
        text-align: center;
        aspect-ratio: 1 / 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .camp-card-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .campaign-product-card:hover .camp-card-img {
        transform: scale(1.05);
    }

    .camp-badge-sale {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #ef4444;
        color: #ffffff;
        font-size: 11px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 6px;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
    }

    .camp-card-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .camp-card-cat {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 4px;
    }

    .camp-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.4;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-decoration: none;
    }

    .camp-card-title a {
        color: inherit;
        text-decoration: none;
    }

    .camp-card-title a:hover {
        color: #ef4444;
    }

    .camp-card-footer {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
    }

    .camp-price-cur {
        font-size: 18px;
        font-weight: 900;
        color: #ef4444;
    }

    .camp-price-old {
        font-size: 13px;
        color: #94a3b8;
        text-decoration: line-through;
        margin-left: 4px;
    }

    .btn-camp-buy {
        background: #fee2e2;
        color: #ef4444;
        border: 1px solid #fecaca;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-camp-buy:hover {
        background: #ef4444;
        color: #ffffff;
        border-color: #ef4444;
    }

    @media (max-width: 768px) {
        .campaign-hero-content {
            padding: 25px 20px;
        }

        .campaign-main-title {
            font-size: 24px;
        }

        .countdown-cell {
            padding: 6px 8px;
            min-width: 44px;
        }

        .countdown-cell-num {
            font-size: 16px;
        }
    }
</style>
@endpush

@section('content')
<div class="campaign-page-wrap">
    <div class="container pt-4">
        <!-- Campaign Hero Banner with Live Countdown -->
        <div class="campaign-hero-banner">
            @if($campaign->banner_image)
                <img src="{{ asset($campaign->banner_image) }}" alt="{{ $campaign->title }}" class="campaign-banner-bg-img">
            @endif

            <div class="campaign-hero-content">
                <span class="campaign-tag-badge">
                    <i class="fas fa-bolt"></i> Flash Deal Campaign
                </span>

                <h1 class="campaign-main-title">{{ $campaign->title }}</h1>
                <p class="campaign-desc-lead">{{ $campaign->description ?: 'Exclusive mega discount offers for a limited period. Grab yours before stocks run out!' }}</p>

                <!-- Live Countdown -->
                <div class="countdown-widget-box">
                    <div class="countdown-cell">
                        <span class="countdown-cell-num" id="campDays">00</span>
                        <span class="countdown-cell-lbl">Days</span>
                    </div>
                    <span class="countdown-colon">:</span>
                    <div class="countdown-cell">
                        <span class="countdown-cell-num" id="campHours">00</span>
                        <span class="countdown-cell-lbl">Hours</span>
                    </div>
                    <span class="countdown-colon">:</span>
                    <div class="countdown-cell">
                        <span class="countdown-cell-num" id="campMins">00</span>
                        <span class="countdown-cell-lbl">Mins</span>
                    </div>
                    <span class="countdown-colon">:</span>
                    <div class="countdown-cell">
                        <span class="countdown-cell-num" id="campSecs">00</span>
                        <span class="countdown-cell-lbl">Secs</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Controls -->
        <div class="card p-3 border-0 shadow-sm mb-4 rounded-3">
            <div class="row g-3 align-items-center">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-2 overflow-auto">
                        <a href="{{ route('ecommerce.campaigns.show', $campaign->slug) }}" 
                           class="btn btn-sm {{ !request('category') ? 'btn-primary' : 'btn-light' }} rounded-pill px-3">
                            All Items ({{ $campaign->products->count() }})
                        </a>
                        @foreach($campaignCategories as $cat)
                            <a href="{{ route('ecommerce.campaigns.show', ['slug' => $campaign->slug, 'category' => $cat->id]) }}" 
                               class="btn btn-sm {{ request('category') == $cat->id ? 'btn-primary' : 'btn-light' }} rounded-pill px-3 text-nowrap">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-6">
                    <form method="GET" action="{{ route('ecommerce.campaigns.show', $campaign->slug) }}" class="d-flex align-items-center gap-2 justify-content-lg-end">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        
                        <div class="input-group" style="max-width: 260px;">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search campaign items..." value="{{ request('search') }}">
                            <button class="btn btn-primary btn-sm" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                        <select name="sort" class="form-select form-select-sm" style="max-width: 160px;" onchange="this.form.submit()">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popular</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row g-3">
            @forelse($products as $product)
                @php
                    $customPrice = $product->pivot->campaign_price ? (float)$product->pivot->campaign_price : null;
                    $campaignPrice = $campaign->calculateCampaignPrice((float)$product->price, $customPrice);
                    $regularPrice = (float)$product->price;
                    $discountPct = ($regularPrice > $campaignPrice && $regularPrice > 0)
                        ? round((($regularPrice - $campaignPrice) / $regularPrice) * 100)
                        : 0;

                    $image = $product->image;
                    if (!$image && !empty($product->gallery) && is_array($product->gallery)) {
                        $image = $product->gallery[0] ?? null;
                    }
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="campaign-product-card">
                        <div class="camp-card-img-wrap">
                            <a href="{{ route('ecommerce.products.show', $product->id) }}">
                                <img src="{{ $image ? asset($image) : asset('assets/img/products/default-product.png') }}" 
                                     alt="{{ $product->name }}" class="camp-card-img">
                            </a>

                            @if($discountPct > 0)
                                <span class="camp-badge-sale">-{{ $discountPct }}% OFF</span>
                            @endif
                        </div>

                        <div class="camp-card-body">
                            <span class="camp-card-cat">{{ $product->category->name ?? 'Healthcare' }}</span>
                            <h3 class="camp-card-title">
                                <a href="{{ route('ecommerce.products.show', $product->id) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>

                            <div class="camp-card-footer">
                                <div>
                                    <span class="camp-price-cur">৳{{ number_format($campaignPrice, 0) }}</span>
                                    @if($campaignPrice < $regularPrice)
                                        <span class="camp-price-old">৳{{ number_format($regularPrice, 0) }}</span>
                                    @endif
                                </div>

                                <a href="{{ route('ecommerce.products.show', $product->id) }}" class="btn-camp-buy">
                                    <i class="fas fa-shopping-bag me-1"></i> Buy
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card p-5 text-center border-0 shadow-sm rounded-4">
                        <i class="fas fa-box-open text-muted mb-3" style="font-size: 48px;"></i>
                        <h4 class="fw-bold mb-2">No Campaign Products Found</h4>
                        <p class="text-muted">Check back soon for new discounts and promotions.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const endTime = new Date("{{ $campaign->end_date->toIso8601String() }}").getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                document.getElementById('campDays').innerText = '00';
                document.getElementById('campHours').innerText = '00';
                document.getElementById('campMins').innerText = '00';
                document.getElementById('campSecs').innerText = '00';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('campDays').innerText = String(days).padStart(2, '0');
            document.getElementById('campHours').innerText = String(hours).padStart(2, '0');
            document.getElementById('campMins').innerText = String(minutes).padStart(2, '0');
            document.getElementById('campSecs').innerText = String(seconds).padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
</script>
@endpush
@endsection
