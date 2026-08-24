<div class="product-details-modal-container">
    <div class="row">
        <!-- Product Image & Gallery Column -->
        <div class="col-md-5 mb-3">
            @php
                $mainImg = $product->image;
                if (!$mainImg && !empty($product->gallery) && is_array($product->gallery)) {
                    $mainImg = $product->gallery[0] ?? null;
                }
                $mainImgUrl = $mainImg ? (Str::startsWith($mainImg, ['http://', 'https://']) ? $mainImg : asset($mainImg)) : asset('assets/img/products/default-product.png');
            @endphp
            <div class="product-main-img-box text-center p-3 border rounded-3 bg-light position-relative mb-2">
                <img src="{{ $mainImgUrl }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-height: 250px; object-fit: contain;">
                @if($product->is_featured)
                    <span class="badge bg-warning position-absolute top-0 start-0 m-2 px-2 py-1"><i class="fas fa-star me-1"></i> Featured</span>
                @endif
            </div>

            @if(!empty($product->gallery) && is_array($product->gallery) && count($product->gallery) > 0)
                <div class="d-flex gap-2 overflow-auto py-1">
                    @foreach($product->gallery as $gImg)
                        @php
                            $gUrl = Str::startsWith($gImg, ['http://', 'https://']) ? $gImg : asset($gImg);
                        @endphp
                        <img src="{{ $gUrl }}" class="rounded border" style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;">
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Overview Info Column -->
        <div class="col-md-7 mb-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="badge bg-primary-light text-primary px-2 py-1" style="font-size: 11px;">#PRO{{ $product->id }}</span>
                <div>
                    @if($product->status === 'approved' || ($product->is_approved && $product->is_active))
                        <span class="badge bg-success-light text-success px-2 py-1"><i class="fas fa-check-circle me-1"></i> Approved / Live</span>
                    @elseif($product->status === 'rejected' || (! $product->is_approved && ! empty($product->rejection_reason)))
                        <span class="badge bg-danger-light text-danger px-2 py-1"><i class="fas fa-times-circle me-1"></i> Rejected</span>
                    @elseif($product->is_active)
                        <span class="badge bg-success-light text-success px-2 py-1"><i class="fas fa-check-circle me-1"></i> Active</span>
                    @else
                        <span class="badge bg-warning-light text-warning px-2 py-1"><i class="fas fa-clock me-1"></i> Pending Approval</span>
                    @endif
                </div>
            </div>

            @if($product->rejection_reason)
                <div class="alert alert-danger p-2 mb-2" style="font-size: 12px;">
                    <strong><i class="fas fa-exclamation-circle me-1"></i> Rejection Reason:</strong> {{ $product->rejection_reason }}
                </div>
            @endif

            <h4 class="fw-bold mb-2">{{ $product->name }}</h4>

            <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
                <span class="text-muted small"><i class="fas fa-folder me-1"></i> Category: <strong>{{ $product->category->name ?? 'N/A' }}</strong></span>
                <span class="text-muted small">|</span>
                <span class="text-muted small"><i class="fas fa-store me-1"></i> Source: 
                    @if($product->seller_id)
                        <span class="badge bg-info-light text-info">{{ $product->seller->name ?? 'Seller' }}</span>
                    @else
                        <span class="badge bg-secondary-light text-secondary">Official Product</span>
                    @endif
                </span>
            </div>

            <!-- Price & Stock Summary Cards -->
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="p-2 border rounded bg-light">
                        <small class="text-muted d-block" style="font-size: 11px;">REGULAR PRICE</small>
                        <span class="fw-bold fs-5 text-dark">৳{{ number_format($product->price, 2) }}</span>
                        @if($product->sale_price && $product->sale_price < $product->price)
                            <small class="text-success d-block" style="font-size: 11px;">Sale: ৳{{ number_format($product->sale_price, 2) }}</small>
                        @endif
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 border rounded bg-light">
                        <small class="text-muted d-block" style="font-size: 11px;">TOTAL AVAILABLE STOCK</small>
                        <span class="fw-bold fs-5 {{ $product->availableStock() > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $product->availableStock() }}
                        </span>
                        <small class="text-muted d-block" style="font-size: 11px;">
                            {{ $product->activeVariantItems()->count() > 0 ? $product->activeVariantItems()->count() . ' Variants' : 'Simple Product' }}
                        </small>
                    </div>
                </div>
            </div>

            @if($product->sku)
                <p class="mb-2 text-muted small"><strong>SKU:</strong> <code>{{ $product->sku }}</code></p>
            @endif

            @if($product->short_description)
                <div class="p-2 border rounded bg-light mb-2">
                    <small class="text-muted d-block fw-bold mb-1" style="font-size: 11px;">SHORT DESCRIPTION</small>
                    <p class="mb-0 small text-secondary">{!! nl2br(e($product->short_description)) !!}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Product Variants Table (If Any) -->
    @if($product->variants && $product->variants->count() > 0)
        <div class="mt-3">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-2"><i class="fas fa-cubes me-1"></i> Product Variants</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Option Name</th>
                            <th>Value</th>
                            <th>Price</th>
                            <th>Sale Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->variants as $index => $variant)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $variant->option_name }}</td>
                                <td><span class="badge bg-secondary">{{ $variant->option_value }}</span></td>
                                <td>৳{{ number_format($variant->price, 2) }}</td>
                                <td>{{ $variant->sale_price ? '৳' . number_format($variant->sale_price, 2) : '-' }}</td>
                                <td><strong>{{ $variant->stock }}</strong></td>
                                <td>
                                    @if($variant->is_active)
                                        <span class="badge bg-success-light text-success">Active</span>
                                    @else
                                        <span class="badge bg-danger-light text-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Full Description -->
    @if($product->description)
        <div class="mt-3">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-2"><i class="fas fa-align-left me-1"></i> Full Description</h6>
            <div class="p-3 border rounded bg-white text-secondary small" style="max-height: 200px; overflow-y: auto;">
                {!! $product->description !!}
            </div>
        </div>
    @endif
</div>
