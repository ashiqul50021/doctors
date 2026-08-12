@extends('layouts.admin')

@section('title', 'Add Product - Seller')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Add Product</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.seller.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.seller.products.index') }}">Products</a></li>
                <li class="breadcrumb-item active">Add Product</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <livewire:seller.product-builder />
    </div>
</div>
@endsection
