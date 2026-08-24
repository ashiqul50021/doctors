<div class="seller-product-review-container">
    @if($product->status === 'approved' || ($product->is_approved && $product->is_active))
        <div class="alert alert-success d-flex align-items-center mb-3">
            <i class="fas fa-check-circle fs-4 me-2"></i>
            <div>
                <strong>Product Status: Approved / Live</strong>
                <p class="mb-0 small text-muted">This product is currently active and visible to buyers on the website.</p>
            </div>
        </div>
    @elseif($product->status === 'rejected' || (! $product->is_approved && ! empty($product->rejection_reason)))
        <div class="alert alert-danger mb-3">
            <div class="d-flex align-items-center mb-1">
                <i class="fas fa-times-circle fs-4 me-2"></i>
                <strong>Product Status: Rejected</strong>
            </div>
            <p class="mb-0 small"><strong>Rejection Reason:</strong> {{ $product->rejection_reason }}</p>
        </div>
    @else
        <div class="alert alert-warning d-flex align-items-center mb-3">
            <i class="fas fa-clock fs-4 me-2"></i>
            <div>
                <strong>Product Status: Pending Review</strong>
                <p class="mb-0 small text-muted">Review the product details below. Click Approve to publish live or Reject to return to seller with feedback.</p>
            </div>
        </div>
    @endif

    @include('ecommerce::backend.products.partials.details-modal-body', ['product' => $product])

    <!-- Review Actions Section -->
    <div class="border-top pt-3 mt-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <a href="{{ route('ecommerce.products.show', $product->id) }}" target="_blank" class="btn btn-outline-info">
                    <i class="fe fe-globe me-1"></i> Preview on Website
                </a>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-danger btn-modal-reject-toggle" data-id="{{ $product->id }}">
                    <i class="fe fe-x me-1"></i> Reject Product
                </button>
                <button type="button" class="btn btn-success text-white btn-modal-approve-action" data-id="{{ $product->id }}">
                    <i class="fe fe-check me-1"></i> Approve & Make Live
                </button>
            </div>
        </div>

        <!-- Hidden Rejection Form inside Modal -->
        <div class="rejection-form-wrapper mt-3 d-none" id="modalRejectionBox_{{ $product->id }}">
            <div class="card card-body bg-light border-danger">
                <h6 class="fw-bold text-danger mb-2"><i class="fas fa-exclamation-triangle me-1"></i> Provide Rejection Reason</h6>
                <form id="modalRejectForm_{{ $product->id }}" action="{{ route('ecommerce.admin.seller-products.reject', $product->id) }}" method="POST">
                    @csrf
                    <div class="form-group mb-2">
                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Please state clearly why this product is rejected (e.g. Invalid price, copyright image, missing specifications...)" required>{{ old('rejection_reason', $product->rejection_reason) }}</textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-secondary btn-cancel-rejection">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-paper-plane me-1"></i> Submit Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
