<?php

namespace Modules\Ecommerce\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Ecommerce\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $sellerId = auth()->id();

        // Get orders that contain at least one product belonging to this seller
        $orders = Order::whereHas('items', function ($query) use ($sellerId) {
                $query->where('seller_id', $sellerId);
            })
            ->with(['patient.user'])
            ->withCount(['items' => function ($query) use ($sellerId) {
                $query->where('seller_id', $sellerId);
            }])
            ->latest()
            ->paginate(10);

        return view('ecommerce::seller.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $sellerId = auth()->id();

        // Load the order only if it belongs to this seller, and only load their items
        $order = Order::whereHas('items', function ($query) use ($sellerId) {
                $query->where('seller_id', $sellerId);
            })
            ->with([
                'patient.user',
                'items' => function ($query) use ($sellerId) {
                    $query->where('seller_id', $sellerId)->with(['product', 'variant']);
                }
            ])
            ->findOrFail($id);

        // Calculate seller specific totals for this order
        $sellerSubtotal = 0;
        foreach ($order->items as $item) {
            $sellerSubtotal += $item->total;
        }

        $sellerProfile = auth()->user()->sellerProfile;
        $commissionRate = (float) ($sellerProfile->commission_rate ?? 0);
        $commissionAmount = $sellerSubtotal * ($commissionRate / 100);
        $netEarnings = max(0, $sellerSubtotal - $commissionAmount);

        return view('ecommerce::seller.orders.show', compact('order', 'sellerSubtotal', 'commissionRate', 'commissionAmount', 'netEarnings'));
    }
}
