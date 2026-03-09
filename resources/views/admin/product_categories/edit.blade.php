@extends('layouts.admin')

@section('title', 'Edit Category - Doccure Admin')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Edit Product Category</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.product-categories.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active">Edit Category</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('admin.product-categories.update', $productCategory->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row form-row">
                            <div class="col-12 col-sm-6">
                                <div class="mb-3">
                                    <label>Category Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $productCategory->name }}"
                                        required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="mb-3">
                                    <label>Image</label>
                                    <input type="file" name="image" class="form-control">
                                    @if($productCategory->image)
                                        <img src="{{ asset($productCategory->image) }}" alt="" width="50" class="mt-2">
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control"
                                        rows="4">{{ $productCategory->description }}</textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                            value="1" {{ $productCategory->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Is Active
                                        </label>
                                    </div>
                                </div>
                            </div>
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
            const fileInput = document.querySelector('input[name="image"]');

            if (fileInput) {
                fileInput.addEventListener('change', async function (e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    // Check if file is an image
                    if (!file.type.match(/image.*/)) return;

                    const compressedFile = await compressImage(file, {
                        quality: 0.7,
                        maxWidth: 800,
                        maxHeight: 800,
                        type: 'image/jpeg'
                    });

                    // Replace input file with compressed one
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressedFile);
                    e.target.files = dataTransfer.files;

                    console.log(`Compressed: ${(file.size / 1024).toFixed(2)}KB -> ${(compressedFile.size / 1024).toFixed(2)}KB`);
                });
            }
        });

        /**
         * Compress Image Utility
         */
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

                        // Resize logic
                        if (width > height) {
                            if (width > options.maxWidth) {
                                height *= options.maxWidth / width;
                                width = options.maxWidth;
                            }
                        } else {
                            if (height > options.maxHeight) {
                                width *= options.maxHeight / height;
                                height = options.maxHeight;
                            }
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
                            const newFile = new File([blob], file.name, {
                                type: options.type || file.type,
                                lastModified: Date.now()
                            });
                            resolve(newFile);
                        }, options.type || 'image/jpeg', options.quality || 0.7);
                    };

                    img.onerror = (error) => reject(error);
                };

                reader.onerror = (error) => reject(error);
            });
        }
    </script>
@endpush