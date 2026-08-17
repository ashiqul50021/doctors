@extends('layouts.admin')

@section('title', 'Edit Campaign - Admin')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Edit Campaign</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.admin.campaigns.index') }}">Campaigns</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('ecommerce.admin.campaigns.products', $campaign->id) }}" class="btn btn-outline-primary">
                <i class="fas fa-box-open me-1"></i> Manage Products
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Edit: {{ $campaign->title }}</h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('ecommerce.admin.campaigns.update', $campaign->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Campaign Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $campaign->title) }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Description / Offer Details</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $campaign->description) }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Start Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date', $campaign->start_date->format('Y-m-d\TH:i')) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">End Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date', $campaign->end_date->format('Y-m-d\TH:i')) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Discount Rule <span class="text-danger">*</span></label>
                                        <select name="discount_type" class="form-select" required>
                                            <option value="percentage" {{ old('discount_type', $campaign->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage Discount (%)</option>
                                            <option value="fixed" {{ old('discount_type', $campaign->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount Discount (৳)</option>
                                            <option value="custom_price" {{ old('discount_type', $campaign->discount_type) == 'custom_price' ? 'selected' : '' }}>Custom Product Pricing</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Discount Value <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="discount_value" class="form-control" value="{{ old('discount_value', $campaign->discount_value) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Campaign Banner / Poster</label>
                                @if($campaign->banner_image)
                                    <div class="mb-2">
                                        <img src="{{ asset($campaign->banner_image) }}" alt="Banner" class="img-fluid rounded border">
                                    </div>
                                @endif
                                <input type="file" name="banner_image" class="form-control" accept="image/*">
                            </div>

                            <div class="card bg-light border p-3 mt-4">
                                <h6 class="fw-bold mb-2">Display Settings</h6>
                                
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ $campaign->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActive">Enable Campaign</label>
                                </div>

                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="show_on_homepage" value="1" id="showHomepage" {{ $campaign->show_on_homepage ? 'checked' : '' }}>
                                    <label class="form-check-label" for="showHomepage">Show Widget on Homepage</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <a href="{{ route('ecommerce.admin.campaigns.index') }}" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Campaign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
