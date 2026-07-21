@extends('layouts.admin')

@section('title', 'Product Reviews - abcsheba Admin')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-7 col-auto">
            <h3 class="page-title">Product Reviews</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Product Reviews</li>
            </ul>
        </div>
        <div class="col-sm-5 col">
            <a href="{{ route('ecommerce.admin.product-reviews.create') }}" class="btn btn-primary float-right mt-2">Add Custom Review</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Reviewer</th>
                                <th>Rating</th>
                                <th style="width: 40%;">Review Content</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $review)
                            <tr>
                                <td>#{{ $review->id }}</td>
                                <td>
                                    @if($review->product)
                                        <a href="{{ route('ecommerce.products.show', $review->product->id) }}" target="_blank">
                                            {{ $review->product->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $review->reviewer_name ?? ($review->patient?->user?->name ?? 'Guest') }}</strong>
                                    @if($review->is_verified_purchase)
                                        <br><span class="badge bg-success-light">Verified Purchase</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td>
                                    @if($review->title)
                                        <strong>{{ $review->title }}</strong><br>
                                    @endif
                                    <p class="text-muted mb-0" style="white-space: normal;">{{ $review->comment }}</p>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $review->is_approved ? 'success' : 'warning' }}-light">
                                        {{ $review->is_approved ? 'Approved' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="actions">
                                        @if(!$review->is_approved)
                                            <form action="{{ route('ecommerce.admin.product-reviews.approve', $review->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm bg-success-light">
                                                    <i class="fe fe-check"></i> Accept
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('ecommerce.admin.product-reviews.destroy', $review->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this review?');">
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
                                <td colspan="7" class="text-center text-muted">No product reviews found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
