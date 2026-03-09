@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Add Banner</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Banners</a></li>
                    <li class="breadcrumb-item active">Add Banner</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label>Banner Type <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input banner-type" type="radio" name="type" id="type_content"
                                        value="content_image" checked>
                                    <label class="form-check-label" for="type_content">
                                        Content with Image (Standard)
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input banner-type" type="radio" name="type" id="type_image"
                                        value="image_only">
                                    <label class="form-check-label" for="type_image">
                                        Image Only (Full Width)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Banner Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" name="image"
                                required>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Recommended Size: 1920x600px (Image Only) or Transparent PNG
                                (Content with Image)</small>
                        </div>

                        <!-- Image Only Link Field -->
                        <div id="image_only_link" class="mb-3" style="display: none;">
                            <label>Banner Link URL</label>
                            <input type="text" class="form-control" name="image_link"
                                placeholder="e.g. https://example.com or /search">
                            <small class="form-text text-muted">Enter the URL where the banner image should link to when
                                clicked</small>
                        </div>

                        <!-- Content Fields Wrapper -->
                        <div id="content_fields">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="title"
                                            placeholder="e.g. The Largest Online Doctor Platform">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Stats Text (Trusted By)</label>
                                        <input type="text" class="form-control" name="stats_text"
                                            placeholder="e.g. 700,000 Patients">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Subtitle / Description</label>
                                <textarea class="form-control" name="subtitle" rows="3"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Button Text</label>
                                        <input type="text" class="form-control" name="button_text"
                                            placeholder="e.g. Consult Now">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Button Link</label>
                                        <input type="text" class="form-control" name="button_link"
                                            placeholder="e.g. /search">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Order Priority</label>
                                    <input type="number" class="form-control" name="order" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Status</label>
                                    <div class="status-toggle">
                                        <input type="checkbox" id="is_active" class="check" name="is_active" checked>
                                        <label for="is_active" class="checktoggle">checkbox</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn" type="submit">Save Banner</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Compressor.js for client-side image compression -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/compressorjs/1.2.1/compressor.min.js"></script>
    <script>
        $(document).ready(function () {
            function toggleFields() {
                if ($('#type_image').is(':checked')) {
                    $('#content_fields').hide();
                    $('#image_only_link').show();
                    $('input[name="title"]').prop('required', false);
                } else {
                    $('#content_fields').show();
                    $('#image_only_link').hide();
                    $('input[name="title"]').prop('required', true);
                }
            }

            // Initial check
            toggleFields();

            // On change
            $('input[name="type"]').change(function () {
                toggleFields();
            });

            // Image Compression Logic
            $('input[name="image"]').change(function (e) {
                const file = e.target.files[0];
                if (!file) return;

                // Only compress if larger than 2MB
                if (file.size > 2 * 1024 * 1024) {
                    const $input = $(this);
                    const originalLabel = $input.next('small').text();
                    $input.next('small').html('<span class="text-warning"><i class="fas fa-spinner fa-spin"></i> Compressing image... (' + (file.size / 1024 / 1024).toFixed(2) + 'MB)</span>');

                    new Compressor(file, {
                        quality: 0.6,
                        maxWidth: 1920,
                        maxHeight: 1920,
                        success(result) {
                            // Create a new File from the Blob result
                            const newFile = new File([result], file.name, {
                                type: result.type,
                                lastModified: Date.now(),
                            });

                            // Replace the file input value
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(newFile);
                            e.target.files = dataTransfer.files;

                            $input.next('small').html('<span class="text-success"><i class="fas fa-check"></i> Compressed to ' + (result.size / 1024 / 1024).toFixed(2) + 'MB</span>');
                        },
                        error(err) {
                            console.error(err.message);
                            $input.next('small').text('Compression failed: ' + err.message);
                        },
                    });
                }
            });
        });
    </script>
@endpush