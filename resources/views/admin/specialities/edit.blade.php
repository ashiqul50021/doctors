@extends('layouts.admin')

@section('title', 'Edit Speciality - Doccure Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Edit Speciality</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.specialities.index') }}">Specialities</a></li>
                <li class="breadcrumb-item active">Edit Speciality</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.specialities.update', $speciality->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row form-row">
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label>Specialities</label>
                                <input type="text" name="name" class="form-control" value="{{ $speciality->name }}" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label>Image</label>
                                <input type="file" name="image" class="form-control">
                                @if($speciality->image)
                                    <img src="{{ asset($speciality->image) }}" alt="" width="50" class="mt-2">
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ $speciality->description }}</textarea>
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
