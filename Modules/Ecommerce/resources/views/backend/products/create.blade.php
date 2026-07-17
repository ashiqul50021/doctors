@extends('layouts.admin')

@include('ecommerce::backend.products.partials.image-manager-styles')

@section('title', 'Add Product - abcsheba Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Add Product</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
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
                    'id' => $variant['id'] ?? '',
                    'option_name' => $variant['option_name'] ?? '',
                    'option_value' => $variant['option_value'] ?? '',
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

                <form action="{{ route('ecommerce.admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row form-row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
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
                                <label>Price</label>
                                <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required>
                            </div>
                        </div>
                         <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Sale Price (Optional)</label>
                                <input type="number" step="0.01" name="sale_price" class="form-control" value="{{ old('sale_price') }}">
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
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="productImageInput">Primary Image</label>
                                <input type="file" name="image" class="form-control" id="productImageInput" accept="image/*">
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
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-4 mb-4">
                            <div class="card border-primary shadow-sm" style="border-radius: 12px; overflow: hidden; border-left: 5px solid #007bff;">
                                <div class="card-header bg-light d-flex align-items-center justify-content-between py-3">
                                    <h4 class="card-title mb-0 text-primary fw-bold" style="font-size: 18px;">
                                        <i class="fas fa-magic me-2"></i> Landing Page Customization (সীমিত সময়ের অফার ও ট্রাস্ট ইনফো)
                                    </h4>
                                    <span class="badge bg-primary text-white">Dynamic Content</span>
                                </div>
                                <div class="card-body">
                                    <!-- Countdown Banner -->
                                    <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark" style="font-size: 15px;"><i class="fas fa-clock text-danger me-1"></i> ১. জরুরী অফার কাউন্টডাউন (Urgency Countdown Banner)</h5>
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label class="fw-bold">অফার টাইটেল (Countdown Title)</label>
                                                <input type="text" name="landing_settings[countdown_title]" class="form-control" value="{{ old('landing_settings.countdown_title', 'আজকের বিশেষ ছাড় অফার!') }}" placeholder="আজকের বিশেষ ছাড় অফার!">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label class="fw-bold">অফার সাবটাইটেল (Countdown Subtitle)</label>
                                                <input type="text" name="landing_settings[countdown_subtitle]" class="form-control" value="{{ old('landing_settings.countdown_subtitle', 'অফারটি শেষ হতে আর মাত্র সময় বাকি আছে:') }}" placeholder="অফারটি শেষ হতে আর মাত্র সময় বাকি আছে:">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label class="fw-bold">অফার সময় - ঘণ্টায় (Countdown Hours)</label>
                                                <input type="number" name="landing_settings[countdown_hours]" class="form-control" value="{{ old('landing_settings.countdown_hours', 3) }}" placeholder="3">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label class="fw-bold">কাউন্টডাউন স্ট্যাটাস (Countdown Status)</label>
                                                <select name="landing_settings[show_countdown]" class="form-control">
                                                    <option value="1" {{ old('landing_settings.show_countdown', '1') == '1' ? 'selected' : '' }}>Active (সক্রিয়)</option>
                                                    <option value="0" {{ old('landing_settings.show_countdown', '1') == '0' ? 'selected' : '' }}>Inactive (নিষ্ক্রিয়)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Product Video -->
                                    <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-dark" style="font-size: 15px;"><i class="fab fa-youtube text-danger me-1"></i> ১.৫. প্রোডাক্ট ভিডিও (Product Video - Optional)</h5>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="fw-bold">ইউটিউব ভিডিও লিংক (YouTube Video URL)</label>
                                                <input type="text" name="landing_settings[youtube_video_url]" class="form-control" value="{{ old('landing_settings.youtube_video_url') }}" placeholder="যেমন: https://www.youtube.com/watch?v=xxxxxx">
                                                <small class="text-muted d-block mt-1">প্রোডাক্টের ব্যবহারবিধি বা রিভিউ ভিডিওর ইউটিউব লিংক এখানে দিন।</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Unified Landing Sections Builder -->
                                    <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-dark" style="font-size: 15px;"><i class="fas fa-layer-group text-primary me-1"></i> ২. ল্যান্ডিং পেজ সেকশন বিল্ডার (Landing Page Sections Builder)</h5>
                                    <div class="row">
                                        <div class="col-12">
                                            <p class="text-muted small">এখানে আপনি ল্যান্ডিং পেজের সেকশনগুলোর ক্রম পরিবর্তন (Up/Down), নতুন সেকশন যোগ বা ডিলিট করতে পারবেন।</p>
                                            
                                            <div id="landing-sections-builder-container">
                                                @php
                                                    $defaultBadges = [];
                                                    $defaultIcons = [1 => 'fas fa-undo-alt', 2 => 'fas fa-hand-holding-usd', 3 => 'fas fa-headset', 4 => 'fas fa-shipping-fast'];
                                                    $defaultTitles = [1 => '৭ দিনের রিটার্ন', 2 => 'হাতে পেয়ে পেমেন্ট', 3 => 'অনলাইন সাপোর্ট', 4 => 'সারাদেশে ডেলিভারি'];
                                                    $defaultDescs = [1 => 'সহজ এক্সচেঞ্জ সুবিধা', 2 => 'ক্যাশ অন ডেলিভারি', 3 => '২৪/৭ কাস্টমার কেয়ার', 4 => 'দ্রুত ও নিরাপদ ডেলিভারি'];
                                                    for ($i = 1; $i <= 4; $i++) {
                                                        $defaultBadges[] = [
                                                            'icon' => $defaultIcons[$i],
                                                            'title' => $defaultTitles[$i],
                                                            'desc' => $defaultDescs[$i]
                                                        ];
                                                    }

                                                    $defaultFeatureTitles = [
                                                        1 => '১০০% আসল প্রোডাক্ট (100% Original)',
                                                        2 => 'নিরাপদ প্যাকেজিং ও ডেলিভারি (Secure Shipping)',
                                                        3 => 'সহজ রিটার্ন পলিসি (Easy Returns)',
                                                        4 => '২৪/৭ কাস্টমার সাপোর্ট (Dedicated Hotline)'
                                                    ];
                                                    $defaultFeatureDescs = [
                                                        1 => 'আমরা কোনো নকল পণ্য বিক্রি করি না। সরাসরি ভেরিফাইড ব্র্যান্ড ও ইমপোর্টার থেকে পণ্য সংগ্রহ করি।',
                                                        2 => 'আপনার পণ্যটি যাতে অক্ষত অবস্থায় পৌঁছায়, সেজন্য আমাদের রয়েছে নিখুঁত বাবল-র‍্যাপড প্যাকেজিং ব্যবস্থা।',
                                                        3 => 'পণ্য গ্রহণের পর কোনো ত্রুটি পেলে ৭ দিনের মধ্যে আমাদের সাথে যোগাযোগ করে রিফান্ড বা এক্সচেঞ্জ করতে পারবেন।',
                                                        4 => 'অর্ডার করার আগে বা পরে যেকোনো গাইডলাইনের জন্য আমাদের কাস্টমার কেয়ার হেল্পলাইন সর্বদা উন্মুক্ত।'
                                                    ];
                                                    $defaultFeatures = [];
                                                    for ($i = 1; $i <= 4; $i++) {
                                                        $defaultFeatures[] = [
                                                            'title' => $defaultFeatureTitles[$i],
                                                            'desc' => $defaultFeatureDescs[$i]
                                                        ];
                                                    }

                                                    $sections = [
                                                        [
                                                            'type' => 'features',
                                                            'title' => 'আমাদের প্রোডাক্টের বৈশিষ্ট্য',
                                                            'tag' => 'Product Features',
                                                            'style' => 'blue-check',
                                                            'items' => [
                                                                '১০০% প্রিমিয়াম কোয়ালিটি',
                                                                'টেকসই ও আকর্ষণীয় ডিজাইন',
                                                                'সহজে ব্যবহারযোগ্য ও বহনযোগ্য'
                                                            ]
                                                        ],
                                                        [
                                                            'type' => 'badges',
                                                            'title' => 'আমাদের থেকে কেন কিনবেন?',
                                                            'tag' => 'Trust Badges',
                                                            'badges' => $defaultBadges
                                                        ],
                                                        [
                                                            'type' => 'problems',
                                                            'title' => 'এই समस्याগুলো কি আপনারও আছে?',
                                                            'tag' => 'Common Issues',
                                                            'style' => 'red-cross',
                                                            'items' => [
                                                                'বাজারে নকল প্রোডাক্টের ভয়?',
                                                                'সঠিক দাম ও কোয়ালিটি নিয়ে দুশ্চিন্তা?',
                                                                'ডেলিভারি পেতে অনেক দেরি হওয়া?'
                                                            ]
                                                        ],
                                                        [
                                                            'type' => 'benefits',
                                                            'title' => 'বৈশিষ্ট্যগুলো কি কি জানতে চান?',
                                                            'tag' => 'Benefits',
                                                            'style' => 'green-check',
                                                            'items' => [
                                                                'আমাদের প্রোডাক্ট ব্যবহারে আপনার সময় বাঁচবে।',
                                                                'এটি ব্যবহারে কাজের পারফরম্যান্স বৃদ্ধি পাবে।',
                                                                '১০০% রিফান্ড বা এক্সচেঞ্জ পলিসি গ্যারান্টি।'
                                                            ]
                                                        ],
                                                        [
                                                            'type' => 'package',
                                                            'title' => 'প্যাকেজের সাথে যা যা পাবেন',
                                                            'tag' => 'Package Contents',
                                                            'style' => 'package-box',
                                                            'items' => [
                                                                '১টি প্রিমিয়াম মেইন প্রোডাক্ট',
                                                                '১টি ইউজার ম্যানুয়াল গাইডবুক',
                                                                '১টি ওয়ারেন্টি কার্ড (১ বছরের গ্যারান্টি)',
                                                                '১টি প্রিমিয়াম গিফট বক্স প্যাকেজিং'
                                                            ]
                                                        ],
                                                        [
                                                            'type' => 'trust',
                                                            'title' => 'কেন আমাদের থেকে অর্ডার করবেন?',
                                                            'tag' => 'Trust',
                                                            'trust_features' => $defaultFeatures
                                                        ],
                                                        [
                                                            'type' => 'faq',
                                                            'title' => 'কিছু সাধারণ প্রশ্ন',
                                                            'tag' => 'FAQs',
                                                            'style' => 'faq-accordion',
                                                            'faqs' => [
                                                                [
                                                                    'q' => 'অর্ডার কিভাবে করব?',
                                                                    'a' => 'অর্ডার করার জন্য ফর্মটি পূরণ করে সাবমিট করুন। আমাদের টিম দ্রুত আপনার সাথে যোগাযোগ করবে।'
                                                                ],
                                                                [
                                                                    'q' => 'ডেলিভারি চার্জ কত?',
                                                                    'a' => 'সারাদেশে ডেলিভারি চার্জ সম্পূর্ণ ফ্রি! কোনো অতিরিক্ত ফি দিতে হবে না।'
                                                                ]
                                                            ]
                                                        ]
                                                    ];
                                                @endphp

                                                @foreach($sections as $secIdx => $sec)
                                                    @php
                                                        $secType = $sec['type'] ?? 'custom';
                                                        $secTitle = $sec['title'] ?? '';
                                                        $secTag = $sec['tag'] ?? '';
                                                        $secStyle = $sec['style'] ?? 'blue-check';
                                                    @endphp
                                                    <div class="section-card card p-3 mb-3 border shadow-sm" data-section-type="{{ $secType }}" style="border-radius: 12px; border: 1px solid #cbd5e1 !important; background-color: #f8fafc;">
                                                        <input type="hidden" name="landing_settings[sections][{{ $secIdx }}][type]" value="{{ $secType }}">
                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="badge bg-dark text-white p-2">সেকশন #<span class="sec-number">{{ $secIdx + 1 }}</span></span>
                                                                <span class="badge bg-info text-white p-2">{{ strtoupper($secType) }}</span>
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
                                                                    <input type="text" name="landing_settings[sections][{{ $secIdx }}][title]" class="form-control form-control-sm" value="{{ $secTitle }}" placeholder="টাইটেল লিখুন">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-6">
                                                                <div class="form-group mb-2">
                                                                    <label class="small fw-bold">সেকশন ট্যাগ (Tag)</label>
                                                                    <input type="text" name="landing_settings[sections][{{ $secIdx }}][tag]" class="form-control form-control-sm" value="{{ $secTag }}" placeholder="ট্যাগ লিখুন">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-6">
                                                                <div class="form-group mb-2">
                                                                    <label class="small fw-bold">স্টাইল / লেআউট (Style)</label>
                                                                    <select name="landing_settings[sections][{{ $secIdx }}][style]" class="form-control form-control-sm form-select">
                                                                        <option value="blue-check" {{ $secStyle === 'blue-check' ? 'selected' : '' }}>Blue Check Square</option>
                                                                        <option value="green-check" {{ $secStyle === 'green-check' ? 'selected' : '' }}>Green Check Circle</option>
                                                                        <option value="red-cross" {{ $secStyle === 'red-cross' ? 'selected' : '' }}>Red Cross</option>
                                                                        <option value="yellow-star" {{ $secStyle === 'yellow-star' ? 'selected' : '' }}>Yellow Star</option>
                                                                        <option value="orange-info" {{ $secStyle === 'orange-info' ? 'selected' : '' }}>Orange Info</option>
                                                                        <option value="package-box" {{ $secStyle === 'package-box' ? 'selected' : '' }}>Package Box Style</option>
                                                                        <option value="faq-accordion" {{ $secStyle === 'faq-accordion' ? 'selected' : '' }}>FAQ Accordion</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="section-content-container mt-2 border-top pt-2" style="{{ in_array($secType, ['video', 'gallery']) ? 'display: none;' : '' }}">
                                                            @if($secType === 'faq')
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
                                                                                    <input type="text" name="landing_settings[sections][{{ $secIdx }}][badges][{{ $itemIdx }}][icon]" class="form-control form-control-sm" value="{{ $badge['icon'] ?? '' }}" placeholder="আইকন ক্লাস">
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
                                                            @elseif($secType === 'trust')
                                                                <label class="small fw-bold mb-2">কেন আমাদের থেকে সংগ্রহ করবেন (Trust Features)</label>
                                                                <div class="items-list">
                                                                    @php
                                                                        $secTrustFeatures = $sec['trust_features'] ?? [];
                                                                    @endphp
                                                                    @foreach($secTrustFeatures as $itemIdx => $feat)
                                                                        <div class="trust-feature-item-row border p-2 mb-2 bg-white rounded shadow-sm">
                                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                <span class="small fw-bold text-muted">ফিচার #{{ $itemIdx + 1 }}</span>
                                                                                <button type="button" class="btn btn-xs btn-outline-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                                                                            </div>
                                                                            <div class="form-group mb-1">
                                                                                <input type="text" name="landing_settings[sections][{{ $secIdx }}][trust_features][{{ $itemIdx }}][title]" class="form-control form-control-sm" value="{{ $feat['title'] ?? '' }}" placeholder="টাইটেল লিখুন">
                                                                            </div>
                                                                            <div class="form-group mb-0">
                                                                                <textarea name="landing_settings[sections][{{ $secIdx }}][trust_features][{{ $itemIdx }}][desc]" class="form-control form-control-sm" rows="2" placeholder="বর্ণনা লিখুন">{{ $feat['desc'] ?? '' }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <button type="button" class="btn btn-xs btn-outline-primary add-item-btn mt-1"><i class="fas fa-plus me-1"></i> ফিচার যোগ করুন (Add Feature)</button>
                                                            @else
                                                                <label class="small fw-bold mb-2">সেকশন আইটেমসমূহ (Items)</label>
                                                                <div class="items-list">
                                                                    @php
                                                                        $secItems = $sec['items'] ?? [];
                                                                    @endphp
                                                                    @foreach($secItems as $itemText)
                                                                        <div class="item-row d-flex align-items-center gap-2 mb-2">
                                                                            <textarea name="landing_settings[sections][{{ $secIdx }}][items][]" class="form-control form-control-sm" rows="2" placeholder="আইটেমের বিবরণ লিখুন">{{ $itemText }}</textarea>
                                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn"><i class="fas fa-trash"></i></button>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <button type="button" class="btn btn-xs btn-outline-primary add-item-btn mt-1"><i class="fas fa-plus me-1"></i> আইটেম যোগ করুন (Add Item)</button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
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
                                                        <option value="custom">Custom Section (কাস্টম সেকশন)</option>
                                                    </select>
                                                    <button type="button" id="add-section-btn" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> সেকশন যোগ করুন (Add Section)</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('ecommerce::backend.products.partials.variant-manager', ['variantRows' => $variantRows])
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
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
                emptyMessage: 'Choose a new main image to replace the current one. Selected image will preview instantly and be compressed before upload.'
            });

            initializeInteractiveGalleryUploader({
                gridId: 'interactiveGalleryGrid',
                inputName: 'gallery_images[]',
                addBtnId: 'addInteractiveGalleryBtn',
                emptyMessageId: 'interactiveGalleryEmptyMessage',
                maxFiles: 10
            });

            const hasVariantsToggle = document.getElementById('has_variants');
            const variantManagerContainer = document.getElementById('variant-manager-container');

            function toggleVariantFields() {
                const isChecked = hasVariantsToggle.checked;
                variantManagerContainer.style.display = isChecked ? 'block' : 'none';
                
                const variantInputs = variantManagerContainer.querySelectorAll('input, select, textarea');
                if (isChecked) {
                    variantInputs.forEach(el => {
                        el.disabled = false;
                    });
                } else {
                    variantInputs.forEach(el => {
                        el.disabled = true;
                    });
                }
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
                div.querySelector('.remove-item-btn').addEventListener('click', function() {
                    div.remove();
                });
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
                div.querySelector('.remove-item-btn').addEventListener('click', function() {
                    div.remove();
                    reindexFAQItems(div.closest('.section-card'));
                });
                return div;
            }

            function reindexFAQItems(sectionCard) {
                const secIndex = parseInt(sectionCard.getAttribute('data-section-index') || '0');
                const faqRows = sectionCard.querySelectorAll('.faq-item-row');
                faqRows.forEach((row, faqIndex) => {
                    row.querySelector('.text-muted').textContent = `প্রশ্ন ও উত্তর #${faqIndex + 1}`;
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
                div.querySelector('.remove-item-btn').addEventListener('click', function() {
                    div.remove();
                    reindexBadgeItems(div.closest('.section-card'));
                });
                return div;
            }

            function reindexBadgeItems(sectionCard) {
                const secIndex = parseInt(sectionCard.getAttribute('data-section-index') || '0');
                const rows = sectionCard.querySelectorAll('.badge-item-row');
                rows.forEach((row, idx) => {
                    row.querySelector('.text-muted').textContent = `ব্যাজ #${idx + 1}`;
                    row.querySelector('input[name*="[icon]"]').name = `landing_settings[sections][${secIndex}][badges][${idx}][icon]`;
                    row.querySelector('input[name*="[title]"]').name = `landing_settings[sections][${secIndex}][badges][${idx}][title]`;
                    row.querySelector('input[name*="[desc]"]').name = `landing_settings[sections][${secIndex}][badges][${idx}][desc]`;
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
                div.querySelector('.remove-item-btn').addEventListener('click', function() {
                    div.remove();
                    reindexTrustFeatureItems(div.closest('.section-card'));
                });
                return div;
            }

            function reindexTrustFeatureItems(sectionCard) {
                const secIndex = parseInt(sectionCard.getAttribute('data-section-index') || '0');
                const rows = sectionCard.querySelectorAll('.trust-feature-item-row');
                rows.forEach((row, idx) => {
                    row.querySelector('.text-muted').textContent = `ফিচার #${idx + 1}`;
                    row.querySelector('input[name*="[title]"]').name = `landing_settings[sections][${secIndex}][trust_features][${idx}][title]`;
                    row.querySelector('textarea[name*="[desc]"]').name = `landing_settings[sections][${secIndex}][trust_features][${idx}][desc]`;
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
                    custom: 'নতুন কাস্টম সেকশন'
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
                    custom: 'INFO'
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
                    custom: 'blue-check'
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
                                <input type="text" name="landing_settings[sections][${index}][title]" class="form-control form-control-sm" value="${titles[type]}" placeholder="টাইটেল লিখুন">
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="form-group mb-2">
                                <label class="small fw-bold">সেকশন ট্যাগ (Tag)</label>
                                <input type="text" name="landing_settings[sections][${index}][tag]" class="form-control form-control-sm" value="${tags[type]}" placeholder="ট্যাগ লিখুন">
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

                    <div class="section-content-container mt-2 border-top pt-2" style="${['video', 'gallery'].includes(type) ? 'display: none;' : ''}">
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
                card.querySelector('.remove-section-btn').addEventListener('click', function() {
                    card.remove();
                    reindexSections();
                });

                // Move up
                card.querySelector('.move-up-btn').addEventListener('click', function() {
                    const prev = card.previousElementSibling;
                    if (prev) {
                        card.parentNode.insertBefore(card, prev);
                        reindexSections();
                    }
                });

                // Move down
                card.querySelector('.move-down-btn').addEventListener('click', function() {
                    const next = card.nextElementSibling;
                    if (next) {
                        card.parentNode.insertBefore(next, card);
                        reindexSections();
                    }
                });

                // Add item
                card.querySelector('.add-item-btn').addEventListener('click', function() {
                    const secIndex = parseInt(card.getAttribute('data-section-index') || '0');
                    const itemsList = card.querySelector('.items-list');
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

                // Bind existing inner delete buttons
                card.querySelectorAll('.remove-item-btn').forEach((btn) => {
                    btn.addEventListener('click', function() {
                        btn.closest('.item-row, .faq-item-row, .badge-item-row, .trust-feature-item-row').remove();
                        if (type === 'faq') {
                            reindexFAQItems(card);
                        } else if (type === 'badges') {
                            reindexBadgeItems(card);
                        } else if (type === 'trust') {
                            reindexTrustFeatureItems(card);
                        }
                    });
                });
            }

            function reindexSections() {
                const cards = document.querySelectorAll('.section-card');
                cards.forEach((card, index) => {
                    card.setAttribute('data-section-index', index);
                    card.querySelector('.sec-number').textContent = index + 1;

                    card.querySelector('input[type="hidden"]').name = `landing_settings[sections][${index}][type]`;

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

                    card.querySelector('.move-up-btn').disabled = (index === 0);
                    card.querySelector('.move-down-btn').disabled = (index === cards.length - 1);
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
        }
    </script>
@endpush
