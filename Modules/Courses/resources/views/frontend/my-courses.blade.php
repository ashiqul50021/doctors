@extends('layouts.app')

@section('title', 'My Courses - Doccure')

@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h3 class="mb-4">My Courses</h3>
                </div>
            </div>
            <div class="row">
                @forelse($enrollments as $enrollment)
                    <div class="col-md-6 col-lg-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title mb-2">{{ $enrollment->course->title ?? 'Course' }}</h5>
                                <p class="text-muted mb-3">
                                    Progress: {{ $enrollment->progress ?? 0 }}%
                                </p>
                                @if(isset($enrollment->course) && $enrollment->course->lessons->isNotEmpty())
                                    <a class="btn btn-primary btn-sm"
                                        href="{{ route('courses.lesson', [$enrollment->course->id, $enrollment->course->lessons->first()->id]) }}">
                                        Continue
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info mb-0">You are not enrolled in any course yet.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
