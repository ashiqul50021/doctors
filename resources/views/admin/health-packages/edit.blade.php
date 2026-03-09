@extends('layouts.admin')

@section('title', 'Edit Health Package - Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Edit Health Package</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.health-packages.index') }}">Health Packages</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.health-packages.update', $healthPackage->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row form-row">
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label>Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $healthPackage->title) }}" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3">
                            <div class="mb-3">
                                <label>Badge Label <span class="text-danger">*</span></label>
                                <input type="text" name="badge_label" class="form-control" value="{{ old('badge_label', $healthPackage->badge_label) }}" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3">
                            <div class="mb-3">
                                <label>Icon Class <span class="text-danger">*</span></label>
                                <input type="text" name="icon" class="form-control" value="{{ old('icon', $healthPackage->icon) }}" required>
                                <small class="text-muted">FontAwesome class, e.g. fas fa-heartbeat</small>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3">
                            <div class="mb-3">
                                <label>Test Count <span class="text-danger">*</span></label>
                                <input type="number" name="test_count" class="form-control" value="{{ old('test_count', $healthPackage->test_count) }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3">
                            <div class="mb-3">
                                <label>Price (৳) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" value="{{ old('price', $healthPackage->price) }}" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3">
                            <div class="mb-3">
                                <label>Price Label</label>
                                <input type="text" name="price_label" class="form-control" value="{{ old('price_label', $healthPackage->price_label) }}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-3">
                            <div class="mb-3">
                                <label>Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $healthPackage->sort_order) }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label>Features (one per line)</label>
                                <textarea name="features" class="form-control" rows="5">{{ old('features', is_array($healthPackage->features) ? implode("\n", $healthPackage->features) : $healthPackage->features) }}</textarea>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label>Custom Link (optional)</label>
                                <input type="text" name="link" class="form-control" value="{{ old('link', $healthPackage->link) }}" placeholder="Leave empty for default products page">
                            </div>
                        </div>
                        <div class="col-12 col-sm-3">
                            <div class="mb-3 mt-4">
                                <div class="form-check">
                                    <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured" {{ old('is_featured', $healthPackage->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">Featured (Most Popular)</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3">
                            <div class="mb-3 mt-4">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ old('is_active', $healthPackage->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Update Package</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
