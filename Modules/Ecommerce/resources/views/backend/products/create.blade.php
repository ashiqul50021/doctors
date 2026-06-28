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
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                                <label for="productGalleryInput">Gallery Images</label>
                                <input type="file" name="gallery[]" class="form-control" id="productGalleryInput" accept="image/*" multiple>
                                <small id="productGalleryHelper" class="form-text text-muted">Upload multiple gallery images. These will appear as thumbnails on the product details page.</small>

                                <div class="image-manager-shell mt-2" id="productGalleryPreviewContainer" style="display: none;">
                                    <div class="gallery-preview-group">
                                        <span class="gallery-preview-label">Selected Gallery Images</span>
                                        <div id="productGalleryPreviewGrid" class="gallery-preview-grid"></div>
                                    </div>
                                    <div class="gallery-empty-note">Choose files again if you want to replace the current selection before saving.</div>
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
                                    </label>
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

            initializeProductGalleryUpload({
                inputId: 'productGalleryInput',
                previewContainerId: 'productGalleryPreviewContainer',
                previewGridId: 'productGalleryPreviewGrid',
                helperId: 'productGalleryHelper',
                emptyMessage: 'Upload multiple gallery images. These will appear as thumbnails on the product details page.'
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
