@extends('layouts.admin')

@section('title', 'Add Category - Doccure Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Add Product Category</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.admin.product-categories.index') }}">Categories</a></li>
                <li class="breadcrumb-item active">Add Category</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
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

                <form action="{{ route('ecommerce.admin.product-categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row form-row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label>Category Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label for="categoryImageInput">Image</label>
                                <input type="file" name="image" class="form-control" id="categoryImageInput" accept="image/*">
                                <small id="categoryImageHelper" class="form-text text-muted">Select an image to preview. Large files will be compressed automatically before upload.</small>
                                <div class="mt-2" id="categoryImagePreviewContainer" style="display: none;">
                                    <img id="categoryImagePreview" src="#" alt="Category Preview" class="img-thumbnail"
                                        style="max-width: 150px; max-height: 150px;">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Save Category</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initializeCategoryImageUpload({
                inputId: 'categoryImageInput',
                previewId: 'categoryImagePreview',
                previewContainerId: 'categoryImagePreviewContainer',
                helperId: 'categoryImageHelper',
                emptyMessage: 'Select an image to preview. Large files will be compressed automatically before upload.'
            });
        });

        function initializeCategoryImageUpload({ inputId, previewId, previewContainerId, helperId, emptyMessage }) {
            const fileInput = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const previewContainer = document.getElementById(previewContainerId);
            const helper = document.getElementById(helperId);

            if (!fileInput || !preview || !previewContainer || !helper) {
                return;
            }

            fileInput.addEventListener('change', async function (event) {
                const file = event.target.files[0];

                if (!file) {
                    preview.src = '#';
                    previewContainer.style.display = 'none';
                    helper.textContent = emptyMessage;
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    helper.textContent = 'Please select a valid image file.';
                    return;
                }

                helper.innerHTML = '<span class="text-warning">Processing image...</span>';

                try {
                    const processedFile = await compressImage(file, {
                        quality: 0.7,
                        maxWidth: 800,
                        maxHeight: 800,
                        type: 'image/jpeg'
                    });

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(processedFile);
                    event.target.files = dataTransfer.files;

                    updatePreview(preview, previewContainer, processedFile);

                    const originalSize = (file.size / 1024).toFixed(2);
                    const compressedSize = (processedFile.size / 1024).toFixed(2);
                    helper.innerHTML = `<span class="text-success">Preview updated. Compressed ${originalSize} KB to ${compressedSize} KB.</span>`;
                } catch (error) {
                    console.error(error);
                    helper.innerHTML = '<span class="text-danger">Image processing failed. Original file will be uploaded.</span>';
                    updatePreview(preview, previewContainer, file);
                }
            });
        }

        function updatePreview(preview, previewContainer, file) {
            preview.src = URL.createObjectURL(file);
            previewContainer.style.display = 'block';
        }

        function compressImage(file, options) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);

                reader.onload = (event) => {
                    const img = new Image();
                    img.src = event.target.result;

                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;

                        if (width > height && width > options.maxWidth) {
                            height *= options.maxWidth / width;
                            width = options.maxWidth;
                        } else if (height >= width && height > options.maxHeight) {
                            width *= options.maxHeight / height;
                            height = options.maxHeight;
                        }

                        canvas.width = width;
                        canvas.height = height;

                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            if (!blob) {
                                reject(new Error('Canvas is empty'));
                                return;
                            }

                            resolve(new File([blob], file.name, {
                                type: options.type || file.type,
                                lastModified: Date.now()
                            }));
                        }, options.type || 'image/jpeg', options.quality || 0.7);
                    };

                    img.onerror = (error) => reject(error);
                };

                reader.onerror = (error) => reject(error);
            });
        }
    </script>
@endpush
