<div class="product-details-livewire-container py-4">
    @if(!$product)
        <div class="alert alert-warning text-center">
            <p class="mb-0">Product not found.</p>
        </div>
    @else
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/" wire:navigate>Home</a></li>
                <li class="breadcrumb-item"><a href="/products" wire:navigate>Products</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->title ?? $product->name }}</li>
            </ol>
        </nav>

        <div class="card border-0 shadow-sm p-4 mb-4">
            <div class="row g-4 align-items-center">
                <!-- Product Image Column -->
                <div class="col-md-6 col-lg-5">
                    <div class="product-main-img border rounded overflow-hidden p-2 text-center bg-white">
                        <img src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : ($product->image ? asset($product->image) : asset('assets/img/products/default-product.png')) }}" 
                             alt="{{ $product->title }}" 
                             class="img-fluid rounded" 
                             style="max-height: 380px; object-fit: contain;">
                    </div>
                </div>

                <!-- Product Details Column -->
                <div class="col-md-6 col-lg-7">
                    <div class="product-info-wrap">
                        @if($product->is_medical)
                            <span class="badge bg-danger mb-2">Rx Medical Product</span>
                        @else
                            <span class="badge bg-success mb-2">General Product</span>
                        @endif

                        <h2 class="fw-bold text-dark mb-1">{{ $product->title ?? $product->name }}</h2>
                        @if($product->generic_name)
                            <p class="text-primary fst-italic small mb-3">Generic: {{ $product->generic_name }}</p>
                        @endif

                        <div class="price-box mb-3">
                            <span class="h3 fw-bold text-primary mb-0">৳{{ number_format($product->sale_price ?? $product->regular_price ?? $product->price, 2) }}</span>
                            @if(($product->sale_price && $product->sale_price < $product->regular_price) || ($product->regular_price > $product->price))
                                <span class="text-muted text-decoration-line-through ms-2">৳{{ number_format($product->regular_price ?? $product->price, 2) }}</span>
                            @endif
                        </div>

                        <p class="text-secondary mb-4">{{ $product->short_description ?? $product->description ?? 'High quality health product.' }}</p>

                        <!-- Quantity Selector & Cart Action -->
                        <div class="d-flex align-items-center gap-3">
                            <div class="input-group" style="width: 130px;">
                                <button type="button" wire:click="decreaseQty" class="btn btn-outline-secondary btn-sm">-</button>
                                <input type="text" class="form-control form-control-sm text-center fw-bold bg-white" value="{{ $quantity }}" readonly>
                                <button type="button" wire:click="increaseQty" class="btn btn-outline-secondary btn-sm">+</button>
                            </div>

                            <button type="button" wire:click="addToCart" class="btn btn-primary px-4 py-2 fw-semibold">
                                <i class="fas fa-shopping-cart me-2"></i> Add To Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Details Page Sections (FAQ, Video, Steps) -->
        @if(!empty($product->custom_sections))
            <div class="card border-0 shadow-sm p-4">
                <h4 class="fw-bold text-dark border-bottom pb-2 mb-3">Highlights & Specifications</h4>
                <div class="row g-3">
                    @foreach($product->custom_sections as $sec)
                        <div class="col-12">
                            <div class="p-3 bg-light rounded border">
                                @if($sec['type'] === 'faq')
                                    <h5 class="fw-bold text-primary mb-1">❓ {{ $sec['question'] }}</h5>
                                    <p class="mb-0 text-secondary">{{ $sec['answer'] }}</p>
                                @elseif($sec['type'] === 'video')
                                    <h5 class="fw-bold text-dark mb-2">🎥 {{ $sec['title'] }}</h5>
                                    <div class="ratio ratio-16x9 rounded overflow-hidden">
                                        <iframe src="{{ $sec['video_url'] }}" allowfullscreen></iframe>
                                    </div>
                                @elseif($sec['type'] === 'steps')
                                    <h5 class="fw-bold text-dark mb-1">📌 {{ $sec['title'] }}</h5>
                                    <p class="mb-0 text-secondary">{{ $sec['description'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
