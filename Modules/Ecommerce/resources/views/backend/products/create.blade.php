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
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
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

                                    <!-- 4 Trust Highlights Badges -->
                                    <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-dark" style="font-size: 15px;"><i class="fas fa-award text-warning me-1"></i> ২. পণ্যের পাশের ৪টি ট্রাস্ট ব্যাজ (Trust Highlights Grid)</h5>
                                    <div class="row">
                                        @for ($i = 1; $i <= 4; $i++)
                                            @php
                                                $defaultIcons = [1 => 'fas fa-undo-alt', 2 => 'fas fa-hand-holding-usd', 3 => 'fas fa-headset', 4 => 'fas fa-shipping-fast'];
                                                $defaultTitles = [1 => '৭ দিনের রিটার্ন', 2 => 'হাতে পেয়ে পেমেন্ট', 3 => 'অনলাইন সাপোর্ট', 4 => 'সারাদেশে ডেলিভারি'];
                                                $defaultDescs = [1 => 'সহজ এক্সচেঞ্জ সুবিধা', 2 => 'ক্যাশ অন ডেলিভারি', 3 => '২৪/৭ কাস্টমার কেয়ার', 4 => 'দ্রুত ও নিরাপদ ডেলিভারি'];
                                            @endphp
                                            <div class="col-md-6 col-12 mb-3">
                                                <div class="p-3 border rounded-3 bg-white shadow-sm">
                                                    <span class="badge bg-secondary mb-2">ব্যাজ {{ $i }}</span>
                                                    <div class="form-group mb-2">
                                                        <label class="small fw-bold">ফন্ট-অসাম আইকন ক্লাস (FontAwesome Icon Class)</label>
                                                        <input type="text" name="landing_settings[badge_{{ $i }}_icon]" class="form-control form-control-sm" value="{{ old('landing_settings.badge_' . $i . '_icon', $defaultIcons[$i]) }}">
                                                    </div>
                                                    <div class="form-group mb-2">
                                                        <label class="small fw-bold">টাইটেল (Title)</label>
                                                        <input type="text" name="landing_settings[badge_{{ $i }}_title]" class="form-control form-control-sm" value="{{ old('landing_settings.badge_' . $i . '_title', $defaultTitles[$i]) }}">
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label class="small fw-bold">বর্ণনা (Subtitle/Desc)</label>
                                                        <input type="text" name="landing_settings[badge_{{ $i }}_desc]" class="form-control form-control-sm" value="{{ old('landing_settings.badge_' . $i . '_desc', $defaultDescs[$i]) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>

                                    <!-- Why Choose Us Features -->
                                    <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-dark" style="font-size: 15px;"><i class="fas fa-check-circle text-success me-1"></i> ৩. আমাদের থেকে কেন সংগ্রহ করবেন? (Why Choose Us Section)</h5>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label class="fw-bold">সেকশন টাইটেল (Trust Section Title)</label>
                                                <input type="text" name="landing_settings[trust_title]" class="form-control" value="{{ old('landing_settings.trust_title', 'আমাদের থেকে কেন সংগ্রহ করবেন?') }}" placeholder="আমাদের থেকে কেন সংগ্রহ করবেন?">
                                            </div>
                                        </div>
                                        @for ($i = 1; $i <= 4; $i++)
                                            @php
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
                                            @endphp
                                            <div class="col-md-6 col-12 mb-3">
                                                <div class="p-3 border rounded-3 bg-white shadow-sm">
                                                    <span class="badge bg-info text-white mb-2">ফিচার {{ $i }}</span>
                                                    <div class="form-group mb-2">
                                                        <label class="small fw-bold">টাইটেল (Feature Title)</label>
                                                        <input type="text" name="landing_settings[feature_{{ $i }}_title]" class="form-control form-control-sm" value="{{ old('landing_settings.feature_' . $i . '_title', $defaultFeatureTitles[$i]) }}">
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label class="small fw-bold">বর্ণনা (Feature Description)</label>
                                                        <textarea name="landing_settings[feature_{{ $i }}_desc]" class="form-control form-control-sm" rows="2">{{ old('landing_settings.feature_' . $i . '_desc', $defaultFeatureDescs[$i]) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>

                                    <!-- Product Features Section -->
                                    <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-dark" style="font-size: 15px;"><i class="fas fa-list-ul text-primary me-1"></i> ৪. আমাদের প্রোডাক্টের বৈশিষ্ট্য (Product Features List)</h5>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label class="fw-bold">সেকশন টাইটেল (Features Section Title)</label>
                                                <input type="text" name="landing_settings[product_features_title]" class="form-control" value="{{ old('landing_settings.product_features_title', 'আমাদের প্রোডাক্টের বৈশিষ্ট্য') }}" placeholder="আমাদের প্রোডাক্টের বৈশিষ্ট্য">
                                            </div>
                                        </div>
                                        @for ($i = 1; $i <= 6; $i++)
                                            @php
                                                $defaultProductFeatures = [
                                                    1 => 'অটো অন/অফ: মানুষ থাকলেই আলো জ্বলবে, চলে গেলে অটো বন্ধ।',
                                                    2 => 'ইনফ্রারেড সেন্সর: দূর থেকেই নিখুঁতভাবে মুভমেন্ট শনাক্ত করে।',
                                                    3 => 'স্মার্ট ডে-লাইট সেন্সর: দিনের আলো থাকলে এটি জ্বলবে না, ফলে আরও বিদ্যুৎ সাশ্রয় হবে।',
                                                    4 => 'সহজ ইনস্টলেশন: কোনো টেকনিশিয়ান লাগবে না, সাধারণ হোল্ডারের মতোই লাগিয়ে নিন।',
                                                    5 => 'মাল্টি-পারপাস: বাথরুম, সিঁড়ি, করিডোর, বারান্দা, স্টোর রুম বা গ্যারেজের জন্য সেরা।',
                                                    6 => ''
                                                ];
                                            @endphp
                                            <div class="col-md-6 col-12 mb-3">
                                                <div class="p-3 border rounded-3 bg-white shadow-sm">
                                                    <span class="badge bg-primary text-white mb-2">বৈশিষ্ট্য {{ $i }}</span>
                                                    <div class="form-group mb-0">
                                                        <label class="small fw-bold">ফিচার বিবরণ (Feature Text)</label>
                                                        <textarea name="landing_settings[product_feature_{{ $i }}]" class="form-control form-control-sm" rows="2" placeholder="প্রোডাক্টের বৈশিষ্ট্যটি এখানে লিখুন">{{ old('landing_settings.product_feature_' . $i, $defaultProductFeatures[$i]) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>

                                    <!-- User Problems Section -->
                                    <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-dark" style="font-size: 15px;"><i class="fas fa-exclamation-triangle text-danger me-1"></i> ৪.৫. এই সমস্যাগুলো কি আপনারও আছে? (User Pain Points / Problems List)</h5>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label class="fw-bold">সেকশন টাইটেল (Problems Section Title)</label>
                                                <input type="text" name="landing_settings[problems_title]" class="form-control" value="{{ old('landing_settings.problems_title', 'এই সমস্যাগুলো কি আপনারও আছে?') }}" placeholder="এই সমস্যাগুলো কি আপনারও আছে?">
                                            </div>
                                        </div>
                                        @for ($i = 1; $i <= 6; $i++)
                                            @php
                                                $defaultProblems = [
                                                    1 => 'অন্ধকারে বাথরুমে বা সিঁড়িতে সুইচ খুঁজতে গিয়ে পড়ে যাওয়ার ভয় পান?',
                                                    2 => 'অপ্রয়োজনে লাইট অন থাকার কারণে প্রতি মাসে বিদ্যুৎ বিল বেশি আসে?',
                                                    3 => 'রাতে অন্ধকারে হাতড়ে লাইটের সুইচ খুঁজে পেতে কষ্ট হয়?',
                                                    4 => 'সুইচ অন-অফ করার আলসেমির কারণে বিদ্যুৎ অপচয় হচ্ছে?',
                                                    5 => '',
                                                    6 => ''
                                                ];
                                            @endphp
                                            <div class="col-md-6 col-12 mb-3">
                                                <div class="p-3 border rounded-3 bg-white shadow-sm">
                                                    <span class="badge bg-danger text-white mb-2">সমস্যা {{ $i }}</span>
                                                    <div class="form-group mb-0">
                                                        <label class="small fw-bold">সমস্যার বিবরণ (Problem Text)</label>
                                                        <textarea name="landing_settings[problem_{{ $i }}]" class="form-control form-control-sm" rows="2" placeholder="সমস্যার বিবরণ এখানে লিখুন">{{ old('landing_settings.problem_' . $i, $defaultProblems[$i]) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>

                                    <!-- Product Benefits Section -->
                                    <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-dark" style="font-size: 15px;"><i class="fas fa-magic text-success me-1"></i> ৫. প্রোডাক্টের সুবিধা ও কার্যকারিতা (Product Benefits List)</h5>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label class="fw-bold">সেকশন টাইটেল (Benefits Section Title)</label>
                                                <input type="text" name="landing_settings[benefits_title]" class="form-control" value="{{ old('landing_settings.benefits_title', 'বৈশিষ্ট্যগুলো কি কি জানতে চান?') }}" placeholder="বৈশিষ্ট্যগুলো কি কি জানতে চান?">
                                            </div>
                                        </div>
                                        @for ($i = 1; $i <= 6; $i++)
                                            @php
                                                $defaultBenefits = [
                                                    1 => 'মানুষের উপস্থিতি টের পেয়ে স্বয়ংক্রিয়ভাবে লাইট জ্বলবে।',
                                                    2 => 'বিদ্যুৎ বিল সাশ্রয় করতে সাহায্য করবে।',
                                                    3 => 'চোর ডাকাত থেকে আপনার বাড়ি সুরক্ষিত রাখতে ভূমিকা রাখবে।',
                                                    4 => 'অন্ধকারে সুইচ খোঁজার ঝামেলা থেকে মুক্তি দেবে।',
                                                    5 => 'বাসার শিশু ও বয়স্কদের জন্য রাতে চলাচলে নিরাপত্তা দেবে।',
                                                    6 => ''
                                                ];
                                            @endphp
                                            <div class="col-md-6 col-12 mb-3">
                                                <div class="p-3 border rounded-3 bg-white shadow-sm">
                                                    <span class="badge bg-success text-white mb-2">সুবিধা {{ $i }}</span>
                                                    <div class="form-group mb-0">
                                                        <label class="small fw-bold">সুবিধার বিবরণ (Benefit Text)</label>
                                                        <textarea name="landing_settings[benefit_{{ $i }}]" class="form-control form-control-sm" rows="2" placeholder="সুবিধার বিবরণ এখানে লিখুন">{{ old('landing_settings.benefit_' . $i, $defaultBenefits[$i]) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>

                                    <!-- Package Includes Section -->
                                    <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-dark" style="font-size: 15px;"><i class="fas fa-box-open text-warning me-1"></i> ৬. প্যাকেজের সাথে যা যা পাবেন (Package Includes)</h5>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label class="fw-bold">সেকশন টাইটেল (Package Title)</label>
                                                <input type="text" name="landing_settings[package_includes_title]" class="form-control" value="{{ old('landing_settings.package_includes_title', 'প্যাকেজের সাথে যা যা পাবেন') }}" placeholder="প্যাকেজের সাথে যা যা পাবেন">
                                            </div>
                                        </div>
                                        @for ($i = 1; $i <= 6; $i++)
                                            @php
                                                $defaultIncludes = [
                                                    1 => '১টি পিআইআর মোশন সেন্সর হোল্ডার (PIR Motion Sensor Holder)',
                                                    2 => 'প্রয়োজনীয় স্ক্রু ও ওয়াল প্লাগ (Mounting Screws & Plugs)',
                                                    3 => 'ব্যবহারকারী নির্দেশিকা বই (User Manual Guide)',
                                                    4 => 'টেস্টিং ওয়ারেন্টি কার্ড (Testing Warranty Card)',
                                                    5 => '',
                                                    6 => ''
                                                ];
                                            @endphp
                                            <div class="col-md-6 col-12 mb-3">
                                                <div class="p-3 border rounded-3 bg-white shadow-sm">
                                                    <span class="badge bg-warning text-dark mb-2">আইটেম {{ $i }}</span>
                                                    <div class="form-group mb-0">
                                                        <label class="small fw-bold">আইটেমের বিবরণ (Item Text)</label>
                                                        <textarea name="landing_settings[package_include_{{ $i }}]" class="form-control form-control-sm" rows="2" placeholder="আইটেম বিবরণ এখানে লিখুন">{{ old('landing_settings.package_include_' . $i, $defaultIncludes[$i]) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>

                                    <!-- FAQ Section -->
                                    <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-dark" style="font-size: 15px;"><i class="fas fa-question-circle text-info me-1"></i> ৭. কিছু সাধারণ প্রশ্ন (Frequently Asked Questions - FAQ)</h5>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label class="fw-bold">সেকশন টাইটেল (FAQ Section Title)</label>
                                                <input type="text" name="landing_settings[faqs_title]" class="form-control" value="{{ old('landing_settings.faqs_title', 'কিছু সাধারণ প্রশ্ন') }}" placeholder="কিছু সাধারণ প্রশ্ন">
                                            </div>
                                        </div>
                                        @for ($i = 1; $i <= 4; $i++)
                                            @php
                                                $defaultFAQQs = [
                                                    1 => 'সেন্সর হোল্ডারটি লাগাতে কি অতিরিক্ত তার লাগবে?',
                                                    2 => 'এর মোশন ডিটেকশন রেঞ্জ কত দূর?',
                                                    3 => 'দিনের বেলায় কি এটি জ্বলবে?',
                                                    4 => 'সব ধরণের বাল্ব কি এতে ব্যবহার করা যাবে?'
                                                ];
                                                $defaultFAQAs = [
                                                    1 => 'জি না, এটি সাধারণ লাইট হোল্ডারের মতোই সরাসরি হোল্ডারে প্যাঁচ দিয়ে বসানো যায়। কোনো তার বা টেকনিশিয়ান লাগবে না।',
                                                    2 => 'এটি প্রায় ১০-১২ ফুট দূর থেকে মানুষের উপস্থিতি ডিটেক্ট করতে পারে এবং আলো অন করতে পারে।',
                                                    3 => 'না, এতে ডে-লাইট সেন্সর রয়েছে যা দিনের আলোতে লাইট অফ রাখে এবং কেবল অন্ধকারেই কাজ করে বিদ্যুৎ বাঁচায়।',
                                                    4 => 'জি হ্যাঁ, যেকোনো রেগুলার এলইডি বা এনার্জি সেভিং বাল্ব এতে খুব সহজেই ব্যবহার করতে পারবেন।'
                                                ];
                                            @endphp
                                            <div class="col-12 mb-3">
                                                <div class="p-3 border rounded-3 bg-white shadow-sm">
                                                    <span class="badge bg-info text-white mb-2">প্রশ্ন ও উত্তর {{ $i }}</span>
                                                    <div class="form-group mb-2">
                                                        <label class="small fw-bold">প্রশ্ন (Question)</label>
                                                        <input type="text" name="landing_settings[faq_q_{{ $i }}]" class="form-control form-control-sm" value="{{ old('landing_settings.faq_q_' . $i, $defaultFAQQs[$i]) }}" placeholder="প্রশ্ন লিখুন">
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label class="small fw-bold">উত্তর (Answer)</label>
                                                        <textarea name="landing_settings[faq_a_{{ $i }}]" class="form-control form-control-sm" rows="2" placeholder="উত্তর লিখুন">{{ old('landing_settings.faq_a_' . $i, $defaultFAQAs[$i]) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('ecommerce::backend.products.partials.variant-manager', ['variantRows' => $variantRows])
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Save Product</button>
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

            initializeVariantManager();
        });

        function initializeProductImageUpload({ inputId, previewId, previewContainerId, helperId, emptyMessage }) {
            const fileInput = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const previewContainer = document.getElementById(previewContainerId);
            const helper = document.getElementById(helperId);

            if (!fileInput || !preview || !previewContainer || !helper) {
                return;
            }

            fileInput.addEventListener('change', function (event) {
                const file = event.target.files[0];

                if (!file) {
                    preview.src = '#';
                    previewContainer.style.display = 'none';
                    helper.textContent = emptyMessage;
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    helper.textContent = 'Please select a valid image file.';
                    event.target.value = '';
                    preview.src = '#';
                    previewContainer.style.display = 'none';
                    return;
                }

                updatePreview(preview, previewContainer, file);
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

        function updatePreview(preview, previewContainer, file) {
            preview.src = URL.createObjectURL(file);
            previewContainer.style.display = 'block';
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

            // Toggle variants section based on checkbox
            const hasVariantsToggle = document.getElementById('has_variants_toggle');
            const variantManagerContainer = document.getElementById('variant-manager-container');

            function toggleVariantFields() {
                if (hasVariantsToggle.checked) {
                    variantManagerContainer.style.display = 'block';
                    variantManagerContainer.querySelectorAll('input, select, button').forEach(el => {
                        el.disabled = false;
                    });
                } else {
                    variantManagerContainer.style.display = 'none';
                    variantManagerContainer.querySelectorAll('input, select, button').forEach(el => {
                        el.disabled = true;
                    });
                }
            }

            if (hasVariantsToggle && variantManagerContainer) {
                hasVariantsToggle.addEventListener('change', toggleVariantFields);
                toggleVariantFields();
            }
        }
    </script>
@endpush
