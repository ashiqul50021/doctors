@extends('layouts.admin')

@section('title', 'Edit Doctor - Doccure Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Edit Doctor</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.doctors.index') }}">Doctors</a></li>
                <li class="breadcrumb-item active">Edit Doctor</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.doctors.update', $doctor->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row form-row">
                        <div class="col-12 col-md-12">
                            <div class="form-group">
                                <div class="change-avatar">
                                    <div class="profile-img">
                                        <img src="{{ $doctor->profile_image ? asset('storage/'.$doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-01.jpg') }}" alt="User Image">
                                    </div>
                                    <div class="upload-img">
                                        <div class="change-photo-btn">
                                            <span><i class="fa fa-upload"></i> Upload Photo</span>
                                            <input type="file" class="upload" name="image">
                                        </div>
                                        <small class="form-text text-muted">Allowed JPG, GIF or PNG. Max size of 2MB</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $doctor->user->name }}" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" value="{{ $doctor->user->email }}" readonly>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Speciality</label>
                                <select name="speciality_id" class="form-control" required>
                                    @foreach($specialities as $speciality)
                                        <option value="{{ $speciality->id }}" {{ $doctor->speciality_id == $speciality->id ? 'selected' : '' }}>{{ $speciality->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Qualification</label>
                                <input type="text" name="qualification" class="form-control" value="{{ $doctor->qualification }}" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Consultation Fee</label>
                                <input type="number" name="consultation_fee" class="form-control" value="{{ $doctor->consultation_fee }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Clinic Name</label>
                                <input type="text" name="clinic_name" class="form-control" value="{{ $doctor->clinic_name }}">
                            </div>
                        </div>
                         <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Clinic Address</label>
                                <input type="text" name="clinic_address" class="form-control" value="{{ $doctor->clinic_address }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Biography</label>
                                <textarea name="bio" class="form-control" rows="4">{{ $doctor->bio }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" {{ $doctor->is_featured ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        Featured Doctor
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
