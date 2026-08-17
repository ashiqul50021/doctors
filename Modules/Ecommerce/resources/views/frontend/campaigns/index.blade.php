@extends('layouts.app')

@section('title', 'Special Campaigns & Flash Deals - abcsheba.com')

@push('styles')
<style>
    .campaigns-page-wrap {
        background: #f8fafc;
        min-height: 80vh;
        padding: 40px 0 60px;
    }

    .campaign-card-item {
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .campaign-card-item:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 36px rgba(37, 99, 235, 0.1);
        border-color: #cbd5e1;
    }

    .campaign-banner-cover {
        position: relative;
        height: 180px;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        overflow: hidden;
    }

    .campaign-banner-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.9;
        transition: transform 0.4s ease;
    }

    .campaign-card-item:hover .campaign-banner-img {
        transform: scale(1.05);
    }

    .campaign-badge-floating {
        position: absolute;
        top: 14px;
        right: 14px;
        background: #ef4444;
        color: #ffffff;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.3px;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .campaign-content-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .campaign-title-text {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .campaign-desc-text {
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .campaign-meta-footer {
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
</style>
@endpush

@section('content')
<div class="campaigns-page-wrap">
    <div class="container">
        <!-- Page Title -->
        <div class="text-center mb-5">
            <span class="badge bg-primary-light text-primary fw-bold px-3 py-1.5 rounded-pill mb-2">LIMITED TIME OFFERS</span>
            <h1 class="fw-bold text-dark display-6 mb-2">Campaigns & Flash Deals</h1>
            <p class="text-muted">Explore exclusive seasonal promotions and mega discounts on healthcare items.</p>
        </div>

        <!-- Active Campaigns -->
        <h3 class="fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="fas fa-bolt text-warning"></i> Running Campaigns
        </h3>

        <div class="row g-4 mb-5">
            @forelse($activeCampaigns as $campaign)
                <div class="col-md-6 col-lg-4">
                    <div class="campaign-card-item">
                        <div class="campaign-banner-cover">
                            @if($campaign->banner_image)
                                <img src="{{ asset($campaign->banner_image) }}" alt="{{ $campaign->title }}" class="campaign-banner-img">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white">
                                    <i class="fas fa-fire-alt text-danger" style="font-size: 42px;"></i>
                                </div>
                            @endif

                            @if($campaign->discount_type === 'percentage')
                                <span class="campaign-badge-floating">{{ $campaign->discount_value }}% OFF</span>
                            @elseif($campaign->discount_type === 'fixed')
                                <span class="campaign-badge-floating">SAVE ৳{{ $campaign->discount_value }}</span>
                            @endif
                        </div>

                        <div class="campaign-content-body">
                            <h3 class="campaign-title-text">{{ $campaign->title }}</h3>
                            <p class="campaign-desc-text">{{ $campaign->description ?: 'Exclusive campaign offers and fast delivery on selected products.' }}</p>

                            <div class="campaign-meta-footer">
                                <span class="text-muted small">
                                    <i class="fas fa-box-open text-primary me-1"></i> {{ $campaign->products_count }} Products
                                </span>
                                <a href="{{ route('ecommerce.campaigns.show', $campaign->slug) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                                    View Deals <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card p-5 text-center border-0 shadow-sm rounded-4">
                        <i class="fas fa-tags text-muted mb-3" style="font-size: 48px;"></i>
                        <h4 class="fw-bold mb-2">No Active Campaigns Right Now</h4>
                        <p class="text-muted mb-0">Stay tuned! Exciting flash deals and discount campaigns are coming soon.</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if($upcomingCampaigns->count() > 0)
            <h3 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-clock text-info"></i> Upcoming Campaigns
            </h3>
            <div class="row g-4">
                @foreach($upcomingCampaigns as $upcoming)
                    <div class="col-md-6 col-lg-4">
                        <div class="campaign-card-item opacity-75">
                            <div class="campaign-banner-cover">
                                @if($upcoming->banner_image)
                                    <img src="{{ asset($upcoming->banner_image) }}" alt="{{ $upcoming->title }}" class="campaign-banner-img">
                                @endif
                                <span class="campaign-badge-floating bg-warning text-dark">Starts Soon</span>
                            </div>
                            <div class="campaign-content-body">
                                <h3 class="campaign-title-text">{{ $upcoming->title }}</h3>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-calendar-alt me-1"></i> Starts on {{ $upcoming->start_date->format('d M, Y h:i A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
