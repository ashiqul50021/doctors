@extends('layouts.admin')

@section('title', 'Courses - Admin')

@section('content')
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Courses</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Courses</li>
                </ul>
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="{{ route('courses.admin.courses.create') }}" class="btn btn-primary btn-sm"><i
                        class="fa fa-plus"></i> Add Course</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Instructor</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($courses as $course)
                                    <tr>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="{{ route('courses.show', $course->id) }}"
                                                    class="avatar avatar-sm mr-2">
                                                    <img class="avatar-img rounded"
                                                        src="{{ $course->image ? asset('storage/' . $course->image) : asset('assets/img/features/feature-01.jpg') }}"
                                                        alt="Course Image">
                                                </a>
                                                <a
                                                    href="{{ route('courses.show', $course->id) }}">{{ Str::limit($course->title, 30) }}</a>
                                            </h2>
                                        </td>
                                        <td>{{ $course->category->name ?? 'Uncategorized' }}</td>
                                        <td>
                                            @if($course->price == 0)
                                                <span class="badge badge-success">Free</span>
                                            @else
                                                ৳{{ number_format($course->price) }}
                                            @endif
                                        </td>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="#" class="avatar avatar-sm mr-2">
                                                    <img class="avatar-img rounded-circle"
                                                        src="{{ $course->instructor && $course->instructor->profile ? asset('storage/' . $course->instructor->profile) : asset('assets/img/doctors/doctor-thumb-01.jpg') }}"
                                                        alt="Instructor">
                                                </a>
                                                <a href="#">{{ $course->instructor->name ?? 'Admin' }}</a>
                                            </h2>
                                        </td>
                                        <td>
                                            <div class="status-toggle">
                                                <input type="checkbox" id="status_{{ $course->id }}" class="check" {{ $course->is_active ? 'checked' : '' }}>
                                                <label for="status_{{ $course->id }}" class="checktoggle">checkbox</label>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <div class="actions">
                                                <a class="btn btn-sm bg-success-light"
                                                    href="{{ route('courses.admin.courses.edit', $course->id) }}">
                                                    <i class="fe fe-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('courses.admin.courses.destroy', $course->id) }}"
                                                    method="POST" class="d-inline-block"
                                                    onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm bg-danger-light">
                                                        <i class="fe fe-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No courses found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection