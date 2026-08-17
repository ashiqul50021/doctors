@extends('layouts.admin')

@section('title', 'Create Campaign - Admin')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Create New Campaign / Flash Sale</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.admin.campaigns.index') }}">Campaigns</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Campaign Configuration</h4>
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

                <form action="{{ route('ecommerce.admin.campaigns.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Campaign Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Eid Mega Flash Sale" value="{{ old('title') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Description / Offer Details</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Brief summary about the campaign perks...">{{ old('description') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Start Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date', now()->format('Y-m-d\TH:i')) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">End Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date', now()->addDays(3)->format('Y-m-d\TH:i')) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Discount Rule <span class="text-danger">*</span></label>
                                        <select name="discount_type" class="form-select" required id="discountTypeSelect">
                                            <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage Discount (%)</option>
                                            <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount Discount (৳)</option>
                                            <option value="custom_price" {{ old('discount_type') == 'custom_price' ? 'selected' : '' }}>Custom Product Pricing</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Discount Value <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="discount_value" class="form-control" placeholder="e.g. 15 for 15%" value="{{ old('discount_value', 10) }}" required>
                                        <small class="text-muted" id="discountHint">Discount will apply to all products in this campaign unless overridden.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Campaign Banner / Poster</label>
                                <input type="file" name="banner_image" class="form-control" accept="image/*">
                                <small class="text-muted">Recommended size: 1200x400px (JPG/PNG/WEBP)</small>
                            </div>

                            <div class="card bg-light border p-3 mt-4">
                                <h6 class="fw-bold mb-2">Display Settings</h6>
                                
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                                    <label class="form-check-label" for="isActive">Enable Campaign</label>
                                </div>

                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="show_on_homepage" value="1" id="showHomepage" checked>
                                    <label class="form-check-label" for="showHomepage">Show Widget on Homepage</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <a href="{{ route('ecommerce.admin.campaigns.index') }}" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save & Continue to Assign Products
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
