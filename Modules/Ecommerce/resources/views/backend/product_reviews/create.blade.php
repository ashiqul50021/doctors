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
                                <select name="product_id" id="product_id" class="form-control select2-product-search" required>
                                    <option value="">-- Search & Select Product --</option>
                                    @foreach($products as $product)
                                        @php
                                            $img = $product->image;
                                            if (!$img && !empty($product->gallery) && is_array($product->gallery)) {
                                                $img = $product->gallery[0] ?? null;
                                            }
                                            $imgUrl = $img ? asset($img) : asset('assets/img/products/default-product.png');
                                        @endphp
                                        <option value="{{ $product->id }}" 
                                                data-image="{{ $imgUrl }}" 
                                                {{ old('product_id') == $product->id ? 'selected' : '' }}>
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

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
<style>
.select2-container--default .select2-selection--single {
    height: 44px !important;
    display: flex !important;
    align-items: center !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 44px !important;
    display: flex !important;
    align-items: center !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 42px !important;
}
.product-select-item {
    display: flex;
    align-items: center;
    gap: 10px;
}
.product-select-img {
    width: 32px;
    height: 32px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof $ !== 'undefined' && $.fn.select2) {
        function formatProduct(product) {
            if (!product.id) {
                return product.text;
            }

            var imageUrl = product.image;
            if (!imageUrl && product.element) {
                imageUrl = $(product.element).data('image');
            }
            if (!imageUrl) {
                imageUrl = "{{ asset('assets/img/products/default-product.png') }}";
            }

            var $container = $(
                '<div class="product-select-item">' +
                    '<img src="' + imageUrl + '" class="product-select-img" />' +
                    '<span class="product-select-name">' + product.text + '</span>' +
                '</div>'
            );

            return $container;
        }

        if ($('#product_id').hasClass('select2-hidden-accessible')) {
            $('#product_id').select2('destroy');
        }

        $('#product_id').select2({
            placeholder: '-- Search & Select Product --',
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ route('ecommerce.admin.product-reviews.search-products') }}",
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            },
            templateResult: formatProduct,
            templateSelection: formatProduct,
            escapeMarkup: function (m) { return m; }
        });
    }
});
</script>
@endpush
