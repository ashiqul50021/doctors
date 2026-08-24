@extends('layouts.admin')

@section('title', 'Products - abcsheba Admin')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Products</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Products</li>
            </ul>
        </div>
        <div class="col-auto">
            <a href="{{ route('ecommerce.admin.products.create') }}" class="btn btn-primary">
                <i class="fe fe-plus me-1"></i> Add Product
            </a>
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
                    <table class="table table-hover table-center mb-0 w-100" id="yajra-products-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Details Modal -->
<div class="modal fade" id="productDetailsModal" tabindex="-1" aria-labelledby="productDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="productDetailsModalLabel">Product Details Overview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="productDetailsModalContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#yajra-products-table')) {
            $('#yajra-products-table').DataTable().destroy();
        }

        const productsTable = $('#yajra-products-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('ecommerce.admin.products.index') }}",
            columns: [
                { data: 'code', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'category', name: 'category.name', defaultContent: 'N/A' },
                { data: 'price', name: 'price' },
                { data: 'stock', name: 'stock', orderable: false, searchable: false },
                { data: 'status', name: 'is_active', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
            ],
            order: [[0, 'desc']]
        });

        $(document).on('click', '.btn-product-details-modal', function(e) {
            e.preventDefault();
            const detailsUrl = $(this).attr('href');
            const modal = new bootstrap.Modal(document.getElementById('productDetailsModal'));
            $('#productDetailsModalContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            modal.show();

            $.ajax({
                url: detailsUrl,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.html) {
                        $('#productDetailsModalContent').html(response.html);
                    } else {
                        $('#productDetailsModalContent').html('<div class="alert alert-danger mb-0">Failed to load details.</div>');
                    }
                },
                error: function() {
                    $('#productDetailsModalContent').html('<div class="alert alert-danger mb-0">Error fetching product details.</div>');
                }
            });
        });

        $(document).on('change', '.status-toggle-btn', function() {
            const checkbox = $(this);
            const productId = checkbox.data('id');
            const isChecked = checkbox.is(':checked');

            $.ajax({
                url: "{{ route('ecommerce.admin.products.index') }}/" + productId + "/toggle-status",
                type: 'PATCH',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (!response.success) {
                        checkbox.prop('checked', !isChecked);
                    }
                },
                error: function() {
                    checkbox.prop('checked', !isChecked);
                    alert('Status toggle failed.');
                }
            });
        });
    });
</script>
@endpush
