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
                                <th>Status</th>
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
                                            <a href="{{ route('ecommerce.products.show', $product->id) }}" target="_blank" class="avatar avatar-sm me-2">
                                                <img class="avatar-img" src="{{ \Illuminate\Support\Str::startsWith($productImage, ['http://', 'https://']) ? $productImage : asset($productImage) }}" alt="Product">
                                            </a>
                                        @else
                                            <span class="avatar avatar-sm me-2 d-inline-flex align-items-center justify-content-center bg-light text-muted border"
                                                style="font-size: 9px; font-weight: 600;">
                                                No Image
                                            </span>
                                        @endif
                                        <a href="{{ route('ecommerce.products.show', $product->id) }}" target="_blank" style="text-decoration: none; color: #333;">{{ $product->name }}</a>
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
                                <td>
                                    @if($product->status === 'approved' || ($product->is_approved && $product->is_active))
                                        <span class="badge bg-success-light text-success px-2 py-1"><i class="fas fa-check-circle me-1"></i> Approved / Live</span>
                                    @elseif($product->status === 'rejected' || (! $product->is_approved && ! empty($product->rejection_reason)))
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-danger-light text-danger px-2 py-1" style="width: fit-content;"><i class="fas fa-times-circle me-1"></i> Rejected</span>
                                            <small class="text-danger mt-1 fw-semibold" style="font-size: 11px;">Reason: {{ $product->rejection_reason }}</small>
                                        </div>
                                    @else
                                        <span class="badge bg-warning-light text-warning px-2 py-1"><i class="fas fa-clock me-1"></i> Pending Approval</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="actions">
                                        <a class="btn btn-sm bg-info-light text-info me-1" href="{{ route('ecommerce.products.show', $product->id) }}" target="_blank" title="Site View">
                                            <i class="fe fe-globe"></i> Site View
                                        </a>
                                        <a class="btn btn-sm bg-primary-light text-primary btn-seller-details-modal me-1" href="{{ route('ecommerce.seller.products.show', $product->id) }}" title="Details View">
                                            <i class="fe fe-eye"></i> Details View
                                        </a>
                                        <a class="btn btn-sm bg-success-light me-1" href="{{ route('ecommerce.seller.products.edit', $product->id) }}" title="Edit Product">
                                            <i class="fe fe-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('ecommerce.seller.products.destroy', $product->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-danger-light" title="Delete Product">
                                                <i class="fe fe-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No products found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Seller Product Details Modal -->
<div class="modal fade" id="sellerProductDetailsModal" tabindex="-1" aria-labelledby="sellerProductDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="sellerProductDetailsModalLabel">Product Details Overview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="sellerProductDetailsModalContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $(document).on('click', '.btn-seller-details-modal', function(e) {
            e.preventDefault();
            const detailsUrl = $(this).attr('href');
            const modal = new bootstrap.Modal(document.getElementById('sellerProductDetailsModal'));
            $('#sellerProductDetailsModalContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            modal.show();

            $.ajax({
                url: detailsUrl,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.html) {
                        $('#sellerProductDetailsModalContent').html(response.html);
                    } else {
                        $('#sellerProductDetailsModalContent').html('<div class="alert alert-danger mb-0">Failed to load details.</div>');
                    }
                },
                error: function() {
                    $('#sellerProductDetailsModalContent').html('<div class="alert alert-danger mb-0">Error fetching product details.</div>');
                }
            });
        });

        $(document).on('change', '.seller-status-toggle-btn', function() {
            const checkbox = $(this);
            const productId = checkbox.data('id');
            const isChecked = checkbox.is(':checked');

            $.ajax({
                url: "{{ route('ecommerce.seller.products.index') }}/" + productId + "/toggle-status",
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
