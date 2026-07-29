<?php

namespace Modules\Ecommerce\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Ecommerce\Models\SellerPayout;
use Modules\Ecommerce\Models\SellerProfile;

class SellerPayoutController extends Controller
{
    public function index(Request $request)
    {
        $query = SellerPayout::with(['seller', 'sellerProfile']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payouts = $query->latest()->paginate(15);

        return view('ecommerce::backend.seller_payouts.index', compact('payouts'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $payout = SellerPayout::findOrFail($id);

        if ($payout->status !== 'pending') {
            return redirect()->back()->with('error', 'This payout request has already been processed.');
        }

        DB::transaction(function () use ($payout, $request) {
            if ($request->status === 'approved') {
                $payout->update([
                    'status' => 'approved',
                    'admin_note' => $request->admin_note,
                    'processed_at' => now(),
                ]);
            } else { // rejected
                $sellerProfile = SellerProfile::where('user_id', $payout->seller_id)->first();
                if ($sellerProfile) {
                    $sellerProfile->increment('wallet_balance', $payout->amount);
                }

                $payout->update([
                    'status' => 'rejected',
                    'admin_note' => $request->admin_note,
                    'processed_at' => now(),
                ]);
            }
        });

        $message = $request->status === 'approved' 
            ? 'Seller payout request approved successfully!' 
            : 'Seller payout request rejected and amount refunded to seller wallet.';

        return redirect()->back()->with('success', $message);
    }
}
