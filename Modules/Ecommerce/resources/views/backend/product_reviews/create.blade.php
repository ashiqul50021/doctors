@extends('layouts.admin')

@section('title', 'Add Custom Review - abcsheba Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Add Custom Product Review</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.admin.product-reviews.index') }}">Product Reviews</a></li>
                <li class="breadcrumb-item active">Add Custom Review</li>
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

                <form action="{{ route('ecommerce.admin.product-reviews.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="product_id">Select Product <span class="text-danger">*</span></label>
                                <select name="product_id" id="product_id" class="form-control select" required>
                                    <option value="">-- Select Product --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="reviewer_name">Reviewer Name <span class="text-danger">*</span></label>
                                <input type="text" name="reviewer_name" id="reviewer_name" class="form-control" value="{{ old('reviewer_name') }}" required placeholder="e.g., John Doe">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="rating">Rating (1 to 5 Stars) <span class="text-danger">*</span></label>
                                <select name="rating" id="rating" class="form-control" required>
                                    <option value="5" {{ old('rating', '5') == '5' ? 'selected' : '' }}>5 Stars</option>
                                    <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                                    <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                                    <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                                    <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="title">Review Title (Optional)</label>
                                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="e.g., Highly Recommended">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="comment">Review Comment <span class="text-danger">*</span></label>
                        <textarea name="comment" id="comment" rows="5" class="form-control" required placeholder="Write review comments here...">{{ old('comment') }}</textarea>
                    </div>

                    <div class="form-group mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_verified_purchase" id="is_verified_purchase" value="1" {{ old('is_verified_purchase', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_verified_purchase">
                                Verified Purchase
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Review</button>
                    <a href="{{ route('ecommerce.admin.product-reviews.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
