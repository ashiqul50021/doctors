@extends('layouts.admin')

@section('title', 'Products - abcsheba Admin')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Products</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Products</li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('ecommerce.admin.products.create') }}" class="btn btn-primary">
                <i class="fe fe-plus me-1"></i> Add Product
            </a>
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
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            @php
                                $availableStock = $product->availableStock();
                                $activeVariantCount = $product->activeVariantItems()->count();

                                if ($availableStock < 1) {
                                    $stockBadgeClass = 'bg-danger-light';
                                    $stockLabel = 'Out of Stock';
                                } elseif ($availableStock <= 10) {
                                    $stockBadgeClass = 'bg-warning-light';
                                    $stockLabel = 'Low Stock';
                                } else {
                                    $stockBadgeClass = 'bg-success-light';
                                    $stockLabel = 'In Stock';
                                }
                            @endphp
                            <tr>
                                <td><span class="product-code-badge">#PRO{{ $product->id }}</span></td>
                                <td>
                                    <div class="table-avatar">
                                        @php
                                            $productImage = $product->image;

                                            if (! $productImage && !empty($product->gallery) && is_array($product->gallery)) {
                                                $productImage = $product->gallery[0] ?? null;
                                            }
                                        @endphp
                                        @if($productImage)
                                            <a href="#" class="avatar avatar-sm me-3">
                                                <img class="avatar-img" src="{{ \Illuminate\Support\Str::startsWith($productImage, ['http://', 'https://']) ? $productImage : asset($productImage) }}" alt="Product">
                                            </a>
                                        @else
                                            <span class="avatar avatar-sm me-3 d-inline-flex align-items-center justify-content-center bg-light text-muted border"
                                                style="font-size: 9px; font-weight: 600;">
                                                No Image
                                            </span>
                                        @endif
                                        <a href="#">{{ $product->name }}</a>
                                    </div>
                                </td>
                                <td><span class="text-secondary fw-semibold">{{ $product->category->name ?? 'N/A' }}</span></td>
                                <td><span class="price-text">৳{{ number_format($product->price, 2) }}</span></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $availableStock }}</span>
                                        <span class="badge {{ $stockBadgeClass }} mt-1" style="width: fit-content;">
                                            {{ $stockLabel }}
                                        </span>
                                        <small class="text-muted mt-1" style="font-size: 11px;">
                                            {{ $activeVariantCount > 0 ? $activeVariantCount . ' variant(s)' : 'Simple product' }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="status-toggle">
                                        <input type="checkbox" id="status_{{ $product->id }}" class="check status-toggle-btn" data-id="{{ $product->id }}" {{ $product->is_active ? 'checked' : '' }}>
                                        <label for="status_{{ $product->id }}" class="checktoggle">checkbox</label>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="actions">
                                        <a class="btn-action-edit" href="{{ route('ecommerce.admin.products.edit', $product->id) }}">
                                            <i class="fe fe-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('ecommerce.admin.products.destroy', $product->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-delete">
                                                <i class="fe fe-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $(document).on('change', '.status-toggle-btn', function() {
            const checkbox = $(this);
            const productId = checkbox.data('id');
            const isChecked = checkbox.is(':checked');

            $.ajax({
                url: "{{ route('ecommerce.admin.products.index') }}/" + productId + "/toggle-status",
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (!response.success) {
                        checkbox.prop('checked', !isChecked);
                    }
                },
                error: function() {
                    checkbox.prop('checked', !isChecked);
                    alert('Status toggle failed.');
                }
            });
        });
    });
</script>
@endpush
