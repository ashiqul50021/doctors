@extends('layouts.admin')

@section('title', 'Doctors - ' . ($siteSettings['site_name'] ?? 'Doccure Admin'))

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-7 col-auto">
                <h3 class="page-title">Doctors</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Doctors</li>
                </ul>
            </div>
            <div class="col-sm-5 col">
                <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary float-end mt-2">Add Doctor</a>
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

                    <div class="table-responsive">
                        <table class="datatable table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Doctor Name</th>
                                    <th>Speciality</th>
                                    <th>Member Since</th>
                                    <th>Account Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($doctors as $doctor)
                                    <tr>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="#" class="avatar avatar-sm me-2">
                                                    <img class="avatar-img rounded-circle"
                                                        src="{{ $doctor->profile_image ? asset($doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-01.jpg') }}"
                                                        alt="User Image">
                                                </a>
                                                <a href="#">{{ $doctor->user->name }}</a>
                                            </h2>
                                        </td>
                                        <td>{{ $doctor->speciality->name ?? 'N/A' }}</td>
                                        <td>{{ $doctor->created_at->format('d M Y') }}
                                            <br><small>{{ $doctor->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-pill bg-{{ $doctor->status === 'approved' ? 'success' : 'warning' }}-light">{{ ucfirst($doctor->status) }}</span>
                                        </td>
                                        <td>
                                            <div class="actions">
                                                <a class="btn btn-sm bg-success-light"
                                                    href="{{ route('admin.doctors.edit', $doctor->id) }}">
                                                    <i class="fe fe-pencil"></i> Edit
                                                </a>
                                                <a class="btn btn-sm bg-info-light"
                                                    href="{{ route('admin.doctors.schedule', $doctor->id) }}">
                                                    <i class="fe fe-clock"></i> Schedule
                                                </a>
                                                <form action="{{ route('admin.doctors.destroy', $doctor->id) }}" method="POST"
                                                    style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm bg-danger-light">
                                                        <i class="fe fe-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
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