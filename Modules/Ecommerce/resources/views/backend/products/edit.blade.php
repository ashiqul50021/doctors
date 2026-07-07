@extends('layouts.admin')

@include('ecommerce::backend.products.partials.image-manager-styles')

@section('title', 'Edit Product - abcsheba Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Edit Product</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.admin.products.index') }}">Products</a></li>
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

                <form action="{{ route('ecommerce.admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row form-row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
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
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Price</label>
                                <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                            </div>
                        </div>
                         <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Sale Price (Optional)</label>
                                <input type="number" step="0.01" name="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price) }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Stock</label>
                                <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required>
                                <small class="text-muted d-block mt-1">Used for simple products. Active variants below will control stock automatically.</small>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="productImageInput">Primary Image</label>
                                <input type="file" name="image" class="form-control" id="productImageInput" accept="image/*">
                                <small id="productImageHelper" class="form-text text-muted">
                                    {{ $product->image ? 'Choose a new main image to replace the current one. Selected image will preview instantly and be compressed before upload.' : 'Select the main product image. Large files will be compressed automatically before upload.' }}
                                </small>
                                <div class="image-manager-shell mt-2 single-image-preview" id="productImagePreviewContainer" style="{{ $product->image ? '' : 'display: none;' }}">
                                    <img id="productImagePreview"
                                        src="{{ $product->image ? (\Illuminate\Support\Str::startsWith($product->image, ['http://', 'https://']) ? $product->image : asset($product->image)) : '#' }}"
                                        alt="Product Preview" class="img-thumbnail">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="productGalleryInput">Gallery Images</label>
                                <input type="file" name="gallery[]" class="form-control" id="productGalleryInput" accept="image/*" multiple>
                                <small id="productGalleryHelper" class="form-text text-muted">
                                    Add more gallery images here. Existing gallery images can be removed below.
                                </small>

                                <div class="image-manager-shell mt-2">
                                    <div class="gallery-preview-group" id="existingGallerySection" style="{{ $existingGalleryImages->isEmpty() ? 'display: none;' : '' }}">
                                        <span class="gallery-preview-label">Current Gallery</span>
                                        <div id="existingGalleryGrid" class="gallery-preview-grid">
                                            @foreach($existingGalleryImages as $imagePath)
                                                <div class="gallery-preview-card" data-gallery-path="{{ $imagePath }}">
                                                    <img src="{{ \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://']) ? $imagePath : asset($imagePath) }}"
                                                        alt="Gallery image {{ $loop->iteration }}">
                                                    <div class="gallery-preview-meta">
                                                        <strong>Gallery image {{ $loop->iteration }}</strong>
                                                    </div>
                                                    <div class="gallery-preview-actions">
                                                        <button type="button" class="btn btn-sm btn-outline-danger js-remove-existing-gallery" data-path="{{ $imagePath }}">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="gallery-preview-group" id="productGalleryPreviewContainer" style="display: none;">
                                        <span class="gallery-preview-label">New Gallery Uploads</span>
                                        <div id="productGalleryPreviewGrid" class="gallery-preview-grid"></div>
                                    </div>

                                    <div class="gallery-empty-note">Choose files again if you want to replace the current new upload selection before saving.</div>
                                </div>

                                <div id="removedGalleryInputs"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Is Active
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        Featured Product
                                    </label>
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
                                                <input type="text" name="landing_settings[countdown_title]" class="form-control" value="{{ old('landing_settings.countdown_title', $product->landing_settings['countdown_title'] ?? 'আজকের বিশেষ ছাড় অফার!') }}" placeholder="আজকের বিশেষ ছাড় অফার!">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label class="fw-bold">অফার সাবটাইটেল (Countdown Subtitle)</label>
                                                <input type="text" name="landing_settings[countdown_subtitle]" class="form-control" value="{{ old('landing_settings.countdown_subtitle', $product->landing_settings['countdown_subtitle'] ?? 'অফারটি শেষ হতে আর মাত্র সময় বাকি আছে:') }}" placeholder="অফারটি শেষ হতে আর মাত্র সময় বাকি আছে:">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label class="fw-bold">অফার সময় - ঘণ্টায় (Countdown Hours)</label>
                                                <input type="number" name="landing_settings[countdown_hours]" class="form-control" value="{{ old('landing_settings.countdown_hours', $product->landing_settings['countdown_hours'] ?? 3) }}" placeholder="3">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Product Video -->
                                    <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-dark" style="font-size: 15px;"><i class="fab fa-youtube text-danger me-1"></i> ১.৫. প্রোডাক্ট ভিডিও (Product Video - Optional)</h5>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="fw-bold">ইউটিউব ভিডিও লিংক (YouTube Video URL)</label>
                                                <input type="text" name="landing_settings[youtube_video_url]" class="form-control" value="{{ old('landing_settings.youtube_video_url', $product->landing_settings['youtube_video_url'] ?? '') }}" placeholder="যেমন: https://www.youtube.com/watch?v=xxxxxx">
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
                                                        <input type="text" name="landing_settings[badge_{{ $i }}_icon]" class="form-control form-control-sm" value="{{ old('landing_settings.badge_' . $i . '_icon', $product->landing_settings['badge_' . $i . '_icon'] ?? $defaultIcons[$i]) }}">
                                                    </div>
                                                    <div class="form-group mb-2">
                                                        <label class="small fw-bold">টাইটেল (Title)</label>
                                                        <input type="text" name="landing_settings[badge_{{ $i }}_title]" class="form-control form-control-sm" value="{{ old('landing_settings.badge_' . $i . '_title', $product->landing_settings['badge_' . $i . '_title'] ?? $defaultTitles[$i]) }}">
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label class="small fw-bold">বর্ণনা (Subtitle/Desc)</label>
                                                        <input type="text" name="landing_settings[badge_{{ $i }}_desc]" class="form-control form-control-sm" value="{{ old('landing_settings.badge_' . $i . '_desc', $product->landing_settings['badge_' . $i . '_desc'] ?? $defaultDescs[$i]) }}">
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
                                                <input type="text" name="landing_settings[trust_title]" class="form-control" value="{{ old('landing_settings.trust_title', $product->landing_settings['trust_title'] ?? 'আমাদের থেকে কেন সংগ্রহ করবেন?') }}" placeholder="আমাদের থেকে কেন সংগ্রহ করবেন?">
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
                                                        <input type="text" name="landing_settings[feature_{{ $i }}_title]" class="form-control form-control-sm" value="{{ old('landing_settings.feature_' . $i . '_title', $product->landing_settings['feature_' . $i . '_title'] ?? $defaultFeatureTitles[$i]) }}">
                                                    </div>
                                                    <div class="form-group mb-0">
                                                        <label class="small fw-bold">বর্ণনা (Feature Description)</label>
                                                        <textarea name="landing_settings[feature_{{ $i }}_desc]" class="form-control form-control-sm" rows="2">{{ old('landing_settings.feature_' . $i . '_desc', $product->landing_settings['feature_' . $i . '_desc'] ?? $defaultFeatureDescs[$i]) }}</textarea>
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

            initializeProductGalleryUpload({
                inputId: 'productGalleryInput',
                previewContainerId: 'productGalleryPreviewContainer',
                previewGridId: 'productGalleryPreviewGrid',
                helperId: 'productGalleryHelper',
                emptyMessage: 'Add more gallery images here. Existing gallery images can be removed below.'
            });

            initializeExistingGalleryRemoval({
                sectionId: 'existingGallerySection',
                gridId: 'existingGalleryGrid',
                removedInputsContainerId: 'removedGalleryInputs'
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

        function initializeProductGalleryUpload({ inputId, previewContainerId, previewGridId, helperId, emptyMessage }) {
            const fileInput = document.getElementById(inputId);
            const previewContainer = document.getElementById(previewContainerId);
            const previewGrid = document.getElementById(previewGridId);
            const helper = document.getElementById(helperId);

            if (!fileInput || !previewContainer || !previewGrid || !helper) {
                return;
            }

            fileInput.addEventListener('change', function (event) {
                const files = Array.from(event.target.files || []);

                if (!files.length) {
                    previewGrid.innerHTML = '';
                    previewContainer.style.display = 'none';
                    helper.textContent = emptyMessage;
                    return;
                }

                const validFiles = files.filter(file => file.type.startsWith('image/'));

                if (!validFiles.length) {
                    previewGrid.innerHTML = '';
                    previewContainer.style.display = 'none';
                    helper.innerHTML = '<span class="text-danger">Please select valid image files.</span>';
                    event.target.value = '';
                    return;
                }

                if (validFiles.length !== files.length) {
                    const dataTransfer = new DataTransfer();
                    validFiles.forEach((file) => dataTransfer.items.add(file));
                    event.target.files = dataTransfer.files;
                }

                renderGalleryPreview(previewGrid, validFiles);
                previewContainer.style.display = 'block';

                let totalSize = validFiles.reduce((sum, f) => sum + f.size, 0);
                helper.innerHTML = `<span class="text-success">${validFiles.length} image(s) ready for upload. Total size: ${(totalSize / 1024).toFixed(2)} KB.</span>`;
            });
        }

        function initializeExistingGalleryRemoval({ sectionId, gridId, removedInputsContainerId }) {
            const section = document.getElementById(sectionId);
            const grid = document.getElementById(gridId);
            const removedInputsContainer = document.getElementById(removedInputsContainerId);

            if (!section || !grid || !removedInputsContainer) {
                return;
            }

            grid.addEventListener('click', function (event) {
                const removeButton = event.target.closest('.js-remove-existing-gallery');

                if (!removeButton) {
                    return;
                }

                const path = removeButton.dataset.path;
                const card = removeButton.closest('[data-gallery-path]');

                if (!path || !card) {
                    return;
                }

                const alreadyRemoved = Array.from(removedInputsContainer.querySelectorAll('input[name="removed_gallery[]"]'))
                    .some((input) => input.value === path);

                if (!alreadyRemoved) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'removed_gallery[]';
                    input.value = path;
                    removedInputsContainer.appendChild(input);
                }

                card.remove();

                if (!grid.children.length) {
                    section.style.display = 'none';
                }
            });
        }

        function updatePreview(preview, previewContainer, file) {
            preview.src = URL.createObjectURL(file);
            previewContainer.style.display = 'block';
        }

        function renderGalleryPreview(previewGrid, files) {
            previewGrid.innerHTML = '';

            files.forEach((file) => {
                const objectUrl = URL.createObjectURL(file);
                const card = document.createElement('div');
                card.className = 'gallery-preview-card';
                card.innerHTML = `
                    <img src="${objectUrl}" alt="${file.name}">
                    <div class="gallery-preview-meta">
                        <strong>${file.name}</strong><br>
                        ${(file.size / 1024).toFixed(2)} KB
                    </div>
                `;

                const image = card.querySelector('img');
                image.addEventListener('load', function () {
                    URL.revokeObjectURL(objectUrl);
                }, { once: true });

                previewGrid.appendChild(card);
            });
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
