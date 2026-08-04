@extends('layouts.admin')

@include('ecommerce::backend.products.partials.image-manager-styles')
@include('ecommerce::backend.products.partials.product-manager-styles')

@section('title', 'Add Product - abcsheba Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Add Product</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.admin.products.index') }}">Products</a></li>
                <li class="breadcrumb-item active">Add Product</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        @php
            $variantRows = collect(old('variants', []))->map(function ($variant) {
                return [
                    'attributes' => $variant['attributes'] ?? [],
                    'price' => $variant['price'] ?? '',
                    'sale_price' => $variant['sale_price'] ?? '',
                    'stock' => $variant['stock'] ?? '',
                    'sku' => $variant['sku'] ?? '',
                    'is_active' => !array_key_exists('is_active', $variant) || (bool) $variant['is_active'],
                ];
            })->all();
        @endphp
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

                <!-- Tab Navigation Links -->
                <ul class="nav nav-tabs nav-tabs-solid nav-justified mb-4" id="productFormTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general-pane" role="tab" aria-controls="general-pane" aria-selected="true">
                            <i class="fas fa-info-circle me-1"></i> General Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pricing-tab" data-bs-toggle="tab" href="#pricing-pane" role="tab" aria-controls="pricing-pane" aria-selected="false">
                            <i class="fas fa-coins me-1"></i> Pricing & Stock
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="landing-tab" data-bs-toggle="tab" href="#landing-pane" role="tab" aria-controls="landing-pane" aria-selected="false">
                            <i class="fas fa-layer-group me-1"></i> Landing Page Builder
                        </a>
                    </li>
                </ul>

                <form action="{{ route('ecommerce.admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productMainForm">
                    @csrf
                    <div class="tab-content" id="productFormTabsContent">
                        
                        <!-- Tab 1: General Info -->
                        <div class="tab-pane fade show active" id="general-pane" role="tabpanel" aria-labelledby="general-tab">
                            <div class="row form-row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Product Name</label>
                                        <input type="text" name="name" id="productNameInput" class="form-control" value="{{ old('name') }}" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select name="product_category_id" class="form-control" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                @foreach($category->children as $child)
                                                    <option value="{{ $child->id }}" {{ old('product_category_id') == $child->id ? 'selected' : '' }}>
                                                        &nbsp;&nbsp;&mdash;&nbsp;{{ $child->name }}
                                                    </option>
                                                    @foreach($child->children as $grandchild)
                                                        <option value="{{ $grandchild->id }}" {{ old('product_category_id') == $grandchild->id ? 'selected' : '' }}>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;&mdash;&nbsp;&mdash;&nbsp;{{ $grandchild->name }}
                                                        </option>
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="productImageInput">Primary Image</label>
                                        <input type="file" name="image" class="form-control" id="productImageInput" accept="image/*" required>
                                        <small id="productImageHelper" class="form-text text-muted">Select the main product image. Large files will be compressed automatically before upload.</small>
                                        <div class="image-manager-shell mt-2 single-image-preview" id="productImagePreviewContainer" style="display: none;">
                                            <img id="productImagePreview" src="#" alt="Product Preview" class="img-thumbnail">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Gallery Images</label>
                                        <small class="form-text text-muted mb-2 d-block">Select gallery images one by one. Click the "Add Image" box to select a file. These will appear as thumbnails on the product details page.</small>

                                        <div class="image-manager-shell mt-2">
                                            <!-- Container for hidden dynamic file inputs -->
                                            <div id="galleryInputsContainer" style="display: none;"></div>

                                            <div class="gallery-preview-group">
                                                <span class="gallery-preview-label">New Gallery Uploads</span>
                                                <div id="interactiveGalleryGrid" class="gallery-preview-grid">
                                                    <!-- "+" card is added by Javascript -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">
                                                Featured Product
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Pricing & Stock -->
                        <div class="tab-pane fade" id="pricing-pane" role="tabpanel" aria-labelledby="pricing-tab">
                            <div class="row form-row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Price</label>
                                        <input type="number" step="0.01" name="price" id="productPriceInput" class="form-control" value="{{ old('price') }}" required>
                                    </div>
                                </div>
                                 <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Sale Price (Optional)</label>
                                        <input type="number" step="0.01" name="sale_price" id="productSalePriceInput" class="form-control" value="{{ old('sale_price') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Stock</label>
                                        <input type="number" name="stock" class="form-control" value="{{ old('stock', 0) }}" required>
                                        <small class="text-muted d-block mt-1">Used for simple products. Active variants below will control stock automatically.</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 d-flex align-items-center">
                                    <div class="form-group mb-0">
                                        <div class="form-check form-switch mt-3">
                                            <input class="form-check-input" type="checkbox" id="has_variants_toggle" name="has_variants" value="1" {{ old('has_variants', false) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="has_variants_toggle">This product has variants (Variable Product)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                @include('ecommerce::backend.products.partials.variant-manager', ['variantRows' => $variantRows])
                            </div>
                        </div>

                        <!-- Tab 3: Landing Page Layout -->
                        <div class="tab-pane fade" id="landing-pane" role="tabpanel" aria-labelledby="landing-tab">
                            <div class="row">
                                <div class="col-lg-7 col-12">
                                    <div class="card border-primary shadow-sm" style="border-radius: 12px; overflow: hidden; border-left: 5px solid #007bff;">
                                        <div class="card-header bg-light d-flex align-items-center justify-content-between py-3">
                                            <h4 class="card-title mb-0 text-primary fw-bold" style="font-size: 16px;">
                                                <i class="fas fa-magic me-2"></i> Landing Page Customization (সীমিত সময়ের অফার ও ট্রাস্ট ইনফো)
                                            </h4>
                                            <span class="badge bg-primary text-white">Dynamic Content</span>
                                        </div>
                                        <div class="card-body">
                                            <!-- Countdown Banner -->
                                            <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark" style="font-size: 14px;"><i class="fas fa-clock text-danger me-1"></i> ১. জরুরী অফার কাউন্টডাউন (Urgency Countdown Banner)</h5>
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label class="fw-bold">অফার টাইটেল (Countdown Title)</label>
                                                        <input type="text" name="landing_settings[countdown_title]" id="countdownTitleInput" class="form-control" value="{{ old('landing_settings.countdown_title', 'আজকের বিশেষ ছাড় অফার!') }}" placeholder="আজকের বিশেষ ছাড় অফার!">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label class="fw-bold">অফার সাবটাইটেল (Countdown Subtitle)</label>
                                                        <input type="text" name="landing_settings[countdown_subtitle]" id="countdownSubtitleInput" class="form-control" value="{{ old('landing_settings.countdown_subtitle', 'অফারটি শেষ হতে আর মাত্র সময় বাকি আছে:') }}" placeholder="অফারটি শেষ হতে আর মাত্র সময় বাকি আছে:">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label class="fw-bold">অফার সময় - ঘণ্টায় (Countdown Hours)</label>
                                                        <input type="number" name="landing_settings[countdown_hours]" id="countdownHoursInput" class="form-control" value="{{ old('landing_settings.countdown_hours', 3) }}" placeholder="3">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label class="fw-bold">কাউন্টডাউন স্ট্যাটাস (Countdown Status)</label>
                                                        <select name="landing_settings[show_countdown]" id="countdownStatusSelect" class="form-control form-select">
                                                            <option value="1" {{ old('landing_settings.show_countdown', '1') == '1' ? 'selected' : '' }}>Active (সক্রিয়)</option>
                                                            <option value="0" {{ old('landing_settings.show_countdown', '1') == '0' ? 'selected' : '' }}>Inactive (নিষ্ক্রিয়)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Product Video -->
                                            <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-dark" style="font-size: 14px;"><i class="fab fa-youtube text-danger me-1"></i> ১.৫. প্রোডাক্ট ভিডিও (Product Video - Optional)</h5>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="fw-bold">ইউটিউব ভিডিও লিংক (YouTube Video URL)</label>
                                                        <input type="text" name="landing_settings[youtube_video_url]" id="youtubeVideoUrlInput" class="form-control" value="{{ old('landing_settings.youtube_video_url', '') }}" placeholder="যেমন: https://www.youtube.com/watch?v=xxxxxx">
                                                        <small class="text-muted d-block mt-1">প্রোডাক্টের ব্যবহারবিধি বা রিভিউ ভিডিওর ইউটিউব লিংক এখানে দিন।</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Unified Landing Sections Builder -->
                                            <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-dark" style="font-size: 14px;"><i class="fas fa-layer-group text-primary me-1"></i> ২. ল্যান্ডিং পেজ সেকশন বিল্ডার (Landing Page Sections Builder)</h5>
                                            <div class="row">
                                                <div class="col-12">
                                                    <p class="text-muted small">এখানে আপনি ল্যান্ডিং পেজের সেকশনগুলোর ক্রম পরিবর্তন (Up/Down), নতুন সেকশন যোগ বা ডিলিট করতে পারবেন।</p>
                                                    
                                                    <div id="landing-sections-builder-container">
                                                        <!-- Sections are generated dynamically in JS -->
                                                    </div>

                                                    <div class="card p-3 mt-3 border bg-light shadow-sm" style="border-radius: 12px; border: 1px solid #cbd5e1 !important;">
                                                        <label class="fw-bold mb-2 text-dark">নতুন সেকশন যোগ করুন (Add New Section)</label>
                                                        <div class="d-flex gap-2">
                                                            <select id="new-section-type" class="form-control form-control-sm form-select" style="max-width: 250px;">
                                                                <option value="features">Product Features (ফিচারসমূহ)</option>
                                                                <option value="badges">Trust Badges (পণ্যের ট্রাস্ট ব্যাজসমূহ)</option>
                                                                <option value="problems">User Problems (সমস্যাসমূহ)</option>
                                                                <option value="benefits">Product Benefits (সুবিধাসমূহ)</option>
                                                                <option value="package">Package Includes (প্যাকেজে কী পাবেন)</option>
                                                                <option value="faq">FAQ (প্রশ্ন ও উত্তর)</option>
                                                                <option value="video">Showcase Video (ইউটিউব ভিডিও)</option>
                                                                <option value="gallery">Gallery Showcase (বাস্তব ছবি গ্যালারি)</option>
                                                                <option value="trust">Why Choose Us (কেন আমাদের থেকে কিনবেন)</option>
                                                                <option value="cta">Buy Now Button (অর্ডার বাটন)</option>
                                                                <option value="custom">Custom Section (কাস্টম সেকশন)</option>
                                                                <option value="rich_text">Custom Rich Text (কালার, এইচটিএমএল সহ)</option>
                                                            </select>
                                                            <button type="button" id="add-section-btn" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> সেকশন যোগ করুন (Add Section)</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-12 d-none d-lg-block">
                                    <!-- Mobile Live Preview -->
                                    <div class="device-container shadow">
                                        <div class="device-header-notch"></div>
                                        <div class="device-screen">
                                            <div class="sim-app-header">
                                                <span class="brand">ABCSheba</span>
                                                <div>
                                                    <i class="fas fa-search me-2"></i>
                                                    <i class="fas fa-shopping-cart"></i>
                                                </div>
                                            </div>
                                            
                                            <div class="sim-product-image" id="simProductImage" style="background-image: url('{{ asset('assets/img/products/product.jpg') }}');"></div>
                                            
                                            <div class="sim-product-info-card">
                                                <div class="sim-product-name" id="simProductName">Product Title Preview</div>
                                                <div class="sim-pricing-row">
                                                    <span class="sim-current-price" id="simCurrentPrice">৳0.00</span>
                                                    <span class="sim-old-price" id="simOldPrice" style="display:none;">৳0.00</span>
                                                    <span class="sim-discount-badge" id="simDiscountBadge" style="display:none;">0% OFF</span>
                                                </div>
                                            </div>

                                            <div class="sim-countdown-banner" id="simCountdownBanner">
                                                <div class="sim-countdown-title" id="simCountdownTitle">আজকের বিশেষ ছাড় অফার!</div>
                                                <div class="sim-countdown-subtitle" id="simCountdownSubtitle">অফারটি শেষ হতে আর মাত্র সময় বাকি আছে:</div>
                                                <div class="sim-countdown-timer">
                                                    <div class="sim-timer-box">02</div>
                                                     <div class="sim-timer-box">45</div>
                                                     <div class="sim-timer-box">30</div>
                                                </div>
                                            </div>

                                            <div id="simSectionsContainer"></div>

                                            <div class="sim-buy-button-wrapper">
                                                <div class="sim-buy-button">Buy Now</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Footer Wizard Controls -->
                    <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" id="prevTabBtn" style="display: none;"><i class="fas fa-arrow-left me-1"></i> Previous</button>
                        <button type="button" class="btn btn-primary ms-auto" id="nextTabBtn">Next <i class="fas fa-arrow-right ms-1"></i></button>
                        <button type="submit" class="btn btn-success ms-auto" id="submitProductBtn" style="display: none;"><i class="fas fa-check-circle me-1"></i> Create Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initializeProductImageUpload({
                inputId: 'productImageInput',
                previewId: 'productImagePreview',
                previewContainerId: 'productImagePreviewContainer',
                helperId: 'productImageHelper',
                emptyMessage: 'Select the main product image. Large files will be compressed automatically before upload.'
            });

            initializeInteractiveGalleryUploader({
                gridId: 'interactiveGalleryGrid',
                inputsContainerId: 'galleryInputsContainer'
            });

            // Variant toggle
            const hasVariantsToggle = document.getElementById('has_variants_toggle');
            const variantManagerContainer = document.getElementById('variant-manager-container');
            const variantInputs = variantManagerContainer ? variantManagerContainer.querySelectorAll('input, select, button') : [];

            function toggleVariantFields() {
                if (!hasVariantsToggle || !variantManagerContainer) return;
                const isChecked = hasVariantsToggle.checked;
                variantManagerContainer.style.display = isChecked ? 'block' : 'none';
                variantInputs.forEach(el => {
                    el.disabled = !isChecked;
                });
            }

            if (hasVariantsToggle && variantManagerContainer) {
                hasVariantsToggle.addEventListener('change', toggleVariantFields);
                toggleVariantFields();
            }

            // Unified Sections Builder
            const sectionsContainer = document.getElementById('landing-sections-builder-container');
            const addSectionBtn = document.getElementById('add-section-btn');
            const newSectionTypeSelect = document.getElementById('new-section-type');

            function getSectionCounter() {
                return document.querySelectorAll('.section-card').length;
            }

            function createSectionItemRow(secIndex, value = '') {
                const div = document.createElement('div');
                div.className = 'item-row d-flex align-items-center gap-2 mb-2';
                div.innerHTML = `
                    <textarea name="landing_settings[sections][${secIndex}][items][]" class="form-control form-control-sm" rows="2" placeholder="আইটেমের বিবরণ লিখুন">${value}</textarea>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                `;
                const removeBtn = div.querySelector('.remove-item-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        div.remove();
                    });
                }
                return div;
            }

            function createFAQItemRow(secIndex, faqIndex, q = '', a = '') {
                const div = document.createElement('div');
                div.className = 'faq-item-row border p-2 mb-2 bg-white rounded shadow-sm';
                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-muted">প্রশ্ন ও উত্তর #${faqIndex + 1}</span>
                        <button type="button" class="btn btn-xs btn-outline-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="form-group mb-1">
                        <input type="text" name="landing_settings[sections][${secIndex}][faqs][${faqIndex}][q]" class="form-control form-control-sm" value="${q}" placeholder="প্রশ্ন লিখুন">
                    </div>
                    <div class="form-group mb-0">
                        <textarea name="landing_settings[sections][${secIndex}][faqs][${faqIndex}][a]" class="form-control form-control-sm" rows="2" placeholder="উত্তর লিখুন">${a}</textarea>
                    </div>
                `;
                const removeBtn = div.querySelector('.remove-item-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        div.remove();
                        const card = div.closest('.section-card');
                        if (card) reindexFAQItems(card);
                    });
                }
                return div;
            }

            function reindexFAQItems(sectionCard) {
                const secIndex = parseInt(sectionCard.getAttribute('data-section-index') || '0');
                const faqRows = sectionCard.querySelectorAll('.faq-item-row');
                faqRows.forEach((row, faqIndex) => {
                    const textMuted = row.querySelector('.text-muted');
                    if (textMuted) textMuted.textContent = `প্রশ্ন ও উত্তর #${faqIndex + 1}`;
                    const qInput = row.querySelector('input');
                    if (qInput) qInput.name = `landing_settings[sections][${secIndex}][faqs][${faqIndex}][q]`;
                    const aTextarea = row.querySelector('textarea');
                    if (aTextarea) aTextarea.name = `landing_settings[sections][${secIndex}][faqs][${faqIndex}][a]`;
                });
            }

            function createBadgeItemRow(secIndex, badgeIndex, icon = '', title = '', desc = '') {
                const div = document.createElement('div');
                div.className = 'badge-item-row border p-2 mb-2 bg-white rounded shadow-sm';
                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-muted">ব্যাজ #${badgeIndex + 1}</span>
                        <button type="button" class="btn btn-xs btn-outline-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="row g-1">
                        <div class="col-4">
                            <input type="text" name="landing_settings[sections][${secIndex}][badges][${badgeIndex}][icon]" class="form-control form-control-sm" value="${icon}" placeholder="আইকন ক্লাস">
                        </div>
                        <div class="col-4">
                            <input type="text" name="landing_settings[sections][${secIndex}][badges][${badgeIndex}][title]" class="form-control form-control-sm" value="${title}" placeholder="টাইটেল">
                        </div>
                        <div class="col-4">
                            <input type="text" name="landing_settings[sections][${secIndex}][badges][${badgeIndex}][desc]" class="form-control form-control-sm" value="${desc}" placeholder="বর্ণনা">
                        </div>
                    </div>
                `;
                const removeBtn = div.querySelector('.remove-item-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        div.remove();
                        const card = div.closest('.section-card');
                        if (card) reindexBadgeItems(card);
                    });
                }
                return div;
            }

            function reindexBadgeItems(sectionCard) {
                const secIndex = parseInt(sectionCard.getAttribute('data-section-index') || '0');
                const rows = sectionCard.querySelectorAll('.badge-item-row');
                rows.forEach((row, idx) => {
                    const textMuted = row.querySelector('.text-muted');
                    if (textMuted) textMuted.textContent = `ব্যাজ #${idx + 1}`;
                    
                    const iconInput = row.querySelector('input[name*="[icon]"]');
                    if (iconInput) iconInput.name = `landing_settings[sections][${secIndex}][badges][${idx}][icon]`;
                    
                    const titleInput = row.querySelector('input[name*="[title]"]');
                    if (titleInput) titleInput.name = `landing_settings[sections][${secIndex}][badges][${idx}][title]`;
                    
                    const descInput = row.querySelector('input[name*="[desc]"]');
                    if (descInput) descInput.name = `landing_settings[sections][${secIndex}][badges][${idx}][desc]`;
                });
            }

            function createTrustFeatureItemRow(secIndex, featureIndex, title = '', desc = '') {
                const div = document.createElement('div');
                div.className = 'trust-feature-item-row border p-2 mb-2 bg-white rounded shadow-sm';
                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold text-muted">ফিচার #${featureIndex + 1}</span>
                        <button type="button" class="btn btn-xs btn-outline-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="form-group mb-1">
                        <input type="text" name="landing_settings[sections][${secIndex}][trust_features][${featureIndex}][title]" class="form-control form-control-sm" value="${title}" placeholder="টাইটেল লিখুন">
                    </div>
                    <div class="form-group mb-0">
                        <textarea name="landing_settings[sections][${secIndex}][trust_features][${featureIndex}][desc]" class="form-control form-control-sm" rows="2" placeholder="বর্ণনা লিখুন">${desc}</textarea>
                    </div>
                `;
                const removeBtn = div.querySelector('.remove-item-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        div.remove();
                        const card = div.closest('.section-card');
                        if (card) reindexTrustFeatureItems(card);
                    });
                }
                return div;
            }

            function reindexTrustFeatureItems(sectionCard) {
                const secIndex = parseInt(sectionCard.getAttribute('data-section-index') || '0');
                const rows = sectionCard.querySelectorAll('.trust-feature-item-row');
                rows.forEach((row, idx) => {
                    const textMuted = row.querySelector('.text-muted');
                    if (textMuted) textMuted.textContent = `ফিচার #${idx + 1}`;
                    
                    const titleInput = row.querySelector('input[name*="[title]"]');
                    if (titleInput) titleInput.name = `landing_settings[sections][${secIndex}][trust_features][${idx}][title]`;
                    
                    const descInput = row.querySelector('textarea[name*="[desc]"]');
                    if (descInput) descInput.name = `landing_settings[sections][${secIndex}][trust_features][${idx}][desc]`;
                });
            }

            function createSectionCard(type, index) {
                const card = document.createElement('div');
                card.className = 'section-card card p-3 mb-3 border shadow-sm';
                card.style.borderRadius = '12px';
                card.style.border = '1px solid #cbd5e1';
                card.style.backgroundColor = '#f8fafc';
                card.setAttribute('data-section-type', type);
                card.setAttribute('data-section-index', index);

                const titles = {
                    features: 'আমাদের প্রোডাক্টের বৈশিষ্ট্য',
                    badges: 'আমাদের থেকে কেন কিনবেন?',
                    problems: 'এই সমস্যাগুলো কি আপনারও আছে?',
                    benefits: 'বৈশিষ্ট্যগুলো কি কি জানতে চান?',
                    package: 'প্যাকেজের সাথে যা যা পাবেন',
                    faq: 'কিছু সাধারণ প্রশ্ন',
                    video: 'পণ্যটির বিবরণী ও ব্যবহারবিধি ভিডিও',
                    gallery: 'পণ্যটির কিছু বাস্তব ছবি (Real Gallery)',
                    trust: 'কেন আমাদের থেকে অর্ডার করবেন?',
                    cta: 'অর্ডার করতে এখানে ক্লিক করুন',
                    custom: 'নতুন কাস্টম সেকশন',
                    rich_text: 'কাস্টম রিচ টেক্সট (কালার, এইচটিএমএল সহ)'
                };
                const tags = {
                    features: 'Product Features',
                    badges: 'Trust Badges',
                    problems: 'Common Issues',
                    benefits: 'Benefits',
                    package: 'Package Contents',
                    faq: 'FAQs',
                    video: 'Showcase Video',
                    gallery: 'Showcase',
                    trust: 'Trust',
                    cta: 'Call To Action',
                    custom: 'INFO',
                    rich_text: 'Rich Text'
                };
                const styles = {
                    features: 'blue-check',
                    badges: 'blue-check',
                    problems: 'red-cross',
                    benefits: 'green-check',
                    package: 'package-box',
                    faq: 'faq-accordion',
                    video: 'blue-check',
                    gallery: 'blue-check',
                    trust: 'blue-check',
                    cta: 'blue-check',
                    custom: 'blue-check',
                    rich_text: 'blue-check'
                };

                card.innerHTML = `
                    <input type="hidden" name="landing_settings[sections][${index}][type]" value="${type}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-dark text-white p-2">সেকশন #<span class="sec-number">${index + 1}</span></span>
                            <span class="badge bg-info text-white p-2">${type.toUpperCase()}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-xs btn-outline-secondary move-up-btn" title="উপরে তুলুন"><i class="fas fa-arrow-up"></i></button>
                            <button type="button" class="btn btn-xs btn-outline-secondary move-down-btn" title="নিচে নামান"><i class="fas fa-arrow-down"></i></button>
                            <button type="button" class="btn btn-xs btn-danger remove-section-btn ms-2" title="ডিলিট"><i class="fas fa-trash"></i> ডিলিট</button>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6 col-12">
                            <div class="form-group mb-2">
                                <label class="small fw-bold">সেকশন টাইটেল (Title)</label>
                                <input type="text" name="landing_settings[sections][${index}][title]" class="form-control form-control-sm" value="${titles[type] || 'নতুন সেকশন'}" placeholder="টাইটেল লিখুন">
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-group mb-2">
                                <label class="small fw-bold">সেকশন ট্যাগ (Tag)</label>
                                <input type="text" name="landing_settings[sections][${index}][tag]" class="form-control form-control-sm" value="${tags[type] || 'Section'}" placeholder="ট্যাগ লিখুন">
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-group mb-2">
                                <label class="small fw-bold">স্টাইল / লেআউট (Style)</label>
                                <select name="landing_settings[sections][${index}][style]" class="form-control form-control-sm form-select">
                                    <option value="blue-check" ${styles[type] === 'blue-check' ? 'selected' : ''}>Blue Check Square</option>
                                    <option value="green-check" ${styles[type] === 'green-check' ? 'selected' : ''}>Green Check Circle</option>
                                    <option value="red-cross" ${styles[type] === 'red-cross' ? 'selected' : ''}>Red Cross</option>
                                    <option value="yellow-star" ${styles[type] === 'yellow-star' ? 'selected' : ''}>Yellow Star</option>
                                    <option value="orange-info" ${styles[type] === 'orange-info' ? 'selected' : ''}>Orange Info</option>
                                    <option value="package-box" ${styles[type] === 'package-box' ? 'selected' : ''}>Package Box Style</option>
                                    <option value="faq-accordion" ${styles[type] === 'faq-accordion' ? 'selected' : ''}>FAQ Accordion</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="section-content-container mt-2 border-top pt-2" style="${['video', 'gallery', 'cta'].includes(type) ? 'display: none;' : ''}">
                        <label class="small fw-bold mb-2">
                            ${type === 'faq' ? 'প্রশ্ন ও উত্তরসমূহ (FAQs)' : (type === 'badges' ? 'পণ্যের ট্রাস্ট ব্যাজসমূহ (Badges)' : (type === 'trust' ? 'কেন আমাদের থেকে সংগ্রহ করবেন (Trust Features)' : 'সেকশন আইটেমসমূহ (Items)'))}
                        </label>
                        <div class="items-list"></div>
                        <button type="button" class="btn btn-xs btn-outline-primary add-item-btn mt-1">
                            <i class="fas fa-plus me-1"></i>
                            ${type === 'faq' ? 'প্রশ্ন ও উত্তর যোগ করুন (Add FAQ)' : (type === 'badges' ? 'ব্যাজ যোগ করুন (Add Badge)' : (type === 'trust' ? 'ফিচার যোগ করুন (Add Feature)' : 'আইটেম যোগ করুন (Add Item)'))}
                        </button>
                    </div>
                `;

                bindSectionCardEvents(card);
                return card;
            }

            function bindSectionCardEvents(card) {
                const type = card.getAttribute('data-section-type');

                // Delete section
                const removeBtn = card.querySelector('.remove-section-btn');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        card.remove();
                        reindexSections();
                    });
                }

                // Move up
                const moveUpBtn = card.querySelector('.move-up-btn');
                if (moveUpBtn) {
                    moveUpBtn.addEventListener('click', function() {
                        const prev = card.previousElementSibling;
                        if (prev) {
                            card.parentNode.insertBefore(card, prev);
                            reindexSections();
                        }
                    });
                }

                // Move down
                const moveDownBtn = card.querySelector('.move-down-btn');
                if (moveDownBtn) {
                    moveDownBtn.addEventListener('click', function() {
                        const next = card.nextElementSibling;
                        if (next) {
                            card.parentNode.insertBefore(next, card);
                            reindexSections();
                        }
                    });
                }

                // Add item
                const addItemBtn = card.querySelector('.add-item-btn');
                if (addItemBtn) {
                    addItemBtn.addEventListener('click', function() {
                        const secIndex = parseInt(card.getAttribute('data-section-index') || '0');
                        const itemsList = card.querySelector('.items-list');
                        if (!itemsList) return;
                        
                        if (type === 'faq') {
                            const faqIndex = itemsList.querySelectorAll('.faq-item-row').length;
                            itemsList.appendChild(createFAQItemRow(secIndex, faqIndex));
                            const qInput = itemsList.lastElementChild.querySelector('input');
                            if (qInput) qInput.focus();
                        } else if (type === 'badges') {
                            const badgeIndex = itemsList.querySelectorAll('.badge-item-row').length;
                            itemsList.appendChild(createBadgeItemRow(secIndex, badgeIndex));
                            const iconInput = itemsList.lastElementChild.querySelector('input');
                            if (iconInput) iconInput.focus();
                        } else if (type === 'trust') {
                            const featureIndex = itemsList.querySelectorAll('.trust-feature-item-row').length;
                            itemsList.appendChild(createTrustFeatureItemRow(secIndex, featureIndex));
                            const titleInput = itemsList.lastElementChild.querySelector('input');
                            if (titleInput) titleInput.focus();
                        } else {
                            itemsList.appendChild(createSectionItemRow(secIndex));
                            const textarea = itemsList.lastElementChild.querySelector('textarea');
                            if (textarea) textarea.focus();
                        }
                    });
                }

                // Bind existing inner delete buttons
                card.querySelectorAll('.remove-item-btn').forEach((btn) => {
                    btn.addEventListener('click', function() {
                        const row = btn.closest('.item-row, .faq-item-row, .badge-item-row, .trust-feature-item-row');
                        if (row) {
                            row.remove();
                            if (type === 'faq') {
                                reindexFAQItems(card);
                            } else if (type === 'badges') {
                                reindexBadgeItems(card);
                            } else if (type === 'trust') {
                                reindexTrustFeatureItems(card);
                            }
                        }
                    });
                });
            }

            function reindexSections() {
                const cards = document.querySelectorAll('.section-card');
                cards.forEach((card, index) => {
                    card.setAttribute('data-section-index', index);
                    
                    const secNumSpan = card.querySelector('.sec-number');
                    if (secNumSpan) secNumSpan.textContent = index + 1;

                    const typeInput = card.querySelector('input[type="hidden"]');
                    if (typeInput) typeInput.name = `landing_settings[sections][${index}][type]`;

                    const titleInput = card.querySelector('input[name*="[title]"]');
                    if (titleInput) titleInput.name = `landing_settings[sections][${index}][title]`;

                    const tagInput = card.querySelector('input[name*="[tag]"]');
                    if (tagInput) tagInput.name = `landing_settings[sections][${index}][tag]`;

                    const styleSelect = card.querySelector('select[name*="[style]"]');
                    if (styleSelect) styleSelect.name = `landing_settings[sections][${index}][style]`;

                    const type = card.getAttribute('data-section-type');
                    if (type === 'faq') {
                        reindexFAQItems(card);
                    } else if (type === 'badges') {
                        reindexBadgeItems(card);
                    } else if (type === 'trust') {
                        reindexTrustFeatureItems(card);
                    } else {
                        card.querySelectorAll('.item-row textarea').forEach(textarea => {
                            textarea.name = `landing_settings[sections][${index}][items][]`;
                        });
                    }

                    const addBtn = card.querySelector('.add-item-btn');
                    if (addBtn) {
                        const newAddBtn = addBtn.cloneNode(true);
                        addBtn.parentNode.replaceChild(newAddBtn, addBtn);
                        newAddBtn.addEventListener('click', function() {
                            const itemsList = card.querySelector('.items-list');
                            if (!itemsList) return;
                            
                            if (type === 'faq') {
                                const faqIndex = itemsList.querySelectorAll('.faq-item-row').length;
                                itemsList.appendChild(createFAQItemRow(index, faqIndex));
                                const qInput = itemsList.lastElementChild.querySelector('input');
                                if (qInput) qInput.focus();
                            } else if (type === 'badges') {
                                const badgeIndex = itemsList.querySelectorAll('.badge-item-row').length;
                                itemsList.appendChild(createBadgeItemRow(index, badgeIndex));
                                const iconInput = itemsList.lastElementChild.querySelector('input');
                                if (iconInput) iconInput.focus();
                            } else if (type === 'trust') {
                                const featureIndex = itemsList.querySelectorAll('.trust-feature-item-row').length;
                                itemsList.appendChild(createTrustFeatureItemRow(index, featureIndex));
                                const titleInput = itemsList.lastElementChild.querySelector('input');
                                if (titleInput) titleInput.focus();
                            } else {
                                itemsList.appendChild(createSectionItemRow(index));
                                const textarea = itemsList.lastElementChild.querySelector('textarea');
                                if (textarea) textarea.focus();
                            }
                        });
                    }

                    const moveUpBtn = card.querySelector('.move-up-btn');
                    if (moveUpBtn) moveUpBtn.disabled = (index === 0);
                    
                    const moveDownBtn = card.querySelector('.move-down-btn');
                    if (moveDownBtn) moveDownBtn.disabled = (index === cards.length - 1);
                });
            }

            document.querySelectorAll('.section-card').forEach(card => {
                bindSectionCardEvents(card);
            });
            reindexSections();

            if (addSectionBtn && sectionsContainer) {
                addSectionBtn.addEventListener('click', function() {
                    const type = newSectionTypeSelect.value;
                    const index = getSectionCounter();
                    const newCard = createSectionCard(type, index);
                    sectionsContainer.appendChild(newCard);
                    reindexSections();
                    const titleInput = newCard.querySelector('input[type="text"]');
                    if (titleInput) titleInput.focus();
                });
            }

            // Tab Wizard Controls
            const tabLinks = ['general-tab', 'pricing-tab', 'landing-tab'];
            let currentTabIdx = 0;

            const prevBtn = document.getElementById('prevTabBtn');
            const nextBtn = document.getElementById('nextTabBtn');
            const submitBtn = document.getElementById('submitProductBtn');

            function updateWizardButtons() {
                if (currentTabIdx === 0) {
                    prevBtn.style.display = 'none';
                } else {
                    prevBtn.style.display = 'block';
                }

                if (currentTabIdx === tabLinks.length - 1) {
                    nextBtn.style.display = 'none';
                    submitBtn.style.display = 'block';
                } else {
                    nextBtn.style.display = 'block';
                    submitBtn.style.display = 'none';
                }
            }

            if (nextBtn && prevBtn && submitBtn) {
                nextBtn.addEventListener('click', function() {
                    if (currentTabIdx < tabLinks.length - 1) {
                        currentTabIdx++;
                        document.getElementById(tabLinks[currentTabIdx]).click();
                        updateWizardButtons();
                    }
                });

                prevBtn.addEventListener('click', function() {
                    if (currentTabIdx > 0) {
                        currentTabIdx--;
                        document.getElementById(tabLinks[currentTabIdx]).click();
                        updateWizardButtons();
                    }
                });

                tabLinks.forEach((id, idx) => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('click', function() {
                            currentTabIdx = idx;
                            updateWizardButtons();
                        });
                    }
                });
            }

            // Live Preview Updates
            function updateSimSections() {
                const simContainer = document.getElementById('simSectionsContainer');
                if (!simContainer) return;
                
                simContainer.innerHTML = '';
                
                const sectionCards = document.querySelectorAll('.section-card');
                sectionCards.forEach(card => {
                    const secType = card.getAttribute('data-section-type') || 'custom';
                    const titleInput = card.querySelector('input[name*="[title]"]');
                    const tagInput = card.querySelector('input[name*="[tag]"]');
                    const styleSelect = card.querySelector('select[name*="[style]"]');
                    
                    const title = titleInput ? titleInput.value : '';
                    const tag = tagInput ? tagInput.value : '';
                    const style = styleSelect ? styleSelect.value : 'blue-check';
                    
                    let iconHtml = '<i class="fas fa-check-square text-primary"></i>';
                    if (style === 'green-check') iconHtml = '<i class="fas fa-check-circle text-success"></i>';
                    else if (style === 'red-cross') iconHtml = '<i class="fas fa-times-circle text-danger"></i>';
                    else if (style === 'yellow-star') iconHtml = '<i class="fas fa-star text-warning"></i>';
                    else if (style === 'orange-info') iconHtml = '<i class="fas fa-info-circle text-warning"></i>';
                    else if (style === 'package-box') iconHtml = '<i class="fas fa-box-open text-primary"></i>';

                    const simCard = document.createElement('div');
                    simCard.className = 'sim-section-card';
                    
                    let headerHtml = '';
                    if (tag || title) {
                        headerHtml = `<div class="sim-section-header">
                            ${tag ? `<span class="sim-section-tag bg-light text-primary border">${tag}</span>` : ''}
                            ${title ? `<h6 class="sim-section-title">${title}</h6>` : ''}
                        </div>`;
                    }
                    
                    let contentHtml = '';
                    
                    if (secType === 'faq') {
                        const faqRows = card.querySelectorAll('.faq-item-row');
                        faqRows.forEach(row => {
                            const qInput = row.querySelector('input[name*="[q]"]');
                            const aTextarea = row.querySelector('textarea[name*="[a]"]');
                            const q = qInput ? qInput.value : '';
                            const a = aTextarea ? aTextarea.value : '';
                            if (q || a) {
                                contentHtml += `
                                    <div class="sim-faq-item">
                                        <div class="sim-faq-q"><i class="fas fa-question-circle text-primary me-1"></i>${q}</div>
                                        ${a ? `<div class="sim-faq-a">${a}</div>` : ''}
                                    </div>
                                `;
                            }
                        });
                    } else if (secType === 'badges') {
                        const badgeRows = card.querySelectorAll('.badge-item-row');
                        let badgeItemsHtml = '';
                        badgeRows.forEach(row => {
                            const iconInput = row.querySelector('input[name*="[icon]"]');
                            const titleInput = row.querySelector('input[name*="[title]"]');
                            const descInput = row.querySelector('input[name*="[desc]"]');
                            const icon = iconInput ? (iconInput.value || 'fas fa-shield-alt') : 'fas fa-shield-alt';
                            const bTitle = titleInput ? titleInput.value : '';
                            const bDesc = descInput ? descInput.value : '';
                            if (bTitle) {
                                badgeItemsHtml += `
                                    <div class="sim-badge-item">
                                        <i class="${icon} text-primary"></i>
                                        <div class="sim-badge-title">${bTitle}</div>
                                        ${bDesc ? `<div class="sim-badge-desc">${bDesc}</div>` : ''}
                                    </div>
                                `;
                            }
                        });
                        if (badgeItemsHtml) {
                            contentHtml = `<div class="sim-badge-grid">${badgeItemsHtml}</div>`;
                        }
                    } else if (secType === 'trust') {
                        const trustRows = card.querySelectorAll('.trust-feature-item-row');
                        trustRows.forEach(row => {
                            const tTitleInput = row.querySelector('input[name*="[title]"]');
                            const tDescTextarea = row.querySelector('textarea[name*="[desc]"]');
                            const tTitle = tTitleInput ? tTitleInput.value : '';
                            const tDesc = tDescTextarea ? tDescTextarea.value : '';
                            if (tTitle) {
                                contentHtml += `
                                    <div class="sim-item-row">
                                        <i class="fas fa-shield-alt text-success"></i>
                                        <div>
                                            <strong style="font-size:11px;">${tTitle}</strong>
                                            ${tDesc ? `<div style="font-size:9px; color:#64748b;">${tDesc}</div>` : ''}
                                        </div>
                                    </div>
                                `;
                            }
                        });
                    } else if (secType === 'video') {
                        contentHtml = `
                            <div class="text-center p-2 bg-light rounded border">
                                <i class="fab fa-youtube text-danger fa-2x mb-1"></i>
                                <div style="font-size:10px; font-weight:bold;">Video Showcase</div>
                            </div>
                        `;
                    } else if (secType === 'gallery') {
                        contentHtml = `
                            <div class="d-flex gap-1 overflow-hidden rounded p-1 bg-light border">
                                <div style="width:33%; height:45px; background:#cbd5e1; border-radius:4px;"></div>
                                <div style="width:33%; height:45px; background:#cbd5e1; border-radius:4px;"></div>
                                <div style="width:33%; height:45px; background:#cbd5e1; border-radius:4px;"></div>
                            </div>
                        `;
                    } else {
                        const itemTextareas = card.querySelectorAll('.item-row textarea');
                        itemTextareas.forEach(textarea => {
                            const val = textarea.value;
                            if (val && val.trim()) {
                                contentHtml += `
                                    <div class="sim-item-row">
                                        ${iconHtml}
                                        <span>${val}</span>
                                    </div>
                                `;
                            }
                        });
                    }
                    
                    simCard.innerHTML = headerHtml + contentHtml;
                    simContainer.appendChild(simCard);
                });
            }

            function updateLivePreview() {
                const nameInput = document.getElementById('productNameInput');
                const simName = document.getElementById('simProductName');
                if (nameInput && simName) {
                    simName.textContent = nameInput.value || 'Product Title Preview';
                }

                const priceInput = document.getElementById('productPriceInput');
                const salePriceInput = document.getElementById('productSalePriceInput');
                const simCurrentPrice = document.getElementById('simCurrentPrice');
                const simOldPrice = document.getElementById('simOldPrice');
                const simDiscountBadge = document.getElementById('simDiscountBadge');

                if (priceInput && simCurrentPrice) {
                    const price = parseFloat(priceInput.value) || 0;
                    const salePrice = parseFloat(salePriceInput ? salePriceInput.value : '0') || 0;

                    if (salePrice > 0 && salePrice < price) {
                        simCurrentPrice.textContent = '৳' + salePrice.toFixed(2);
                        if (simOldPrice) {
                            simOldPrice.textContent = '৳' + price.toFixed(2);
                            simOldPrice.style.display = 'inline';
                        }
                        if (simDiscountBadge) {
                            const discount = Math.round(((price - salePrice) / price) * 100);
                            simDiscountBadge.textContent = discount + '% OFF';
                            simDiscountBadge.style.display = 'inline';
                        }
                    } else {
                        simCurrentPrice.textContent = '৳' + price.toFixed(2);
                        if (simOldPrice) simOldPrice.style.display = 'none';
                        if (simDiscountBadge) simDiscountBadge.style.display = 'none';
                    }
                }

                const countdownStatus = document.getElementById('countdownStatusSelect');
                const simCountdownBanner = document.getElementById('simCountdownBanner');
                if (countdownStatus && simCountdownBanner) {
                    if (countdownStatus.value === '1') {
                        simCountdownBanner.style.display = 'block';
                        const titleInput = document.getElementById('countdownTitleInput');
                        const subtitleInput = document.getElementById('countdownSubtitleInput');
                        const simTitle = document.getElementById('simCountdownTitle');
                        const simSubtitle = document.getElementById('simCountdownSubtitle');
                        
                        if (titleInput && simTitle) simTitle.textContent = titleInput.value || 'আজকের বিশেষ ছাড় অফার!';
                        if (subtitleInput && simSubtitle) simSubtitle.textContent = subtitleInput.value || 'অফারটি শেষ হতে আর মাত্র সময় বাকি আছে:';
                    } else {
                        simCountdownBanner.style.display = 'none';
                    }
                }

                const imageInput = document.getElementById('productImageInput');
                const simProductImage = document.getElementById('simProductImage');
                if (imageInput && simProductImage && imageInput.files && imageInput.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        simProductImage.style.backgroundImage = `url('${e.target.result}')`;
                    }
                    reader.readAsDataURL(imageInput.files[0]);
                }

                updateSimSections();
            }

            const previewSelectors = [
                '#productNameInput',
                '#productPriceInput',
                '#productSalePriceInput',
                '#countdownTitleInput',
                '#countdownSubtitleInput',
                '#countdownStatusSelect'
            ];
            
            previewSelectors.forEach(selector => {
                const el = document.querySelector(selector);
                if (el) {
                    el.addEventListener('input', updateLivePreview);
                    el.addEventListener('change', updateLivePreview);
                }
            });

            const builderContainer = document.getElementById('landing-sections-builder-container');
            if (builderContainer) {
                builderContainer.addEventListener('input', updateLivePreview);
                builderContainer.addEventListener('change', updateLivePreview);
                builderContainer.addEventListener('click', updateLivePreview);
            }

            document.getElementById('productImageInput')?.addEventListener('change', updateLivePreview);
            setTimeout(updateLivePreview, 500);
        });

        function initializeProductImageUpload({ inputId, previewId, previewContainerId, helperId, emptyMessage }) {
            const fileInput = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const previewContainer = document.getElementById(previewContainerId);
            const helper = document.getElementById(helperId);

            if (!fileInput || !preview || !previewContainer || !helper) return;

            fileInput.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (!file) {
                    preview.src = '#';
                    previewContainer.style.display = 'none';
                    helper.textContent = emptyMessage;
                    return;
                }
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    event.target.value = '';
                    preview.src = '#';
                    previewContainer.style.display = 'none';
                    return;
                }
                preview.src = URL.createObjectURL(file);
                previewContainer.style.display = 'block';
                helper.innerHTML = `<span class="text-success">Image selected. Size: ${(file.size / 1024).toFixed(2)} KB.</span>`;
            });
        }

        function initializeInteractiveGalleryUploader({ gridId, inputsContainerId }) {
            const grid = document.getElementById(gridId);
            const inputsContainer = document.getElementById(inputsContainerId);
            if (!grid || !inputsContainer) return;

            inputsContainer.innerHTML = '';
            grid.innerHTML = '';
            let imageCounter = 0;

            function createAddCard() {
                const addCard = document.createElement('div');
                addCard.className = 'gallery-add-card';
                addCard.innerHTML = `
                    <i class="fas fa-plus-circle"></i>
                    <span>Add Image</span>
                `;
                addCard.addEventListener('click', function () {
                    imageCounter++;
                    const fileInput = document.createElement('input');
                    fileInput.type = 'file';
                    fileInput.name = 'gallery[]';
                    fileInput.accept = 'image/*';
                    fileInput.style.display = 'none';
                    fileInput.id = `gallery_input_${imageCounter}`;

                    fileInput.addEventListener('change', function (event) {
                        const file = event.target.files[0];
                        if (!file) {
                            fileInput.remove();
                            return;
                        }
                        if (!file.type.startsWith('image/')) {
                            alert('Please select a valid image file.');
                            fileInput.remove();
                            return;
                        }
                        inputsContainer.appendChild(fileInput);

                        const previewCard = document.createElement('div');
                        previewCard.className = 'gallery-preview-card';
                        previewCard.innerHTML = `
                            <img src="${URL.createObjectURL(file)}" alt="Gallery Preview">
                            <div class="gallery-preview-meta">
                                <strong>${file.name}</strong>
                                <small>${(file.size / 1024).toFixed(1)} KB</small>
                            </div>
                            <div class="gallery-preview-actions">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100">Remove</button>
                            </div>
                        `;
                        previewCard.querySelector('button').addEventListener('click', function () {
                            fileInput.remove();
                            previewCard.remove();
                        });

                        grid.insertBefore(previewCard, addCard);
                    });
                    fileInput.click();
                });
                return addCard;
            }
            grid.appendChild(createAddCard());
        }
    </script>
@endpush
