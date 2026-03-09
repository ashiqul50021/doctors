@extends('layouts.admin')

@section('title', 'Add Advertisement - Doccure Admin')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Add Advertisement</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.advertisements.index') }}">Advertisements</a></li>
                    <li class="breadcrumb-item active">Add Advertisement</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.advertisements.store') }}" method="POST" enctype="multipart/form-data">
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

                        <div class="row form-row">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label>Title</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label>Link (URL)</label>
                                    <input type="url" name="link" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label>Speciality (Optional)</label>
                                    <select name="speciality_id" class="form-control">
                                        <option value="">All Specialities</option>
                                        @foreach($specialities as $speciality)
                                            <option value="{{ $speciality->id }}">{{ $speciality->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label>Priority</label>
                                    <input type="number" name="priority" class="form-control" value="0" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label>Image</label>
                                    <input type="file" name="image"
                                        class="form-control @error('image') is-invalid @enderror" required>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                            checked>
                                        <label class="form-check-label" for="is_active">
                                            Is Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Save Advertisement</button>
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
            // Image Compression Logic
            $('input[name="image"]').change(function (e) {
                const file = e.target.files[0];
                if (!file) return;

                // Only compress if larger than 2MB
                if (file.size > 2 * 1024 * 1024) {
                    const $input = $(this);
                    // Add helper if not exists
                    if (!$input.next('small').length && !$input.next('.invalid-feedback').length) {
                        $input.after('<small class="form-text text-muted"></small>');
                    }
                    const $helper = $input.next('small');

                    $helper.html('<span class="text-warning"><i class="fas fa-spinner fa-spin"></i> Compressing image... (' + (file.size / 1024 / 1024).toFixed(2) + 'MB)</span>');

                    new Compressor(file, {
                        quality: 0.6,
                        maxWidth: 1920,
                        maxHeight: 1920,
                        success(result) {
                            const newFile = new File([result], file.name, {
                                type: result.type,
                                lastModified: Date.now(),
                            });

                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(newFile);
                            e.target.files = dataTransfer.files;

                            $helper.html('<span class="text-success"><i class="fas fa-check"></i> Compressed to ' + (result.size / 1024 / 1024).toFixed(2) + 'MB</span>');
                        },
                        error(err) {
                            console.error(err.message);
                            $helper.text('Compression failed: ' + err.message);
                        },
                    });
                }
            });
        });
    </script>
@endpush