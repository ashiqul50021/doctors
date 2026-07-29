<?php

namespace Modules\Ecommerce\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Ecommerce\Models\OrderItem;
use Modules\Ecommerce\Models\SellerPayout;
use Modules\Ecommerce\Models\SellerProfile;

class PayoutController extends Controller
{
    public function index()
    {
        $sellerId = auth()->id();
        $sellerProfile = SellerProfile::where('user_id', $sellerId)->firstOrFail();

        $walletBalance = (float) $sellerProfile->wallet_balance;

        // Pending earnings from orders that are not yet delivered
        $pendingItems = OrderItem::where('seller_id', $sellerId)
            ->whereHas('order', function ($query) {
                $query->whereIn('status', ['pending', 'processing', 'shipped']);
            })
            ->get();

        $commissionRate = (float) ($sellerProfile->commission_rate ?? 0);
        $pendingEarnings = 0;
        foreach ($pendingItems as $item) {
            $gross = $item->price * $item->quantity;
            $pendingEarnings += ($gross - ($gross * ($commissionRate / 100)));
        }

        $totalWithdrawn = SellerPayout::where('seller_id', $sellerId)
            ->where('status', 'approved')
            ->sum('amount');

        $payouts = SellerPayout::where('seller_id', $sellerId)
            ->latest()
            ->paginate(15);

        return view('ecommerce::seller.payouts.index', compact(
            'sellerProfile',
            'walletBalance',
            'pendingEarnings',
            'totalWithdrawn',
            'payouts'
        ));
    }

    public function store(Request $request)
    {
        $sellerId = auth()->id();
        $sellerProfile = SellerProfile::where('user_id', $sellerId)->firstOrFail();

        $request->validate([
            'amount' => 'required|numeric|min:500|max:' . $sellerProfile->wallet_balance,
            'payment_method' => 'required|string|in:bank,bkash,nagad,rocket',
            'account_details' => 'required|string|max:1000',
        ], [
            'amount.min' => 'Minimum withdrawal amount is ৳500.',
            'amount.max' => 'Withdrawal amount cannot exceed your available wallet balance (৳' . number_format($sellerProfile->wallet_balance, 2) . ').',
        ]);

        DB::transaction(function () use ($sellerId, $sellerProfile, $request) {
            $sellerProfile->decrement('wallet_balance', $request->amount);

            SellerPayout::create([
                'seller_id' => $sellerId,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'account_details' => $request->account_details,
                'status' => 'pending',
            ]);
        });

        return redirect()->back()->with('success', 'Withdrawal request of ৳' . number_format($request->amount, 2) . ' submitted successfully!');
    }
}
