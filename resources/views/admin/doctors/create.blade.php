@extends('layouts.admin')

@section('title', 'Add Doctor - Doccure Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">Add Doctor</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.doctors.index') }}">Doctors</a></li>
                <li class="breadcrumb-item active">Add Doctor</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.doctors.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row form-row">
                        <div class="col-12 col-md-12">
                            <div class="mb-3">
                                <div class="change-avatar">
                                    <div class="profile-img">
                                        <img src="{{ asset('assets/img/doctors/doctor-thumb-01.jpg') }}" alt="User Image">
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
                            <div class="mb-3">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label>Speciality</label>
                                <select name="speciality_id" class="form-control" required>
                                    @foreach($specialities as $speciality)
                                        <option value="{{ $speciality->id }}">{{ $speciality->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label>Qualification</label>
                                <input type="text" name="qualification" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label>Consultation Fee</label>
                                <input type="number" name="consultation_fee" class="form-control">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label>Clinic Name</label>
                                <input type="text" name="clinic_name" class="form-control">
                            </div>
                        </div>
                         <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label>Clinic Address</label>
                                <input type="text" name="clinic_address" class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label>Biography</label>
                                <textarea name="bio" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured">
                                    <label class="form-check-label" for="is_featured">
                                        Featured Doctor
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Save Doctor</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
