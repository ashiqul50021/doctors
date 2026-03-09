@extends('layouts.admin')

@section('title', 'Advertisements - Doccure Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-7 col-auto">
            <h3 class="page-title">Advertisements</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Advertisements</li>
            </ul>
        </div>
        <div class="col-sm-5 col">
            <a href="{{ route('admin.advertisements.create') }}" class="btn btn-primary float-end mt-2">Add Ad</a>
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
                                <th>#</th>
                                <th>Ad Title</th>
                                <th>Speciality</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($advertisements as $ad)
                            <tr>
                                <td>#AD{{ $ad->id }}</td>
                                <td>
                                    <h2 class="table-avatar">
                                        <a href="#" class="avatar avatar-sm me-2">
                                            <img class="avatar-img" src="{{ asset($ad->image) }}" alt="Ad Image">
                                        </a>
                                        <a href="#">{{ $ad->title }}</a>
                                    </h2>
                                </td>
                                <td>{{ $ad->speciality->name ?? 'All Specialities' }}</td>
                                <td>{{ $ad->priority }}</td>
                                <td>
                                    <div class="status-toggle">
                                        <input type="checkbox" id="status_{{ $ad->id }}" class="check" {{ $ad->is_active ? 'checked' : '' }} disabled>
                                        <label for="status_{{ $ad->id }}" class="checktoggle">checkbox</label>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="actions">
                                        <a class="btn btn-sm bg-success-light" href="{{ route('admin.advertisements.edit', $ad->id) }}">
                                            <i class="fe fe-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.advertisements.destroy', $ad->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
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
