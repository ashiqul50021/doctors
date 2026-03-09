@extends('layouts.app')

@section('title', 'Health Courses - Doccure')

@section('content')

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/courses.css') }}">
    @endpush

    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Courses</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">Health Courses</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb -->

    <!-- Page Content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <!-- Search Filter (Optional, can be expanded) -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <form action="{{ route('courses.index') }}" method="GET">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <input type="text" class="form-control" name="search"
                                                placeholder="Search courses..." value="{{ request('search') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <select class="form-control select" name="category">
                                                <option value="">All Categories</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary w-100" type="submit">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Courses Grid -->
                    <div class="row">
                        @forelse($courses as $course)
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="course-card">
                                    <div class="course-thumbnail">
                                        <a href="{{ route('courses.show', $course->id) }}">
                                            @if(Str::startsWith($course->image, 'assets'))
                                                <img src="{{ asset($course->image) }}" class="img-fluid" alt="{{ $course->title }}">
                                            @else
                                                <img src="{{ asset('storage/' . $course->image) }}" class="img-fluid"
                                                    alt="{{ $course->title }}">
                                            @endif
                                        </a>
                                        <div class="play-overlay">
                                            <a href="{{ route('courses.show', $course->id) }}">
                                                <i class="fas fa-play-circle"></i>
                                            </a>
                                        </div>
                                        <span class="course-badge {{ $course->price == 0 ? 'free' : 'premium' }}">
                                            {{ $course->price == 0 ? 'Free' : '৳' . number_format($course->price) }}
                                        </span>
                                    </div>
                                    <div class="course-content">
                                        <div class="course-meta">
                                            <span><i class="fas fa-clock"></i> {{ $course->duration_hours }} hrs</span>
                                            <span><i class="fas fa-book"></i> {{ $course->lessons->count() }} Lessons</span>
                                        </div>
                                        <h4 class="course-title">
                                            <a href="{{ route('courses.show', $course->id) }}">{{ $course->title }}</a>
                                        </h4>
                                        <p class="course-desc text-muted mb-3"
                                            style="font-size: 13px; line-height: 1.5; height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                            {{ $course->short_description }}
                                        </p>
                                        <div class="course-footer">
                                            <div class="course-instructor">
                                                <img src="{{ $course->instructor && $course->instructor->profile ? asset('storage/' . $course->instructor->profile) : asset('assets/img/doctors/doctor-thumb-01.jpg') }}"
                                                    alt="Instructor">
                                                <span>Dr. {{ $course->instructor->name ?? 'Instructor' }}</span>
                                            </div>
                                            <a href="{{ route('courses.show', $course->id) }}" class="btn-enroll">
                                                {{ $course->price == 0 ? 'Enroll' : 'Buy Now' }} <i
                                                    class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center p-5">
                                        <h3>No Courses Found</h3>
                                        <p class="text-muted">Try adjusting your search or filters.</p>
                                        <a href="{{ route('courses.index') }}" class="btn btn-primary mt-3">View All Courses</a>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="blog-pagination">
                                {{ $courses->links() }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection