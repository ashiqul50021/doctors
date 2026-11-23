@extends('layouts.app')

@section('title', 'Supplier Dashboard - ' . ($siteSettings['site_name'] ?? 'abcsheba'))

@section('content')
    <div class="container py-5" style="max-width: 760px;">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h3 class="mb-1">Welcome, {{ $user->name }}</h3>
                <p class="text-muted mb-4">Supplier dashboard</p>

                @if ($profile)
                    <dl class="row mb-4">
                        <dt class="col-sm-4">Company</dt>
                        <dd class="col-sm-8">{{ $profile->company_name }}</dd>
                        <dt class="col-sm-4">Phone</dt>
                        <dd class="col-sm-8">{{ $profile->phone }}</dd>
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $user->email }}</dd>
                        <dt class="col-sm-4">Trade License</dt>
                        <dd class="col-sm-8">{{ $profile->trade_license_no ?: '-' }}</dd>
                        <dt class="col-sm-4">Address</dt>
                        <dd class="col-sm-8">{{ $profile->address ?: '-' }}</dd>
                        <dt class="col-sm-4">Supplies</dt>
                        <dd class="col-sm-8">{{ $profile->products_description ?: '-' }}</dd>
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8"><span class="badge bg-success">{{ ucfirst($profile->status) }}</span></dd>
                    </dl>
                @endif

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </div>
@endsection
