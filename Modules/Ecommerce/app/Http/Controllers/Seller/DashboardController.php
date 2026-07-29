<?php

namespace Modules\Ecommerce\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Ecommerce\Models\Order;
use Modules\Ecommerce\Models\OrderItem;
use Modules\Ecommerce\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $sellerId = auth()->id();

        $totalProducts = Product::where('seller_id', $sellerId)->count();
        $totalItemsSold = OrderItem::where('seller_id', $sellerId)->sum('quantity');
        $totalEarnings = OrderItem::where('seller_id', $sellerId)->sum('total_price');

        $recentItems = OrderItem::with(['order', 'product', 'variant'])
            ->where('seller_id', $sellerId)
            ->latest()
            ->take(10)
            ->get();

        return view('ecommerce::seller.dashboard', compact(
            'totalProducts',
            'totalItemsSold',
            'totalEarnings',
            'recentItems'
        ));
    }
}
