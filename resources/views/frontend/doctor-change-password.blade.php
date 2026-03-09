@extends('layouts.app')

@section('title', 'Change Password - ' . ($siteSettings['site_name'] ?? 'Doccure'))

@section('content')
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                @include('frontend.includes.doctor-sidebar')
            </div>
            <div class="col-md-7 col-lg-8 col-xl-9">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Change Password</h4>

                        @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('doctor.change.password.update') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label>Current Password</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>New Password</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Confirm New Password</label>
                                <input type="password" class="form-control" name="new_password_confirmation" required>
                            </div>
                            <div class="submit-section">
                                <button type="submit" class="btn btn-primary submit-btn">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
