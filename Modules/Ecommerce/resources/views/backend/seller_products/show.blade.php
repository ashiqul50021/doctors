@extends('layouts.admin')

@section('title', 'Review Seller Product - ' . $product->name)

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Review Seller Product</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.admin.seller-products.index') }}">Seller Products</a></li>
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('ecommerce.admin.seller-products.index') }}" class="btn btn-secondary">
                <i class="fe fe-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                @include('ecommerce::backend.seller_products.partials.review-modal-body', ['product' => $product])
            </div>
        </div>
    </div>
</div>
@endsection
