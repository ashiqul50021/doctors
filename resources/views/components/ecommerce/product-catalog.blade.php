<div class="product-catalog-livewire-container py-4">
    <div class="row g-4">
        <!-- Sidebar Filter Panel -->
        <div class="col-md-12 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm p-4 space-y-4">
                <h4 class="fw-bold text-dark border-bottom pb-2 mb-3">🔍 Filter Products</h4>

                <!-- Search Bar -->
                <div class="mb-3">
                    <label class="form-label text-uppercase text-muted fw-bold small">Search</label>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Type product or generic name..." class="form-control">
                </div>

                <!-- Product Type Filter -->
                <div class="mb-3">
                    <label class="form-label text-uppercase text-muted fw-bold small">Product Type</label>
                    <select wire:model.live="is_medical_filter" class="form-select">
                        <option value="">All Products</option>
                        <option value="0">General Items</option>
                        <option value="1">Medical & Healthcare</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div class="mb-3">
                    <label class="form-label text-uppercase text-muted fw-bold small">Sort By</label>
                    <select wire:model.live="sort_by" class="form-select">
                        <option value="latest">Latest Arrivals</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Product Grid Catalog -->
        <div class="col-md-12 col-lg-8 col-xl-9">
            <div wire:loading class="alert alert-info w-100 text-center">
                Updating catalog in real-time...
            </div>

            @if($products->isEmpty())
                <div class="card border-0 shadow-sm p-5 text-center">
                    <p class="text-muted mb-0">No products found matching your filters.</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach($products as $product)
                        <div class="col-sm-6 col-md-4">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden d-flex flex-column justify-between">
                                <div class="card-body p-3">
                                    <div class="product-img-wrap text-center mb-3">
                                        <a href="{{ route('ecommerce.products.show', $product->id) }}" wire:navigate>
                                            <img src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : ($product->image ? asset($product->image) : asset('assets/img/products/default-product.png')) }}" 
                                                 alt="{{ $product->title }}" 
                                                 class="img-fluid rounded" 
                                                 style="max-height: 180px; object-fit: contain;">
                                        </a>
                                    </div>

                                    @if($product->is_medical)
                                        <span class="badge bg-danger mb-2">Rx Medical</span>
                                    @else
                                        <span class="badge bg-success mb-2">General</span>
                                    @endif

                                    <h5 class="fw-bold text-dark text-truncate mb-1">
                                        <a href="{{ route('ecommerce.products.show', $product->id) }}" wire:navigate class="text-decoration-none text-dark hover-primary">
                                            {{ $product->title ?? $product->name }}
                                        </a>
                                    </h5>
                                    @if($product->generic_name)
                                        <p class="text-muted small fst-italic mb-2">Generic: {{ $product->generic_name }}</p>
                                    @endif

                                    <div class="price-wrap mt-2">
                                        <span class="fw-bold text-primary h5 mb-0">৳{{ number_format($product->sale_price ?? $product->regular_price ?? $product->price, 2) }}</span>
                                        @if(($product->sale_price && $product->sale_price < $product->regular_price) || ($product->regular_price > $product->price))
                                            <span class="text-muted text-decoration-line-through small ms-1">৳{{ number_format($product->regular_price ?? $product->price, 2) }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-footer bg-light border-0 p-3">
                                    <button wire:click="addToCart({{ $product->id }})" class="btn btn-primary w-100 btn-sm fw-semibold">
                                        🛒 Add To Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
