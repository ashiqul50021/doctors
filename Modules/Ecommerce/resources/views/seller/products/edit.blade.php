@extends('layouts.admin')

@push('styles')
    <style>
        .ck-editor__editable_inline {
            min-height: 220px;
            max-height: 500px;
        }
    </style>
@endpush

@include('ecommerce::backend.products.partials.image-manager-styles')
@include('ecommerce::backend.products.partials.product-manager-styles')

@section('title', 'Edit Product - Seller')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Edit Product</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.seller.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.seller.products.index') }}">Products</a></li>
                <li class="breadcrumb-item active">Edit Product</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        @php
            $variantRows = old('variants', $product->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'option_name' => $variant->option_name,
                    'option_value' => $variant->option_value,
                    'price' => $variant->price,
                    'sale_price' => $variant->sale_price,
                    'stock' => $variant->stock,
                    'sku' => $variant->sku,
                    'is_active' => $variant->is_active,
                ];
            })->all());
            $existingGalleryImages = collect($product->gallery ?? [])
                ->reject(fn ($path) => collect(old('removed_gallery', []))->contains($path))
                ->filter()
                ->values();
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

                <form action="{{ route('ecommerce.seller.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productMainForm">
                    @csrf
                    @method('PUT')
                    <div class="tab-content" id="productFormTabsContent">
                        
                        <!-- Tab 1: General Info -->
                        <div class="tab-pane fade show active" id="general-pane" role="tabpanel" aria-labelledby="general-tab">
                            <div class="row form-row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Product Name</label>
                                        <input type="text" name="name" id="productNameInput" class="form-control" value="{{ old('name', $product->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select name="product_category_id" class="form-control" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('product_category_id', $product->product_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                @foreach($category->children as $child)
                                                    <option value="{{ $child->id }}" {{ old('product_category_id', $product->product_category_id) == $child->id ? 'selected' : '' }}>
                                                        &nbsp;&nbsp;&mdash;&nbsp;{{ $child->name }}
                                                    </option>
                                                    @foreach($child->children as $grandchild)
                                                        <option value="{{ $grandchild->id }}" {{ old('product_category_id', $product->product_category_id) == $grandchild->id ? 'selected' : '' }}>
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
                                        <input type="file" name="image" class="form-control" id="productImageInput" accept="image/*">
                                        <small id="productImageHelper" class="form-text text-muted">Choose a new main image to replace the current one. Selected image will preview instantly and be compressed before upload.</small>
                                        <div class="image-manager-shell mt-2 single-image-preview" id="productImagePreviewContainer" style="{{ $product->image ? '' : 'display: none;' }}">
                                            <img id="productImagePreview" src="{{ $product->image ? (\Illuminate\Support\Str::startsWith($product->image, ['http://', 'https://']) ? $product->image : asset($product->image)) : '#' }}" alt="Product Preview" class="img-thumbnail">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Gallery Images</label>
                                        <small class="form-text text-muted mb-2 d-block">Select gallery images one by one. Click the "Add Image" box to select a file. These will appear as thumbnails on the product details page.</small>

                                        @if($existingGalleryImages->count() > 0)
                                            <div id="existingGallerySection" class="gallery-preview-group mb-3">
                                                <span class="gallery-preview-label">Existing Gallery Images</span>
                                                <div id="existingGalleryGrid" class="gallery-preview-grid">
                                                    @foreach($existingGalleryImages as $img)
                                                        <div class="gallery-preview-card" data-gallery-path="{{ $img }}">
                                                            <img src="{{ \Illuminate\Support\Str::startsWith($img, ['http://', 'https://']) ? $img : asset($img) }}" alt="Gallery Image">
                                                            <div class="gallery-preview-actions">
                                                                <button type="button" class="btn btn-sm btn-outline-danger w-100 js-remove-existing-gallery" data-path="{{ $img }}">
                                                                    Remove
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <div id="removedGalleryInputs" style="display: none;"></div>

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
                                        <label class="fw-bold">Product Description (প্রোডাক্ট বিবরণী - Text Editor)</label>
                                        <textarea name="description" id="productDescriptionEditor" class="form-control" rows="5" placeholder="প্রোডাক্টের বিস্তারিত বিবরণী লিখুন...">{{ old('description', $product->description) }}</textarea>
                                        <small class="text-muted">প্রোডাক্টের বিস্তারিত বিবরণ, বৈশিষ্ট্য ও ব্যবহারবিধি টেক্সট এডিটরে সুন্দরভাবে সাজিয়ে লিখুন।</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="is_active">
                                                Active Product (সক্রিয় / প্রকাশিত প্রোডাক্ট)
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
                                        <input type="number" step="0.01" name="price" id="productPriceInput" class="form-control" value="{{ old('price', $product->price) }}" required>
                                    </div>
                                </div>
                                 <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="mb-0">Sale Price (Optional)</label>
                                            <span id="liveDiscountBadge" class="badge bg-danger text-white fs-6 px-2 py-1" style="display: none;">0% OFF</span>
                                        </div>
                                        <input type="number" step="0.01" name="sale_price" id="productSalePriceInput" class="form-control" value="{{ old('sale_price', $product->sale_price) }}">
                                        <small class="text-muted d-block mt-1">কাস্টমারকে বিশেষ অফার বা ছাড় দিতে নিয়মিত মূল্যের চেয়ে কম ডিসকাউন্ট প্রাইস লিখুন।</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>Stock</label>
                                        <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock ?? 0) }}" required>
                                        <small class="text-muted d-block mt-1">Used for simple products. Active variants below will control stock automatically.</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 d-flex align-items-center">
                                    <div class="form-group mb-0">
                                        <div class="form-check form-switch mt-3">
                                            <input class="form-check-input" type="checkbox" id="has_variants" name="has_variants" value="1" {{ old('has_variants', $product->has_variants) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="has_variants">This product has variants (Variable Product)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Shipping Fee Option -->
                            <div class="row form-row mt-3 pt-3 border-top">
                                <div class="col-12">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="override_shipping" name="override_shipping" value="1" {{ old('override_shipping', $product->override_shipping) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-dark" for="override_shipping">
                                            <i class="fas fa-truck me-1 text-primary"></i> এই প্রোডাক্টের জন্য কাস্টম শিপিং চার্জ নির্ধারণ করুন (Override Global Shipping)
                                        </label>
                                        <small class="text-muted d-block ms-4">সুইচ অফ থাকলে সাইটের গ্লোবাল ডিফল্ট শিপিং চার্জ (ঢাকার ভেতরে ৳৮০, বাইরে ৳১৩০) প্রযোজ্য হবে।</small>
                                    </div>
                                </div>
                                <div class="col-12" id="custom-shipping-container" style="{{ old('override_shipping', $product->override_shipping) ? 'display: block;' : 'display: none;' }}">
                                    <div class="row bg-light p-3 rounded border">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group mb-2 mb-md-0">
                                                <label class="fw-bold small">ঢাকার ভেতরে শিপিং ফি (Inside Dhaka Charge - ৳)</label>
                                                <input type="number" step="0.01" name="inside_dhaka_charge" class="form-control" value="{{ old('inside_dhaka_charge', $product->inside_dhaka_charge) }}" placeholder="যেমন: 80 বা 0 (ফ্রি)">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="fw-bold small">ঢাকার বাইরে শিপিং ফি (Outside Dhaka Charge - ৳)</label>
                                                <input type="number" step="0.01" name="outside_dhaka_charge" class="form-control" value="{{ old('outside_dhaka_charge', $product->outside_dhaka_charge) }}" placeholder="যেমন: 130 বা 0 (ফ্রি)">
                                            </div>
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
                                                <i class="fas fa-palette me-2"></i> প্রোডাক্ট পেজ ডিজাইন ও সেকশন বিল্ডার
                                            </h4>
                                            <span class="badge bg-primary text-white">Custom Details Page</span>
                                        </div>
                                        <div class="card-body">
                                            <!-- Global Product Details Page Color Settings -->
                                            <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark" style="font-size: 15px;">
                                                <i class="fas fa-fill-drip text-primary me-1"></i> ১. সম্পূর্ণ প্রোডাক্ট পেজের কাস্টম কালার (Page Styling)
                                            </h5>
                                            <div class="row mb-4 p-3 rounded border bg-light">
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group mb-2">
                                                        <label class="fw-bold small">পুরো পেজের ব্যাকগ্রাউন্ড কালার (Page Background)</label>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <input type="color" name="landing_settings[page_bg_color]" id="pageBgColorInput" class="form-control form-control-color" value="{{ old('landing_settings.page_bg_color', $product->landing_settings['page_bg_color'] ?? '#ffffff') }}" style="width: 60px; height: 38px;">
                                                            <input type="text" class="form-control form-control-sm" value="{{ old('landing_settings.page_bg_color', $product->landing_settings['page_bg_color'] ?? '#ffffff') }}" onchange="document.getElementById('pageBgColorInput').value=this.value">
                                                        </div>
                                                        <small class="text-muted">ডিফল্ট: সাদা (#ffffff) অথবা আপনার পছন্দের যেকোনো ব্যাকগ্রাউন্ড কালার সিলেক্ট করুন।</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group mb-2">
                                                        <label class="fw-bold small">পুরো পেজের মূল টেক্সট কালার (Page Text Color)</label>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <input type="color" name="landing_settings[page_text_color]" id="pageTextColorInput" class="form-control form-control-color" value="{{ old('landing_settings.page_text_color', $product->landing_settings['page_text_color'] ?? '#1e293b') }}" style="width: 60px; height: 38px;">
                                                            <input type="text" class="form-control form-control-sm" value="{{ old('landing_settings.page_text_color', $product->landing_settings['page_text_color'] ?? '#1e293b') }}" onchange="document.getElementById('pageTextColorInput').value=this.value">
                                                        </div>
                                                        <small class="text-muted">ডিফল্ট: ডার্ক স্লেট (#1e293b) অথবা আপনার ব্যাকগ্রাউন্ড অনুযায়ী টেক্সট কালার দিন।</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Unified Dynamic Sections Builder -->
                                            <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-dark" style="font-size: 15px;">
                                                <i class="fas fa-layer-group text-primary me-1"></i> ২. ডাইনামিক সেকশন বিল্ডার (Dynamic Sections)
                                            </h5>
                                            <div class="row">
                                                <div class="col-12">
                                                    <p class="text-muted small">এখানে আপনি ট্রাস্ট ব্যাজ, প্রশ্নোত্তর (QnA), গ্যালারি, ইউটিউব ভিডিও, কাস্টম সেকশন, অর্ডার ফর্ম এবং রিভিউ ফর্ম যোগ, সক্রিয়/নিষ্ক্রিয় ও সাজাতে পারবেন।</p>
                                                    
                                                    <div id="landing-sections-builder-container">
                                                        @php
                                                            $sections = $product->landing_settings['sections'] ?? [];
                                                            if (!is_array($sections)) $sections = [];
                                                            $typeLabels = [
                                                                'badges' => '🛡️ Trust Badges',
                                                                'faq' => '❓ QnA / FAQ',
                                                                'gallery' => '🖼️ Photo Gallery',
                                                                'video' => '🎥 YouTube Video',
                                                                'custom' => '📝 Custom Section',
                                                                'order_form' => '🛒 Custom Order Form',
                                                                'reviews' => '⭐ Customer Reviews'
                                                            ];
                                                        @endphp

                                                        @foreach($sections as $secIdx => $sec)
                                                            @php
                                                                $secType = $sec['type'] ?? 'custom';
                                                                $secTitle = $sec['title'] ?? '';
                                                                $secTag = $sec['tag'] ?? '';
                                                                $isActive = $sec['is_active'] ?? '1';
                                                                $bgColor = $sec['bg_color'] ?? '#ffffff';
                                                                $textColor = $sec['text_color'] ?? '#1e293b';
                                                                $fontSize = $sec['font_size'] ?? '16px';
                                                                $lineHeight = $sec['line_height'] ?? '1.6';
                                                                $showBtn = $sec['show_button'] ?? '0';
                                                                $btnText = $sec['button_text'] ?? 'অর্ডার করতে ক্লিক করুন';
                                                            @endphp
                                                            <div class="section-card card p-3 mb-3 border shadow-sm" data-section-type="{{ $secType }}" data-section-index="{{ $secIdx }}" style="border-radius: 12px; border: 1px solid #cbd5e1 !important; background-color: #f8fafc;">
                                                                <input type="hidden" name="landing_settings[sections][{{ $secIdx }}][type]" value="{{ $secType }}">
                                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <span class="badge bg-dark text-white p-2">সেকশন #<span class="sec-number">{{ $secIdx + 1 }}</span></span>
                                                                        <span class="badge bg-primary text-white p-2">{{ $typeLabels[$secType] ?? strtoupper($secType) }}</span>
                                                                    </div>
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <button type="button" class="btn btn-xs btn-outline-secondary move-up-btn" title="উপরে তুলুন"><i class="fas fa-arrow-up"></i></button>
                                                                        <button type="button" class="btn btn-xs btn-outline-secondary move-down-btn" title="নিচে নামান"><i class="fas fa-arrow-down"></i></button>
                                                                        <button type="button" class="btn btn-xs btn-danger remove-section-btn ms-2" title="ডিলিট"><i class="fas fa-trash"></i> ডিলিট</button>
                                                                    </div>
                                                                </div>

                                                                <!-- Row 1: Active Status, Title, Tag -->
                                                                <div class="row g-2 mb-2">
                                                                    <div class="col-md-3 col-6">
                                                                        <div class="form-group mb-2">
                                                                            <label class="small fw-bold">সেকশন স্ট্যাটাস (Status)</label>
                                                                            <select name="landing_settings[sections][{{ $secIdx }}][is_active]" class="form-control form-control-sm form-select">
                                                                                <option value="1" {{ $isActive == '1' ? 'selected' : '' }}>Active (সক্রিয়)</option>
                                                                                <option value="0" {{ $isActive == '0' ? 'selected' : '' }}>Inactive (নিষ্ক্রিয়)</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6 col-12">
                                                                        <div class="form-group mb-2">
                                                                            <label class="small fw-bold">সেকশন টাইটেল (Title)</label>
                                                                            <input type="text" name="landing_settings[sections][{{ $secIdx }}][title]" class="form-control form-control-sm" value="{{ $secTitle }}" placeholder="টাইটেল লিখুন">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3 col-6">
                                                                        <div class="form-group mb-2">
                                                                            <label class="small fw-bold">সেকশন ট্যাগ (Tag/Badge)</label>
                                                                            <input type="text" name="landing_settings[sections][{{ $secIdx }}][tag]" class="form-control form-control-sm" value="{{ $secTag }}" placeholder="যেমন: FAQs / Trust">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Row 2: Background, Text Color, Font Size, Line Height -->
                                                                <div class="row g-2 mb-2 p-2 rounded border bg-light">
                                                                    <div class="col-md-3 col-6">
                                                                        <div class="form-group mb-1">
                                                                            <label class="small fw-bold">ব্যাকগ্রাউন্ড (Background)</label>
                                                                            <input type="color" name="landing_settings[sections][{{ $secIdx }}][bg_color]" class="form-control form-control-sm form-control-color w-100" value="{{ $bgColor }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3 col-6">
                                                                        <div class="form-group mb-1">
                                                                            <label class="small fw-bold">টেক্সট কালার (Text Color)</label>
                                                                            <input type="color" name="landing_settings[sections][{{ $secIdx }}][text_color]" class="form-control form-control-sm form-control-color w-100" value="{{ $textColor }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3 col-6">
                                                                        <div class="form-group mb-1">
                                                                            <label class="small fw-bold">ফন্ট সাইজ (Font Size)</label>
                                                                            <select name="landing_settings[sections][{{ $secIdx }}][font_size]" class="form-control form-control-sm form-select">
                                                                                <option value="14px" {{ $fontSize == '14px' ? 'selected' : '' }}>Small (14px)</option>
                                                                                <option value="16px" {{ $fontSize == '16px' ? 'selected' : '' }}>Normal (16px)</option>
                                                                                <option value="18px" {{ $fontSize == '18px' ? 'selected' : '' }}>Medium (18px)</option>
                                                                                <option value="20px" {{ $fontSize == '20px' ? 'selected' : '' }}>Large (20px)</option>
                                                                                <option value="24px" {{ $fontSize == '24px' ? 'selected' : '' }}>Extra Large (24px)</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3 col-6">
                                                                        <div class="form-group mb-1">
                                                                            <label class="small fw-bold">টেক্সট হাইট (Line Height)</label>
                                                                            <select name="landing_settings[sections][{{ $secIdx }}][line_height]" class="form-control form-control-sm form-select">
                                                                                <option value="1.4" {{ $lineHeight == '1.4' ? 'selected' : '' }}>Tight (1.4)</option>
                                                                                <option value="1.6" {{ $lineHeight == '1.6' ? 'selected' : '' }}>Normal (1.6)</option>
                                                                                <option value="1.8" {{ $lineHeight == '1.8' ? 'selected' : '' }}>Relaxed (1.8)</option>
                                                                                <option value="2.0" {{ $lineHeight == '2.0' ? 'selected' : '' }}>Loose (2.0)</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Row 3: Add Buy Now Button Option -->
                                                                <div class="row g-2 mb-2 p-2 rounded border bg-light">
                                                                    <div class="col-md-4 col-12">
                                                                        <div class="form-group mb-1">
                                                                            <label class="small fw-bold">বাই নাও বাটন যুক্ত করুন (Add Buy Now Button)</label>
                                                                            <select name="landing_settings[sections][{{ $secIdx }}][show_button]" class="form-control form-control-sm form-select">
                                                                                <option value="0" {{ $showBtn == '0' ? 'selected' : '' }}>না (No Button)</option>
                                                                                <option value="1" {{ $showBtn == '1' ? 'selected' : '' }}>হ্যাঁ, বাটন দেখাও (Show Buy Now Button)</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-8 col-12">
                                                                        <div class="form-group mb-1">
                                                                            <label class="small fw-bold">বাটন টেক্সট (Button Text)</label>
                                                                            <input type="text" name="landing_settings[sections][{{ $secIdx }}][button_text]" class="form-control form-control-sm" value="{{ $btnText }}" placeholder="যেমন: অর্ডার করতে ক্লিক করুন">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Section Content Container -->
                                                                <div class="section-content-container mt-2 border-top pt-2">
                                                                    @if($secType === 'gallery')
                                                                        <label class="small fw-bold mb-2">গ্যালারি ছবিসমূহ (Gallery Image Links / URLs)</label>
                                                                        <div class="gallery-items-list border rounded p-2 bg-white">
                                                                            @php
                                                                                $secGalleryImages = $sec['images'] ?? [];
                                                                            @endphp
                                                                            @foreach($secGalleryImages as $imgUrl)
                                                                                <div class="gallery-image-row d-flex align-items-center gap-2 mb-2 p-2 rounded border bg-light">
                                                                                    <div class="preview-box flex-shrink-0" style="width: 50px; height: 50px; background: #cbd5e1; border-radius: 6px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                                                        <img src="{{ $imgUrl }}" class="img-preview" style="width: 100%; height: 100%; object-fit: cover;">
                                                                                    </div>
                                                                                    <div class="flex-grow-1">
                                                                                        <input type="text" name="landing_settings[sections][{{ $secIdx }}][images][]" class="form-control form-control-sm gallery-url-input" value="{{ $imgUrl }}" placeholder="ছবির লিংক/ইউআরএল (যেমন: https://... বা /uploads/...)">
                                                                                    </div>
                                                                                    <button type="button" class="btn btn-xs btn-danger remove-item-btn" title="মুছে ফেলুন"><i class="fas fa-trash"></i></button>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <button type="button" class="btn btn-xs btn-outline-primary add-gallery-item-btn mt-2">
                                                                            <i class="fas fa-plus me-1"></i> ছবি যোগ করুন (Add Photo URL)
                                                                        </button>
                                                                    @elseif($secType === 'video')
                                                                        <div class="form-group mb-2">
                                                                            <label class="small fw-bold">ইউটিউব ভিডিও লিংক (YouTube Video URL)</label>
                                                                            <input type="text" name="landing_settings[sections][{{ $secIdx }}][youtube_video_url]" class="form-control form-control-sm" value="{{ $sec['youtube_video_url'] ?? '' }}" placeholder="যেমন: https://www.youtube.com/watch?v=xxxxxx">
                                                                            <small class="text-muted">ইউটিউব ভিডিওর সম্পূর্ণ লিংকটি এখানে দিন।</small>
                                                                        </div>
                                                                    @elseif($secType === 'faq')
                                                                        <label class="small fw-bold mb-2">প্রশ্ন ও উত্তরসমূহ (FAQs)</label>
                                                                        <div class="items-list">
                                                                            @php
                                                                                $faqItems = $sec['faqs'] ?? [];
                                                                            @endphp
                                                                            @foreach($faqItems as $itemIdx => $faq)
                                                                                <div class="faq-item-row border p-2 mb-2 bg-white rounded shadow-sm">
                                                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                        <span class="small fw-bold text-muted">প্রশ্ন ও উত্তর #{{ $itemIdx + 1 }}</span>
                                                                                        <button type="button" class="btn btn-xs btn-outline-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                                                                                    </div>
                                                                                    <div class="form-group mb-1">
                                                                                        <input type="text" name="landing_settings[sections][{{ $secIdx }}][faqs][{{ $itemIdx }}][q]" class="form-control form-control-sm" value="{{ $faq['q'] ?? '' }}" placeholder="প্রশ্ন লিখুন">
                                                                                    </div>
                                                                                    <div class="form-group mb-0">
                                                                                        <textarea name="landing_settings[sections][{{ $secIdx }}][faqs][{{ $itemIdx }}][a]" class="form-control form-control-sm" rows="2" placeholder="উত্তর লিখুন">{{ $faq['a'] ?? '' }}</textarea>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <button type="button" class="btn btn-xs btn-outline-primary add-item-btn mt-1"><i class="fas fa-plus me-1"></i> প্রশ্ন ও উত্তর যোগ করুন (Add FAQ)</button>
                                                                    @elseif($secType === 'badges')
                                                                        <label class="small fw-bold mb-2">পণ্যের ট্রাস্ট ব্যাজসমূহ (Badges)</label>
                                                                        <div class="items-list">
                                                                            @php
                                                                                $secBadges = $sec['badges'] ?? [];
                                                                            @endphp
                                                                            @foreach($secBadges as $itemIdx => $badge)
                                                                                <div class="badge-item-row border p-2 mb-2 bg-white rounded shadow-sm">
                                                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                        <span class="small fw-bold text-muted">ব্যাজ #{{ $itemIdx + 1 }}</span>
                                                                                        <button type="button" class="btn btn-xs btn-outline-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                                                                                    </div>
                                                                                    <div class="row g-1">
                                                                                        <div class="col-4">
                                                                                            <input type="text" name="landing_settings[sections][{{ $secIdx }}][badges][{{ $itemIdx }}][icon]" class="form-control form-control-sm" value="{{ $badge['icon'] ?? '' }}" placeholder="আইকন (যেমন: fas fa-shield-alt)">
                                                                                        </div>
                                                                                        <div class="col-4">
                                                                                            <input type="text" name="landing_settings[sections][{{ $secIdx }}][badges][{{ $itemIdx }}][title]" class="form-control form-control-sm" value="{{ $badge['title'] ?? '' }}" placeholder="টাইটেল">
                                                                                        </div>
                                                                                        <div class="col-4">
                                                                                            <input type="text" name="landing_settings[sections][{{ $secIdx }}][badges][{{ $itemIdx }}][desc]" class="form-control form-control-sm" value="{{ $badge['desc'] ?? '' }}" placeholder="বর্ণনা">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <button type="button" class="btn btn-xs btn-outline-primary add-item-btn mt-1"><i class="fas fa-plus me-1"></i> ব্যাজ যোগ করুন (Add Badge)</button>
                                                                    @elseif($secType === 'custom')
                                                                        <div class="form-group mb-2">
                                                                            <label class="small fw-bold">সংক্ষিপ্ত বিবরণ (Short Description)</label>
                                                                            <textarea name="landing_settings[sections][{{ $secIdx }}][short_desc]" class="form-control form-control-sm mb-2" rows="2" placeholder="সেকশনের সংক্ষিপ্ত বিবরণ লিখুন...">{{ $sec['short_desc'] ?? '' }}</textarea>
                                                                        </div>
                                                                        <label class="small fw-bold mb-2">পয়েন্ট / আইটেমসমূহ (Item Points)</label>
                                                                        <div class="items-list">
                                                                            @php
                                                                                $secItems = $sec['items'] ?? [];
                                                                            @endphp
                                                                            @foreach($secItems as $itemVal)
                                                                                <div class="item-row d-flex align-items-center gap-2 mb-2">
                                                                                    <textarea name="landing_settings[sections][{{ $secIdx }}][items][]" class="form-control form-control-sm" rows="2" placeholder="পয়েন্ট / আইটেমের বিবরণ লিখুন">{{ $itemVal }}</textarea>
                                                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                        <button type="button" class="btn btn-xs btn-outline-primary add-item-btn mt-1"><i class="fas fa-plus me-1"></i> পয়েন্ট যোগ করুন (Add Point)</button>
                                                                    @elseif($secType === 'order_form')
                                                                        <div class="alert alert-info py-2 px-3 small mb-0">
                                                                            <i class="fas fa-info-circle me-1"></i> এই সেকশনটি ফ্রন্টএন্ডে সরাসরি ক্যাশ অন ডেলিভারি (Name, Phone, Address, Delivery Area) অর্ডার ফর্ম হিসেবে রেন্ডার হবে।
                                                                        </div>
                                                                    @elseif($secType === 'reviews')
                                                                        <div class="alert alert-info py-2 px-3 small mb-0">
                                                                            <i class="fas fa-info-circle me-1"></i> এই সেকশনটি ফ্রন্টএন্ডে কাস্টমারদের রিভিউ ফর্ম এবং অনুমোদিত রিভিউ তালিকা হিসেবে রেন্ডার হবে।
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <div class="card p-3 mt-3 border text-center shadow-sm" style="border-radius: 12px; border: 1px dashed #cbd5e1 !important; background-color: #f8fafc;">
                                                        <p class="text-muted small mb-2">আপনার প্রোডাক্ট পেজে নতুন সেকশন যোগ করতে নিচের বাটনে ক্লিক করুন</p>
                                                        <div>
                                                            <button type="button" id="openSectionPickerModalBtn" class="btn btn-primary px-4 py-2 fw-semibold" style="border-radius: 8px; background-color: #2563eb;">
                                                                <i class="fas fa-plus-circle me-2"></i> সেকশন যোগ করুন (Add Section)
                                                            </button>
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
                                            
                                            <div class="sim-product-image" id="simProductImage" style="background-image: url('{{ $product->image ? asset($product->image) : asset('assets/img/products/product.jpg') }}');"></div>
                                            
                                            <div class="sim-product-info-card">
                                                <div class="sim-product-name" id="simProductName">{{ $product->name }}</div>
                                                <div class="sim-pricing-row">
                                                    <span class="sim-current-price" id="simCurrentPrice">৳{{ number_format($product->sale_price ?? $product->price, 2) }}</span>
                                                    <span class="sim-old-price" id="simOldPrice" style="{{ $product->sale_price ? '' : 'display:none;' }}">৳{{ number_format($product->price, 2) }}</span>
                                                    <span class="sim-discount-badge" id="simDiscountBadge" style="{{ $product->sale_price ? '' : 'display:none;' }}">SAVE</span>
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
                        <button type="submit" class="btn btn-success ms-auto" id="submitProductBtn" style="display: none;"><i class="fas fa-check-circle me-1"></i> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        // Live Discount Calculator
        function updateLiveDiscount() {
            const priceInput = document.getElementById('productPriceInput');
            const salePriceInput = document.getElementById('productSalePriceInput');
            const badge = document.getElementById('liveDiscountBadge');

            if (!priceInput || !salePriceInput || !badge) return;

            const price = parseFloat(priceInput.value) || 0;
            const salePrice = parseFloat(salePriceInput.value) || 0;

            if (price > 0 && salePrice > 0 && salePrice < price) {
                const discount = Math.round(((price - salePrice) / price) * 100);
                badge.textContent = `${discount}% OFF`;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const priceInput = document.getElementById('productPriceInput');
            const salePriceInput = document.getElementById('productSalePriceInput');
            if (priceInput) priceInput.addEventListener('input', updateLiveDiscount);
            if (salePriceInput) salePriceInput.addEventListener('input', updateLiveDiscount);
            updateLiveDiscount();

            const descEditorEl = document.querySelector('#productDescriptionEditor');
            if (descEditorEl) {
                ClassicEditor
                    .create(descEditorEl, {
                        toolbar: [
                            'heading', '|',
                            'bold', 'italic', 'underline', 'link', 'bulletedList', 'numberedList', '|',
                            'blockQuote', 'insertTable', 'undo', 'redo'
                        ]
                    })
                    .catch(error => {
                        console.error('CKEditor Init Error:', error);
                    });
            }

            initializeProductImageUpload({
                inputId: 'productImageInput',
                previewId: 'productImagePreview',
                previewContainerId: 'productImagePreviewContainer',
                helperId: 'productImageHelper',
                emptyMessage: 'Choose a new main image to replace the current one. Selected image will preview instantly and be compressed before upload.'
            });

            initializeExistingGalleryRemoval({
                sectionId: 'existingGallerySection',
                gridId: 'existingGalleryGrid',
                removedInputsContainerId: 'removedGalleryInputs'
            });
            initializeInteractiveGalleryUploader({
                gridId: 'interactiveGalleryGrid',
                inputsContainerId: 'galleryInputsContainer'
            });
            initializeVariantManager();

            // Dynamic Variant Fields Toggle
            const hasVariantsToggle = document.getElementById('has_variants');
            const variantManagerContainer = document.getElementById('variant-manager-container');

            function toggleVariantFields() {
                if (!hasVariantsToggle || !variantManagerContainer) return;
                const isChecked = hasVariantsToggle.checked;
                variantManagerContainer.style.display = isChecked ? 'block' : 'none';
                const inputs = variantManagerContainer.querySelectorAll('input, select, button');
                inputs.forEach(el => {
                    el.disabled = !isChecked;
                });
            }

            if (hasVariantsToggle && variantManagerContainer) {
                hasVariantsToggle.addEventListener('change', toggleVariantFields);
                toggleVariantFields();
            }

            // Custom Shipping Toggle
            const overrideShippingToggle = document.getElementById('override_shipping');
            const customShippingContainer = document.getElementById('custom-shipping-container');

            function toggleCustomShippingFields() {
                if (!overrideShippingToggle || !customShippingContainer) return;
                const isChecked = overrideShippingToggle.checked;
                customShippingContainer.style.display = isChecked ? 'block' : 'none';
            }

            if (overrideShippingToggle && customShippingContainer) {
                overrideShippingToggle.addEventListener('change', toggleCustomShippingFields);
                toggleCustomShippingFields();
            }

            function createSectionItemRow(secIndex, value = '') {
                const div = document.createElement('div');
                div.className = 'item-row d-flex align-items-center gap-2 mb-2';
                div.innerHTML = `
                    <textarea name="landing_settings[sections][${secIndex}][items][]" class="form-control form-control-sm" rows="2" placeholder="পয়েন্ট / আইটেমের বিবরণ লিখুন">${value}</textarea>
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
                sectionCard.querySelectorAll('.faq-item-row').forEach((row, faqIndex) => {
                    row.querySelector('.text-muted').textContent = `প্রশ্ন ও উত্তর #${faqIndex + 1}`;
                    row.querySelector('input').name = `landing_settings[sections][${secIndex}][faqs][${faqIndex}][q]`;
                    row.querySelector('textarea').name = `landing_settings[sections][${secIndex}][faqs][${faqIndex}][a]`;
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
                            <input type="text" name="landing_settings[sections][${secIndex}][badges][${badgeIndex}][icon]" class="form-control form-control-sm" value="${icon}" placeholder="আইকন (যেমন: fas fa-shield-alt)">
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
                sectionCard.querySelectorAll('.badge-item-row').forEach((row, idx) => {
                    row.querySelector('.text-muted').textContent = `ব্যাজ #${idx + 1}`;
                    row.querySelector('input[name*="[icon]"]').name = `landing_settings[sections][${secIndex}][badges][${idx}][icon]`;
                    row.querySelector('input[name*="[title]"]').name = `landing_settings[sections][${secIndex}][badges][${idx}][title]`;
                    row.querySelector('input[name*="[desc]"]').name = `landing_settings[sections][${secIndex}][badges][${idx}][desc]`;
                });
            }

            function createGalleryImageRow(secIndex, imgUrl = '') {
                const div = document.createElement('div');
                div.className = 'gallery-image-row d-flex align-items-center gap-2 mb-2 p-2 rounded border bg-light';
                div.innerHTML = `
                    <div class="preview-box flex-shrink-0" style="width: 50px; height: 50px; background: #cbd5e1; border-radius: 6px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <img src="${imgUrl || ''}" class="img-preview" style="width: 100%; height: 100%; object-fit: cover; ${imgUrl ? '' : 'display: none;'}" />
                        <i class="fas fa-image text-muted placeholder-icon" style="${imgUrl ? 'display: none;' : ''}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <input type="text" name="landing_settings[sections][${secIndex}][images][]" class="form-control form-control-sm gallery-url-input" value="${imgUrl}" placeholder="ছবির ইউআরএল/লিংক লিখুন">
                    </div>
                    <button type="button" class="btn btn-xs btn-danger remove-item-btn" title="মুছে ফেলুন"><i class="fas fa-trash"></i></button>
                `;
                div.querySelector('.remove-item-btn').addEventListener('click', () => div.remove());
                return div;
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
                    badges: 'আমাদের থেকে কেন কিনবেন?',
                    faq: 'কিছু সাধারণ প্রশ্ন ও উত্তর',
                    gallery: 'পণ্যটির বাস্তব ছবিসমূহ (Photo Gallery)',
                    video: 'পণ্যটির রিভিউ ও ব্যবহারবিধি ভিডিও',
                    custom: 'কাস্টম সেকশন টাইটেল',
                    order_form: 'অর্ডার করতে নিচের ফর্মটি পূরণ করুন',
                    reviews: 'কাস্টমার রিভিউ ও রেটিং'
                };
                const tags = {
                    badges: 'Trust Badges',
                    faq: 'QnA',
                    gallery: 'Photo Gallery',
                    video: 'Showcase Video',
                    custom: 'Special Info',
                    order_form: 'Order Form',
                    reviews: 'Reviews'
                };
                const typeLabels = {
                    badges: '🛡️ Trust Badges',
                    faq: '❓ QnA / FAQ',
                    gallery: '🖼️ Photo Gallery',
                    video: '🎥 YouTube Video',
                    custom: '📝 Custom Section',
                    order_form: '🛒 Custom Order Form',
                    reviews: '⭐ Customer Reviews'
                };

                card.innerHTML = `
                    <input type="hidden" name="landing_settings[sections][${index}][type]" value="${type}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-dark text-white p-2">সেকশন #<span class="sec-number">${index + 1}</span></span>
                            <span class="badge bg-primary text-white p-2">${typeLabels[type] || type.toUpperCase()}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-xs btn-outline-secondary move-up-btn" title="উপরে তুলুন"><i class="fas fa-arrow-up"></i></button>
                            <button type="button" class="btn btn-xs btn-outline-secondary move-down-btn" title="নিচে নামান"><i class="fas fa-arrow-down"></i></button>
                            <button type="button" class="btn btn-xs btn-danger remove-section-btn ms-2" title="ডিলিট"><i class="fas fa-trash"></i> ডিলিট</button>
                        </div>
                    </div>

                    <!-- Row 1: Active Status, Title, Tag -->
                    <div class="row g-2 mb-2">
                        <div class="col-md-3 col-6">
                            <div class="form-group mb-2">
                                <label class="small fw-bold">সেকশন স্ট্যাটাস (Status)</label>
                                <select name="landing_settings[sections][${index}][is_active]" class="form-control form-control-sm form-select">
                                    <option value="1" selected>Active (সক্রিয়)</option>
                                    <option value="0">Inactive (নিষ্ক্রিয়)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group mb-2">
                                <label class="small fw-bold">সেকশন টাইটেল (Title)</label>
                                <input type="text" name="landing_settings[sections][${index}][title]" class="form-control form-control-sm" value="${titles[type] || ''}" placeholder="টাইটেল লিখুন">
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-group mb-2">
                                <label class="small fw-bold">সেকশন ট্যাগ (Tag/Badge)</label>
                                <input type="text" name="landing_settings[sections][${index}][tag]" class="form-control form-control-sm" value="${tags[type] || ''}" placeholder="যেমন: FAQs / Trust">
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Background, Text Color, Font Size, Line Height -->
                    <div class="row g-2 mb-2 p-2 rounded border bg-light">
                        <div class="col-md-3 col-6">
                            <div class="form-group mb-1">
                                <label class="small fw-bold">ব্যাকগ্রাউন্ড (Background)</label>
                                <input type="color" name="landing_settings[sections][${index}][bg_color]" class="form-control form-control-sm form-control-color w-100" value="#ffffff">
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-group mb-1">
                                <label class="small fw-bold">টেক্সট কালার (Text Color)</label>
                                <input type="color" name="landing_settings[sections][${index}][text_color]" class="form-control form-control-sm form-control-color w-100" value="#1e293b">
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-group mb-1">
                                <label class="small fw-bold">ফন্ট সাইজ (Font Size)</label>
                                <select name="landing_settings[sections][${index}][font_size]" class="form-control form-control-sm form-select">
                                    <option value="14px">Small (14px)</option>
                                    <option value="16px" selected>Normal (16px)</option>
                                    <option value="18px">Medium (18px)</option>
                                    <option value="20px">Large (20px)</option>
                                    <option value="24px">Extra Large (24px)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-group mb-1">
                                <label class="small fw-bold">টেক্সট হাইট (Line Height)</label>
                                <select name="landing_settings[sections][${index}][line_height]" class="form-control form-control-sm form-select">
                                    <option value="1.4">Tight (1.4)</option>
                                    <option value="1.6" selected>Normal (1.6)</option>
                                    <option value="1.8">Relaxed (1.8)</option>
                                    <option value="2.0">Loose (2.0)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Add Buy Now Button Option -->
                    <div class="row g-2 mb-2 p-2 rounded border bg-light">
                        <div class="col-md-4 col-12">
                            <div class="form-group mb-1">
                                <label class="small fw-bold">বাই নাও বাটন যুক্ত করুন (Add Buy Now Button)</label>
                                <select name="landing_settings[sections][${index}][show_button]" class="form-control form-control-sm form-select">
                                    <option value="0" selected>না (No Button)</option>
                                    <option value="1">হ্যাঁ, বাটন দেখাও (Show Buy Now Button)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8 col-12">
                            <div class="form-group mb-1">
                                <label class="small fw-bold">বাটন টেক্সট (Button Text)</label>
                                <input type="text" name="landing_settings[sections][${index}][button_text]" class="form-control form-control-sm" value="অর্ডার করতে ক্লিক করুন" placeholder="যেমন: অর্ডার করতে ক্লিক করুন">
                            </div>
                        </div>
                    </div>

                    <!-- Section Content Container -->
                    <div class="section-content-container mt-2 border-top pt-2">
                        ${type === 'gallery' ? `
                            <label class="small fw-bold mb-2">গ্যালারি ছবিসমূহ (Gallery Image Links / URLs)</label>
                            <div class="gallery-items-list border rounded p-2 bg-white"></div>
                            <button type="button" class="btn btn-xs btn-outline-primary add-gallery-item-btn mt-2">
                                <i class="fas fa-plus me-1"></i> ছবি যোগ করুন (Add Photo URL)
                            </button>
                        ` : type === 'video' ? `
                            <div class="form-group mb-2">
                                <label class="small fw-bold">ইউটিউব ভিডিও লিংক (YouTube Video URL)</label>
                                <input type="text" name="landing_settings[sections][${index}][youtube_video_url]" class="form-control form-control-sm" placeholder="যেমন: https://www.youtube.com/watch?v=xxxxxx">
                                <small class="text-muted">ইউটিউব ভিডিওর সম্পূর্ণ লিংকটি এখানে দিন।</small>
                            </div>
                        ` : type === 'custom' ? `
                            <div class="form-group mb-2">
                                <label class="small fw-bold">সংক্ষিপ্ত বিবরণ (Short Description)</label>
                                <textarea name="landing_settings[sections][${index}][short_desc]" class="form-control form-control-sm mb-2" rows="2" placeholder="সেকশনের সংক্ষিপ্ত বিবরণ লিখুন..."></textarea>
                            </div>
                            <label class="small fw-bold mb-2">পয়েন্ট / আইটেমসমূহ (Item Points)</label>
                            <div class="items-list"></div>
                            <button type="button" class="btn btn-xs btn-outline-primary add-item-btn mt-1">
                                <i class="fas fa-plus me-1"></i> পয়েন্ট যোগ করুন (Add Point)
                            </button>
                        ` : type === 'order_form' ? `
                            <div class="alert alert-info py-2 px-3 small mb-0">
                                <i class="fas fa-info-circle me-1"></i> এই সেকশনটি ফ্রন্টএন্ডে সরাসরি ক্যাশ অন ডেলিভারি (Name, Phone, Address, Delivery Area) অর্ডার ফর্ম হিসেবে রেন্ডার হবে।
                            </div>
                        ` : type === 'reviews' ? `
                            <div class="alert alert-info py-2 px-3 small mb-0">
                                <i class="fas fa-info-circle me-1"></i> এই সেকশনটি ফ্রন্টএন্ডে কাস্টমারদের রিভিউ ফর্ম এবং অনুমোদিত রিভিউ তালিকা হিসেবে রেন্ডার হবে।
                            </div>
                        ` : `
                            <label class="small fw-bold mb-2">
                                ${type === 'faq' ? 'প্রশ্ন ও উত্তরসমূহ (FAQs)' : 'পণ্যের ট্রাস্ট ব্যাজসমূহ (Badges)'}
                            </label>
                            <div class="items-list"></div>
                            <button type="button" class="btn btn-xs btn-outline-primary add-item-btn mt-1">
                                <i class="fas fa-plus me-1"></i>
                                ${type === 'faq' ? 'প্রশ্ন ও উত্তর যোগ করুন (Add FAQ)' : 'ব্যাজ যোগ করুন (Add Badge)'}
                            </button>
                        `}
                    </div>
                `;

                bindSectionCardEvents(card);

                if (type === 'gallery') {
                    const list = card.querySelector('.gallery-items-list');
                    if (list) {
                        list.appendChild(createGalleryImageRow(index, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500'));
                        list.appendChild(createGalleryImageRow(index, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500'));
                        list.appendChild(createGalleryImageRow(index, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500'));
                    }
                } else if (type === 'custom') {
                    const itemsList = card.querySelector('.items-list');
                    if (itemsList) {
                        itemsList.appendChild(createSectionItemRow(index, '১০০% প্রিমিয়াম ও অরিজিনাল কোয়ালিটি'));
                        itemsList.appendChild(createSectionItemRow(index, 'দীর্ঘস্থায়ী ও টেকসই ব্যবহার উপযোগী'));
                    }
                } else {
                    const itemsList = card.querySelector('.items-list');
                    if (itemsList) {
                        if (type === 'faq') {
                            itemsList.appendChild(createFAQItemRow(index, 0, 'প্রোডাক্টটি পেতে কতদিন সময় লাগবে?', 'ঢাকা সিটিতে ১-২ দিন এবং ঢাকার বাইরে ২-৩ কার্যদিবসের মধ্যে ডেলিভারি পেয়ে যাবেন।'));
                        } else if (type === 'badges') {
                            itemsList.appendChild(createBadgeItemRow(index, 0, 'fas fa-shield-alt', '১০০% অরিজিনাল', 'সেরা কোয়ালিটির নিশ্চয়তা'));
                            itemsList.appendChild(createBadgeItemRow(index, 1, 'fas fa-shipping-fast', 'দ্রুত ডেলিভারি', 'সারা বাংলাদেশে ক্যাশ অন ডেলিভারি'));
                            itemsList.appendChild(createBadgeItemRow(index, 2, 'fas fa-undo-alt', 'সহজ রিটার্ন সুবিধা', '৭ দিনের মধ্যে এক্সচেঞ্জ সুবিধা'));
                            itemsList.appendChild(createBadgeItemRow(index, 3, 'fas fa-headset', '২৪/৭ সাপোর্ট', 'যেকোনো প্রয়োজনে কল করুন'));
                        }
                    }
                }

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

                // Add gallery image
                const addGalleryBtn = card.querySelector('.add-gallery-item-btn');
                if (addGalleryBtn) {
                    addGalleryBtn.addEventListener('click', function() {
                        const secIndex = parseInt(card.getAttribute('data-section-index') || '0');
                        const galleryList = card.querySelector('.gallery-items-list');
                        if (galleryList) {
                            galleryList.appendChild(createGalleryImageRow(secIndex));
                            const input = galleryList.lastElementChild.querySelector('input');
                            if (input) input.focus();
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
                        } else if (type === 'custom') {
                            itemsList.appendChild(createSectionItemRow(secIndex));
                            const textarea = itemsList.lastElementChild.querySelector('textarea');
                            if (textarea) textarea.focus();
                        }
                    });
                }

                // Bind existing inner delete buttons
                card.querySelectorAll('.remove-item-btn').forEach((btn) => {
                    btn.addEventListener('click', function() {
                        const row = btn.closest('.item-row, .faq-item-row, .badge-item-row, .gallery-image-row');
                        if (row) {
                            row.remove();
                            if (type === 'faq') {
                                reindexFAQItems(card);
                            } else if (type === 'badges') {
                                reindexBadgeItems(card);
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

                    const activeSelect = card.querySelector('select[name*="[is_active]"]');
                    if (activeSelect) activeSelect.name = `landing_settings[sections][${index}][is_active]`;

                    const titleInput = card.querySelector('input[name*="[title]"]');
                    if (titleInput) titleInput.name = `landing_settings[sections][${index}][title]`;

                    const tagInput = card.querySelector('input[name*="[tag]"]');
                    if (tagInput) tagInput.name = `landing_settings[sections][${index}][tag]`;

                    const bgColorInput = card.querySelector('input[name*="[bg_color]"]');
                    if (bgColorInput) bgColorInput.name = `landing_settings[sections][${index}][bg_color]`;

                    const textColorInput = card.querySelector('input[name*="[text_color]"]');
                    if (textColorInput) textColorInput.name = `landing_settings[sections][${index}][text_color]`;

                    const fontSizeSelect = card.querySelector('select[name*="[font_size]"]');
                    if (fontSizeSelect) fontSizeSelect.name = `landing_settings[sections][${index}][font_size]`;

                    const lineHeightSelect = card.querySelector('select[name*="[line_height]"]');
                    if (lineHeightSelect) lineHeightSelect.name = `landing_settings[sections][${index}][line_height]`;

                    const showButtonSelect = card.querySelector('select[name*="[show_button]"]');
                    if (showButtonSelect) showButtonSelect.name = `landing_settings[sections][${index}][show_button]`;

                    const buttonTextInput = card.querySelector('input[name*="[button_text]"]');
                    if (buttonTextInput) buttonTextInput.name = `landing_settings[sections][${index}][button_text]`;

                    const shortDescTextarea = card.querySelector('textarea[name*="[short_desc]"]');
                    if (shortDescTextarea) shortDescTextarea.name = `landing_settings[sections][${index}][short_desc]`;

                    const videoInput = card.querySelector('input[name*="[youtube_video_url]"]');
                    if (videoInput) videoInput.name = `landing_settings[sections][${index}][youtube_video_url]`;

                    const type = card.getAttribute('data-section-type');
                    if (type === 'faq') {
                        reindexFAQItems(card);
                    } else if (type === 'badges') {
                        reindexBadgeItems(card);
                    } else if (type === 'gallery') {
                        card.querySelectorAll('.gallery-url-input').forEach(input => {
                            input.name = `landing_settings[sections][${index}][images][]`;
                        });
                    } else if (type === 'custom') {
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
                            } else if (type === 'custom') {
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

            // Modal controller for visual section selector
            const openPickerBtn = document.getElementById('openSectionPickerModalBtn');
            const sectionModalEl = document.getElementById('sectionPickerModal');
            const confirmAddBtn = document.getElementById('confirmAddSectionBtn');
            const sectionsContainer = document.getElementById('landing-sections-builder-container');
            let selectedType = null;

            if (openPickerBtn && sectionModalEl) {
                const pickerModal = new bootstrap.Modal(sectionModalEl);

                openPickerBtn.addEventListener('click', function() {
                    selectedType = null;
                    if (confirmAddBtn) confirmAddBtn.disabled = true;
                    document.querySelectorAll('.section-picker-card').forEach(c => c.classList.remove('active-selected'));
                    pickerModal.show();
                });

                document.querySelectorAll('.section-picker-card').forEach(card => {
                    card.addEventListener('click', function() {
                        document.querySelectorAll('.section-picker-card').forEach(c => c.classList.remove('active-selected'));
                        card.classList.add('active-selected');
                        selectedType = card.getAttribute('data-type');
                        if (confirmAddBtn) confirmAddBtn.disabled = !selectedType;
                    });
                });

                if (confirmAddBtn) {
                    confirmAddBtn.addEventListener('click', function() {
                        const targetContainer = sectionsContainer || document.getElementById('landing-sections-builder-container');
                        if (!selectedType || !targetContainer) return;
                        const index = document.querySelectorAll('.section-card').length;
                        const newCard = createSectionCard(selectedType, index);
                        bindSectionCardEvents(newCard);
                        targetContainer.appendChild(newCard);
                        reindexSections();
                        pickerModal.hide();
                        newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        const titleInput = newCard.querySelector('input[type="text"]');
                        if (titleInput) titleInput.focus();
                    });
                }
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
        });

        function initializeExistingGalleryManager() {
            const section = document.getElementById('existingGallerySection');
            const grid = document.getElementById('existingGalleryGrid');
            const removedInputs = document.getElementById('removedGalleryInputs');

            if (!section || !grid || !removedInputs) return;

            grid.addEventListener('click', function (e) {
                const btn = e.target.closest('.js-remove-existing-gallery');
                if (!btn) return;

                const path = btn.getAttribute('data-path');
                const card = btn.closest('.gallery-preview-card');

                if (card) {
                    card.remove();
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'removed_gallery[]';
                    input.value = path;
                    removedInputs.appendChild(input);

                    if (grid.children.length === 0) {
                        section.style.display = 'none';
                    }
                }
            });
        }

        function initializeProductImageUpload({ inputId, previewId, previewContainerId, helperId, emptyMessage }) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const previewContainer = document.getElementById(previewContainerId);

            if (!input || !preview || !previewContainer) return;

            input.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    input.value = '';
                    return;
                }

                updatePreview(preview, previewContainer, file);
            });
        }

        function updatePreview(preview, previewContainer, file) {
            preview.src = URL.createObjectURL(file);
            previewContainer.style.display = 'block';
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

                        const objectUrl = URL.createObjectURL(file);
                        previewCard.innerHTML = `
                            <img src="${objectUrl}" alt="${file.name}">
                            <div class="gallery-preview-meta">
                                <strong>${file.name}</strong><br>
                                ${(file.size / 1024).toFixed(1)} KB
                            </div>
                            <div class="gallery-preview-actions">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100 js-remove-preview">
                                    Remove
                                </button>
                            </div>
                        `;

                        const img = previewCard.querySelector('img');
                        img.addEventListener('load', function() {
                            URL.revokeObjectURL(objectUrl);
                        }, { once: true });

                        previewCard.querySelector('.js-remove-preview').addEventListener('click', function() {
                            previewCard.remove();
                            fileInput.remove();
                        });

                        grid.insertBefore(previewCard, addCard);
                    });

                    fileInput.click();
                });

                grid.appendChild(addCard);
            }

            createAddCard();
        }

        function initializeVariantManager() {
            const rowsContainer = document.getElementById('variantRows');
            const template = document.getElementById('variantRowTemplate');
            const addButton = document.getElementById('addVariantRowBtn');

            if (!rowsContainer || !template || !addButton) {
                return;
            }

            let nextIndex = rowsContainer.querySelectorAll('.variant-row').length;

            addButton.addEventListener('click', function () {
                const wrapper = document.createElement('tbody');
                wrapper.innerHTML = template.innerHTML.replace(/__INDEX__/g, nextIndex);
                rowsContainer.appendChild(wrapper.firstElementChild);
                nextIndex += 1;
            });

            rowsContainer.addEventListener('click', function (event) {
                const removeButton = event.target.closest('.js-remove-variant-row');

                if (!removeButton) {
                    return;
                }

                removeButton.closest('.variant-row')?.remove();
            });
        }
    </script>
@endpush

<!-- Select a Section to Add Modal -->
<div class="modal fade" id="sectionPickerModal" tabindex="-1" aria-labelledby="sectionPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: 0; box-shadow: 0 20px 40px rgba(0,0,0,0.15); overflow: hidden;">
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="modal-title fw-bold text-dark fs-5" id="sectionPickerModalLabel">Select a Section to Add</h5>
                    <p class="text-muted small mb-0">Choose a section layout template for your landing page</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                <div class="row g-3" id="sectionPickerCardsGrid">
                    <!-- 1. Trust Badges -->
                    <div class="col-md-4 col-6">
                        <div class="section-picker-card h-100 p-3 text-center rounded-3 border bg-white shadow-sm position-relative cursor-pointer" data-type="badges">
                            <div class="section-card-icon-box rounded-3 mb-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-shield-alt fs-1 text-success"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">Trust Badges (ট্রাস্ট ব্যাজ)</h6>
                            <p class="text-muted small mb-0" style="font-size: 11px; line-height: 1.3;">কোয়ালিটি, গ্যারান্টি ও সিকিউরিটি ব্যাজ</p>
                        </div>
                    </div>

                    <!-- 2. QnA / FAQ -->
                    <div class="col-md-4 col-6">
                        <div class="section-picker-card h-100 p-3 text-center rounded-3 border bg-white shadow-sm position-relative cursor-pointer" data-type="faq">
                            <div class="section-card-icon-box rounded-3 mb-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-question-circle fs-1" style="color: #8b5cf6;"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">QnA Section (প্রশ্নোত্তর)</h6>
                            <p class="text-muted small mb-0" style="font-size: 11px; line-height: 1.3;">সাধারণ প্রশ্ন ও উত্তর একর্ডিয়ন</p>
                        </div>
                    </div>

                    <!-- 3. Photo Gallery -->
                    <div class="col-md-4 col-6">
                        <div class="section-picker-card h-100 p-3 text-center rounded-3 border bg-white shadow-sm position-relative cursor-pointer" data-type="gallery">
                            <div class="section-card-icon-box rounded-3 mb-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-images fs-1 text-danger"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">Photo Gallery (ছবি গ্যালারি)</h6>
                            <p class="text-muted small mb-0" style="font-size: 11px; line-height: 1.3;">পণ্যের বাস্তব ছবি ও ফটো গ্রিড</p>
                        </div>
                    </div>

                    <!-- 4. YouTube Video -->
                    <div class="col-md-4 col-6">
                        <div class="section-picker-card h-100 p-3 text-center rounded-3 border bg-white shadow-sm position-relative cursor-pointer" data-type="video">
                            <div class="section-card-icon-box rounded-3 mb-3 d-flex align-items-center justify-content-center">
                                <i class="fab fa-youtube fs-1 text-danger"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">YouTube Video (ভিডিও)</h6>
                            <p class="text-muted small mb-0" style="font-size: 11px; line-height: 1.3;">প্রোডাক্ট রিভিউ বা ডেমো ভিডিও</p>
                        </div>
                    </div>

                    <!-- 5. Custom Section -->
                    <div class="col-md-4 col-6">
                        <div class="section-picker-card h-100 p-3 text-center rounded-3 border bg-white shadow-sm position-relative cursor-pointer" data-type="custom">
                            <div class="section-card-icon-box rounded-3 mb-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-list-check fs-1 text-primary"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">Custom Section (কাস্টম সেকশন)</h6>
                            <p class="text-muted small mb-0" style="font-size: 11px; line-height: 1.3;">টাইটেল, সংক্ষিপ্ত বিবরণ ও পয়েন্ট তালিকা</p>
                        </div>
                    </div>

                    <!-- 6. Custom Order Form -->
                    <div class="col-md-4 col-6">
                        <div class="section-picker-card h-100 p-3 text-center rounded-3 border bg-white shadow-sm position-relative cursor-pointer" data-type="order_form">
                            <div class="section-card-icon-box rounded-3 mb-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-shopping-bag fs-1 text-warning"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">Custom Order Form (অর্ডার ফর্ম)</h6>
                            <p class="text-muted small mb-0" style="font-size: 11px; line-height: 1.3;">সরাসরি ক্যাশ অন ডেলিভারি ফর্ম</p>
                        </div>
                    </div>

                    <!-- 7. Review Form / Reviews -->
                    <div class="col-md-4 col-6">
                        <div class="section-picker-card h-100 p-3 text-center rounded-3 border bg-white shadow-sm position-relative cursor-pointer" data-type="reviews">
                            <div class="section-card-icon-box rounded-3 mb-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-star-half-alt fs-1 text-warning"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">Review Section (রিভিউ ফর্ম ও তালিকা)</h6>
                            <p class="text-muted small mb-0" style="font-size: 11px; line-height: 1.3;">কাস্টমার রিভিউ ও মতামত ফর্ম</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex justify-content-between">
                <button type="button" class="btn btn-light px-4 text-secondary fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                <button type="button" class="btn btn-primary px-4 fw-bold" id="confirmAddSectionBtn" style="border-radius: 8px; background-color: #2563eb;" disabled>Add Section</button>
            </div>
        </div>
    </div>
</div>

<style>
.section-picker-card {
    border: 1.5px solid #e2e8f0 !important;
    transition: all 0.2s ease-in-out;
}
.section-picker-card .section-card-icon-box {
    height: 90px;
    background: #f8fafc;
    transition: all 0.2s ease-in-out;
}
.section-picker-card:hover {
    border-color: #3b82f6 !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.12) !important;
}
.section-picker-card.active-selected {
    border: 2.5px solid #2563eb !important;
    background-color: #eff6ff !important;
    box-shadow: 0 10px 24px rgba(37, 99, 235, 0.2) !important;
}
.section-picker-card.active-selected .section-card-icon-box {
    background-color: #dbeafe !important;
}
.cursor-pointer {
    cursor: pointer;
}

/* Section Card Form Control & Select Styles Fix */
.section-card select.form-select,
.section-card select.form-control-sm,
.section-card input.form-control-sm {
    height: 38px !important;
    padding: 6px 28px 6px 12px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #1e293b !important;
    background-color: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    line-height: 1.4 !important;
    box-sizing: border-box !important;
}

.section-card select.form-select {
    background-position: right 10px center !important;
    background-size: 12px 10px !important;
}

.section-card select.form-select:focus,
.section-card input.form-control-sm:focus,
.section-card textarea.form-control-sm:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    outline: 0 !important;
}

.section-card input[type="color"].form-control-color {
    height: 38px !important;
    padding: 3px 4px !important;
    border-radius: 6px !important;
    cursor: pointer;
}
</style>
