@extends('layouts.admin')

@section('title', 'Course Details - Admin')

@section('content')
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Course Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.admin.courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item active">{{ $course->title }}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">{{ $course->title }}</h4>
            <p class="text-muted mb-3">{{ $course->description }}</p>
            <p class="mb-1"><strong>Category:</strong> {{ $course->category->name ?? 'Uncategorized' }}</p>
            <p class="mb-1"><strong>Instructor:</strong> {{ $course->instructor->name ?? 'N/A' }}</p>
            <p class="mb-1"><strong>Price:</strong> ৳{{ number_format($course->price, 2) }}</p>
            <p class="mb-3"><strong>Status:</strong> {{ $course->is_active ? 'Active' : 'Inactive' }}</p>

            <h5 class="mb-2">Lessons ({{ $course->lessons->count() }})</h5>
            <ul class="mb-0">
                @forelse($course->lessons as $lesson)
                    <li>{{ $lesson->title }}</li>
                @empty
                    <li class="text-muted">No lessons available.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
