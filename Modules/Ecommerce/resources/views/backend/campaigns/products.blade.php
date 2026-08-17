@extends('layouts.admin')

@section('title', 'Manage Campaign Products - ' . $campaign->title)

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Manage Products: {{ $campaign->title }}</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.admin.campaigns.index') }}">Campaigns</a></li>
                <li class="breadcrumb-item active">Campaign Products</li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('ecommerce.admin.campaigns.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Campaigns
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <!-- Add Products Card -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Add Products to Campaign</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('ecommerce.admin.campaigns.products.add', $campaign->id) }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Select Products</label>
                        <select name="product_ids[]" class="form-select" multiple style="height: 320px;" required>
                            @foreach($availableProducts as $product)
                                @if(!in_array($product->id, $assignedProductIds))
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }} (৳{{ number_format($product->price, 0) }}) - [{{ $product->category->name ?? 'General' }}]
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl / Cmd to select multiple products.</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-plus-circle me-1"></i> Add Selected to Campaign
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Assigned Products List -->
    <div class="col-md-7">
        <div class="card card-table">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Assigned Products ({{ $campaign->products->count() }})</h4>
                <div class="badge bg-primary">
                    Default: 
                    @if($campaign->discount_type === 'percentage')
                        {{ $campaign->discount_value }}% OFF
                    @elseif($campaign->discount_type === 'fixed')
                        ৳{{ $campaign->discount_value }} OFF
                    @else
                        Custom
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Regular Price</th>
                                <th>Campaign Price</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaign->products as $product)
                                @php
                                    $calculatedPrice = $campaign->calculateCampaignPrice((float)$product->price, $product->pivot->campaign_price ? (float)$product->pivot->campaign_price : null);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $product->name }}</div>
                                        <small class="text-muted">{{ $product->category->name ?? 'N/A' }}</small>
                                    </td>
                                    <td>৳{{ number_format($product->price, 2) }}</td>
                                    <td>
                                        <form action="{{ route('ecommerce.admin.campaigns.products.price', [$campaign->id, $product->id]) }}" method="POST" class="d-flex align-items-center gap-1">
                                            @csrf
                                            <input type="number" step="0.01" name="campaign_price" class="form-control form-control-sm" style="width: 100px;" 
                                                   placeholder="৳{{ $calculatedPrice }}" 
                                                   value="{{ $product->pivot->campaign_price }}">
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Save Custom Price">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <small class="text-success d-block mt-1">Effective: <strong>৳{{ number_format($calculatedPrice, 2) }}</strong></small>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('ecommerce.admin.campaigns.products.remove', [$campaign->id, $product->id]) }}" method="POST" onsubmit="return confirm('Remove product from this campaign?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-danger-light" title="Remove">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        No products assigned to this campaign yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
