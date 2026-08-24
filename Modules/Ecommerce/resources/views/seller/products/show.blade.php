@extends('layouts.admin')

@section('title', 'Product Details - ' . $product->name)

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Product Details</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.seller.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.seller.products.index') }}">Products</a></li>
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ul>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ route('ecommerce.products.show', $product->id) }}" target="_blank" class="btn btn-info text-white">
                <i class="fe fe-globe me-1"></i> Site View
            </a>
            <a href="{{ route('ecommerce.seller.products.edit', $product->id) }}" class="btn btn-primary">
                <i class="fe fe-pencil me-1"></i> Edit Product
            </a>
            <a href="{{ route('ecommerce.seller.products.index') }}" class="btn btn-secondary">
                <i class="fe fe-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                @include('ecommerce::backend.products.partials.details-modal-body', ['product' => $product])
            </div>
        </div>
    </div>
</div>
@endsection
