@extends('layouts.admin')

@section('title', 'My Products')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">My Products</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.seller.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Products</li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('ecommerce.seller.products.create') }}" class="btn btn-primary">Add Product</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="datatable table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            @php
                                $availableStock = $product->availableStock();
                                $activeVariantCount = $product->activeVariantItems()->count();

                                if ($availableStock < 1) {
                                    $stockBadgeClass = 'bg-danger-light text-danger';
                                    $stockLabel = 'Out of Stock';
                                } elseif ($availableStock <= 10) {
                                    $stockBadgeClass = 'bg-warning-light text-warning';
                                    $stockLabel = 'Low Stock';
                                } else {
                                    $stockBadgeClass = 'bg-success-light text-success';
                                    $stockLabel = 'In Stock';
                                }
                            @endphp
                            <tr>
                                <td>#PRO{{ $product->id }}</td>
                                <td>
                                    <h2 class="table-avatar">
                                        @php
                                            $productImage = $product->image;

                                            if (! $productImage && !empty($product->gallery) && is_array($product->gallery)) {
                                                $productImage = $product->gallery[0] ?? null;
                                            }
                                        @endphp
                                        @if($productImage)
                                            <a href="#" class="avatar avatar-sm me-2">
                                                <img class="avatar-img" src="{{ \Illuminate\Support\Str::startsWith($productImage, ['http://', 'https://']) ? $productImage : asset($productImage) }}" alt="Product">
                                            </a>
                                        @else
                                            <span class="avatar avatar-sm me-2 d-inline-flex align-items-center justify-content-center bg-light text-muted border"
                                                style="font-size: 9px; font-weight: 600;">
                                                No Image
                                            </span>
                                        @endif
                                        <a href="#" style="text-decoration: none; color: #333;">{{ $product->name }}</a>
                                    </h2>
                                </td>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                                <td>৳{{ number_format($product->price, 2) }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold">{{ $availableStock }}</span>
                                        <span class="badge {{ $stockBadgeClass }} mt-1" style="width: fit-content;">
                                            {{ $stockLabel }}
                                        </span>
                                        <small class="text-muted mt-1">
                                            {{ $activeVariantCount > 0 ? $activeVariantCount . ' variant(s)' : 'Simple product' }}
                                        </small>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div class="actions">
                                        <a class="btn btn-sm bg-success-light me-2" href="{{ route('ecommerce.seller.products.edit', $product->id) }}">
                                            <i class="fe fe-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('ecommerce.seller.products.destroy', $product->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-danger-light">
                                                <i class="fe fe-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No products found.</td>
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
