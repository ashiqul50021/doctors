@extends('layouts.admin')

@section('title', 'Doctors List - Doccure Admin')

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">List of Doctors</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:(0);">Users</a></li>
                    <li class="breadcrumb-item active">Doctors</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="datatable table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Doctor Name</th>
                                    <th>Speciality</th>
                                    <th>Member Since</th>
                                    <th>Earned</th>
                                    <th>Account Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($doctors as $doctor)
                                    <tr>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="javascript:void(0);" class="avatar avatar-sm me-2">
                                                    <img class="avatar-img rounded-circle"
                                                        src="{{ $doctor->profile_image ? asset($doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-01.jpg') }}"
                                                        alt="User Image">
                                                </a>
                                                <a href="javascript:void(0);">Dr. {{ $doctor->user->name }}</a>
                                            </h2>
                                        </td>
                                        <td>{{ $doctor->speciality->name ?? 'General' }}</td>
                                        <td>{{ $doctor->created_at->format('d M Y') }}
                                            <br><small>{{ $doctor->created_at->format('h:i A') }}</small></td>
                                        <td>$0.00</td>
                                        <td>
                                            @if($doctor->status === 'pending')
                                                <div class="d-flex gap-2">
                                                    <form action="{{ route('admin.doctors.approve', $doctor->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm bg-success-light">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.doctors.reject', $doctor->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm bg-danger-light">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif($doctor->status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection