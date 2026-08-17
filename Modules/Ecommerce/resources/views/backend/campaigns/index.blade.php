@extends('layouts.admin')

@section('title', 'Campaigns & Flash Sales - Admin')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Campaigns & Flash Deals</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Campaigns</li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('ecommerce.admin.campaigns.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Create Campaign
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
    <div class="col-sm-12">
        <div class="card card-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Campaign Info</th>
                                <th>Schedule (Start - End)</th>
                                <th>Discount Rule</th>
                                <th>Products</th>
                                <th>Live Status</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $campaign)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($campaign->banner_image)
                                                <img src="{{ asset($campaign->banner_image) }}" alt="Banner" class="rounded me-2" style="width: 50px; height: 35px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 35px;">
                                                    <i class="fas fa-bolt text-warning"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('ecommerce.campaigns.show', $campaign->slug) }}" target="_blank" class="fw-bold text-dark">
                                                    {{ $campaign->title }} <i class="fas fa-external-link-alt ms-1 text-muted" style="font-size: 10px;"></i>
                                                </a>
                                                <div class="text-muted small">Slug: {{ $campaign->slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div><strong>{{ $campaign->start_date->format('d M, Y h:i A') }}</strong></div>
                                        <div class="text-muted small">to {{ $campaign->end_date->format('d M, Y h:i A') }}</div>
                                    </td>
                                    <td>
                                        @if($campaign->discount_type === 'percentage')
                                            <span class="badge bg-primary-light text-primary fw-bold">{{ $campaign->discount_value }}% OFF</span>
                                        @elseif($campaign->discount_type === 'fixed')
                                            <span class="badge bg-success-light text-success fw-bold">৳{{ $campaign->discount_value }} OFF</span>
                                        @else
                                            <span class="badge bg-info-light text-info fw-bold">Custom Pricing</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('ecommerce.admin.campaigns.products', $campaign->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-box-open me-1"></i> {{ $campaign->products_count }} Products
                                        </a>
                                    </td>
                                    <td>
                                        @if($campaign->isRunning())
                                            <span class="badge bg-success"><i class="fas fa-circle me-1" style="font-size: 8px;"></i> Running Now</span>
                                        @elseif($campaign->isUpcoming())
                                            <span class="badge bg-warning"><i class="fas fa-clock me-1"></i> Upcoming</span>
                                        @else
                                            <span class="badge bg-secondary">Expired</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('ecommerce.admin.campaigns.toggle-status', $campaign->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $campaign->is_active ? 'btn-success' : 'btn-danger' }}" style="padding: 2px 8px; font-size: 11px;">
                                                {{ $campaign->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('ecommerce.admin.campaigns.products', $campaign->id) }}" class="btn btn-sm bg-info-light" title="Manage Products">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                        <a href="{{ route('ecommerce.admin.campaigns.edit', $campaign->id) }}" class="btn btn-sm bg-warning-light" title="Edit Campaign">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('ecommerce.admin.campaigns.destroy', $campaign->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this campaign?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-danger-light" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        No campaigns found. Click "Create Campaign" to launch a new flash sale!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($campaigns->hasPages())
                <div class="card-footer">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
