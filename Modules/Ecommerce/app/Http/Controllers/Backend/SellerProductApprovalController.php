<?php

namespace Modules\Ecommerce\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Ecommerce\Models\Product;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class SellerProductApprovalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::whereNotNull('seller_id')
                ->with(['seller', 'category', 'variants'])
                ->select('products.*');

            if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
                if ($request->status === 'approved') {
                    $query->where('is_approved', true)->where('is_active', true)->where('status', 'approved');
                } elseif ($request->status === 'rejected') {
                    $query->where(function($q) {
                        $q->where('status', 'rejected')->orWhereNotNull('rejection_reason');
                    });
                } elseif ($request->status === 'pending') {
                    $query->where(function($q) {
                        $q->where('is_approved', false)
                          ->where(function($subQ) {
                              $subQ->whereNull('rejection_reason')->where('status', '!=', 'rejected');
                          });
                    });
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('code', function ($row) {
                    return '<span class="product-code-badge">#PRO' . $row->id . '</span>';
                })
                ->editColumn('name', function ($row) {
                    $image = $row->image;
                    if (!$image && !empty($row->gallery) && is_array($row->gallery)) {
                        $image = $row->gallery[0] ?? null;
                    }
                    $imgUrl = $image ? (Str::startsWith($image, ['http://', 'https://']) ? $image : asset($image)) : null;
                    $viewUrl = route('ecommerce.products.show', $row->id);

                    $avatarHtml = $imgUrl 
                        ? '<a href="' . $viewUrl . '" target="_blank" class="avatar avatar-sm me-2"><img class="avatar-img" src="' . $imgUrl . '" alt="Product"></a>'
                        : '<span class="avatar avatar-sm me-2 d-inline-flex align-items-center justify-content-center bg-light text-muted border" style="font-size: 9px; font-weight: 600;">No Image</span>';

                    return '<div class="table-avatar">' . $avatarHtml . '<div><a href="' . $viewUrl . '" target="_blank" class="fw-semibold text-dark">' . e($row->name) . '</a></div></div>';
                })
                ->addColumn('seller_name', function ($row) {
                    return '<span class="badge bg-info-light text-info fw-bold">' . e($row->seller->name ?? 'Seller #' . $row->seller_id) . '</span>';
                })
                ->editColumn('category', function ($row) {
                    return '<span class="text-secondary fw-semibold">' . e($row->category->name ?? 'N/A') . '</span>';
                })
                ->editColumn('price', function ($row) {
                    return '<span class="price-text">৳' . number_format($row->price, 2) . '</span>';
                })
                ->editColumn('status', function ($row) {
                    $isApprovedAndLive = $row->is_approved && $row->is_active && $row->status === 'approved';
                    $isRejected = $row->status === 'rejected' || (!empty($row->rejection_reason));

                    if ($isApprovedAndLive) {
                        return '<span class="badge bg-success-light text-success px-2 py-1"><i class="fas fa-check-circle me-1"></i> Live / Approved</span>';
                    } elseif ($isRejected) {
                        return '<div class="d-flex flex-column">
                            <span class="badge bg-danger-light text-danger px-2 py-1" style="width: fit-content;"><i class="fas fa-times-circle me-1"></i> Rejected</span>
                            <small class="text-muted mt-1" style="font-size: 11px;" title="' . e($row->rejection_reason) . '">Reason: ' . e(Str::limit($row->rejection_reason, 25)) . '</small>
                        </div>';
                    } else {
                        return '<span class="badge bg-warning-light text-warning px-2 py-1"><i class="fas fa-clock me-1"></i> Pending Review</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $siteUrl = route('ecommerce.products.show', $row->id);
                    $detailsUrl = route('ecommerce.admin.seller-products.show', $row->id);

                    $isApprovedAndLive = $row->is_approved && $row->is_active && $row->status === 'approved';
                    $isRejected = $row->status === 'rejected' || (!empty($row->rejection_reason));

                    $buttons = '<div class="actions text-end gap-1 d-inline-flex align-items-center">
                        <a class="btn btn-sm btn-outline-info me-1" href="' . $siteUrl . '" target="_blank" title="Site View"><i class="fe fe-globe"></i> Site View</a>
                        <a class="btn btn-sm btn-primary me-1 btn-review-modal" href="' . $detailsUrl . '" data-id="' . $row->id . '" title="Review & Action"><i class="fe fe-eye"></i> Review</a>';

                    if (! $isApprovedAndLive) {
                        $buttons .= '<button class="btn btn-sm btn-success me-1 btn-approve-direct" data-id="' . $row->id . '" title="Approve & Live"><i class="fe fe-check"></i> Approve</button>';
                    }

                    if (! $isRejected) {
                        $buttons .= '<button class="btn btn-sm btn-danger btn-reject-modal" data-id="' . $row->id . '" data-name="' . e($row->name) . '" title="Reject Product"><i class="fe fe-x"></i> Reject</button>';
                    }

                    $buttons .= '</div>';
                    return $buttons;
                })
                ->rawColumns(['code', 'name', 'seller_name', 'category', 'price', 'status', 'action'])
                ->make(true);
        }

        $pendingCount = Product::whereNotNull('seller_id')
            ->where(function($q) {
                $q->where('is_approved', false)
                  ->where(function($subQ) {
                      $subQ->whereNull('rejection_reason')->where('status', '!=', 'rejected');
                  });
            })->count();

        $approvedCount = Product::whereNotNull('seller_id')
            ->where('is_approved', true)
            ->where('is_active', true)
            ->where('status', 'approved')
            ->count();

        $rejectedCount = Product::whereNotNull('seller_id')
            ->where(function($q) {
                $q->where('status', 'rejected')->orWhereNotNull('rejection_reason');
            })->count();

        return view('ecommerce::backend.seller_products.index', compact('pendingCount', 'approvedCount', 'rejectedCount'));
    }

    public function show(Request $request, $id)
    {
        $product = Product::whereNotNull('seller_id')
            ->with(['category', 'variants', 'seller', 'approvedProductReviews'])
            ->findOrFail($id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('ecommerce::backend.seller_products.partials.review-modal-body', compact('product'))->render()
            ]);
        }

        return view('ecommerce::backend.seller_products.show', compact('product'));
    }

    public function approve(Request $request, $id)
    {
        $product = Product::whereNotNull('seller_id')->findOrFail($id);
        
        $product->update([
            'is_approved' => true,
            'is_active' => true,
            'status' => 'approved',
            'rejection_reason' => null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Seller product approved and published live successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Seller product approved and published live successfully!');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ], [
            'rejection_reason.required' => 'Please state a reason for rejecting this seller product.'
        ]);

        $product = Product::whereNotNull('seller_id')->findOrFail($id);

        $product->update([
            'is_approved' => false,
            'is_active' => false,
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Seller product rejected successfully with reason recorded.'
            ]);
        }

        return redirect()->back()->with('success', 'Seller product rejected successfully.');
    }
}
