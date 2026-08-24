@extends('layouts.admin')

@section('title', 'Seller Products Approval - Admin')

@section('content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Seller Products Approval</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.admin.products.index') }}">Ecommerce</a></li>
                <li class="breadcrumb-item active">Seller Products</li>
            </ul>
        </div>
    </div>
</div>

<!-- Stats Counter Widgets -->
<div class="row mb-4">
    <div class="col-xl-4 col-sm-6 col-12">
        <div class="card bg-warning-light border-0 shadow-sm">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon text-warning border-warning">
                        <i class="fe fe-clock"></i>
                    </span>
                    <div class="dash-count">
                        <h3 class="text-warning mb-0" id="stat-pending-count">{{ $pendingCount }}</h3>
                        <span class="text-muted small">Pending Approval</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-6 col-12">
        <div class="card bg-success-light border-0 shadow-sm">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon text-success border-success">
                        <i class="fe fe-check-circle"></i>
                    </span>
                    <div class="dash-count">
                        <h3 class="text-success mb-0" id="stat-approved-count">{{ $approvedCount }}</h3>
                        <span class="text-muted small">Approved & Live</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-6 col-12">
        <div class="card bg-danger-light border-0 shadow-sm">
            <div class="card-body">
                <div class="dash-widget-header">
                    <span class="dash-widget-icon text-danger border-danger">
                        <i class="fe fe-x-circle"></i>
                    </span>
                    <div class="dash-count">
                        <h3 class="text-danger mb-0" id="stat-rejected-count">{{ $rejectedCount }}</h3>
                        <span class="text-muted small">Rejected Products</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <!-- Filter Tabs -->
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                    <ul class="nav nav-pills" id="sellerProductStatusFilter">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" data-status="all">All Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-status="pending">
                                Pending Review <span class="badge bg-warning text-dark ms-1">{{ $pendingCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-status="approved">Approved / Live</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-status="rejected">Rejected</a>
                        </li>
                    </ul>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-center mb-0 w-100" id="seller-products-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Seller Store</th>
                                <th>Category</th>
                                <th>Price</th>
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

<!-- Review & Details Modal -->
<div class="modal fade" id="reviewProductModal" tabindex="-1" aria-labelledby="reviewProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="reviewProductModalLabel">Seller Product Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="reviewProductModalContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div class="modal fade" id="rejectionReasonModal" tabindex="-1" aria-labelledby="rejectionReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="directRejectionForm" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold text-white" id="rejectionReasonModalLabel"><i class="fas fa-exclamation-triangle me-1"></i> Reject Seller Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-2">Please state the reason for rejecting product <strong id="rejectProductName"></strong>. The seller will see this reason in their panel.</p>
                    <div class="form-group mb-0">
                        <label class="fw-bold">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" id="rejectionReasonTextarea" class="form-control" rows="4" placeholder="Enter reason for rejection (e.g. Quality check failed, price discrepancy, misleading title...)" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-paper-plane me-1"></i> Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let currentStatusFilter = 'all';

        const sellerProductsTable = $('#seller-products-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('ecommerce.admin.seller-products.index') }}",
                data: function(d) {
                    d.status = currentStatusFilter;
                }
            },
            columns: [
                { data: 'code', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'seller_name', name: 'seller.name' },
                { data: 'category', name: 'category.name', defaultContent: 'N/A' },
                { data: 'price', name: 'price' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
            ],
            order: [[0, 'desc']]
        });

        // Filter tab click
        $('#sellerProductStatusFilter a').on('click', function(e) {
            e.preventDefault();
            $('#sellerProductStatusFilter a').removeClass('active');
            $(this).addClass('active');
            currentStatusFilter = $(this).data('status');
            sellerProductsTable.ajax.reload();
        });

        // Review Modal Click
        $(document).on('click', '.btn-review-modal', function(e) {
            e.preventDefault();
            const detailsUrl = $(this).attr('href');
            const modal = new bootstrap.Modal(document.getElementById('reviewProductModal'));
            $('#reviewProductModalContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            modal.show();

            $.ajax({
                url: detailsUrl,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.html) {
                        $('#reviewProductModalContent').html(response.html);
                    } else {
                        $('#reviewProductModalContent').html('<div class="alert alert-danger mb-0">Failed to load review details.</div>');
                    }
                },
                error: function() {
                    $('#reviewProductModalContent').html('<div class="alert alert-danger mb-0">Error fetching product details.</div>');
                }
            });
        });

        // Toggle rejection box in modal
        $(document).on('click', '.btn-modal-reject-toggle', function() {
            const productId = $(this).data('id');
            $(`#modalRejectionBox_${productId}`).toggleClass('d-none');
        });

        $(document).on('click', '.btn-cancel-rejection', function() {
            $(this).closest('.rejection-form-wrapper').addClass('d-none');
        });

        // Approve Direct Button click
        $(document).on('click', '.btn-approve-direct, .btn-modal-approve-action', function(e) {
            e.preventDefault();
            const productId = $(this).data('id');
            if (!confirm('Are you sure you want to approve this seller product and publish it live?')) return;

            $.ajax({
                url: `{{ url('admin/seller-products') }}/${productId}/approve`,
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        $('#reviewProductModal').modal('hide');
                        sellerProductsTable.ajax.reload();
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Approval failed. Please try again.');
                }
            });
        });

        // Reject Direct Button click (Opens Rejection Modal)
        $(document).on('click', '.btn-reject-modal', function(e) {
            e.preventDefault();
            const productId = $(this).data('id');
            const productName = $(this).data('name');
            $('#rejectProductName').text(productName);
            $('#directRejectionForm').attr('action', `{{ url('admin/seller-products') }}/${productId}/reject`);
            $('#rejectionReasonTextarea').val('');
            const modal = new bootstrap.Modal(document.getElementById('rejectionReasonModal'));
            modal.show();
        });

        // Submit Modal Rejection Form AJAX
        $(document).on('submit', '[id^=modalRejectForm_]', function(e) {
            e.preventDefault();
            const form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#reviewProductModal').modal('hide');
                        sellerProductsTable.ajax.reload();
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('Rejection failed: ' + (xhr.responseJSON?.message || 'Please provide a valid reason.'));
                }
            });
        });
    });
</script>
@endpush
