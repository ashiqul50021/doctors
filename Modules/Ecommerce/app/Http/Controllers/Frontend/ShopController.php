<?php

namespace Modules\Ecommerce\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Models\ProductCategory;
use Modules\Ecommerce\Models\SellerProfile;

class ShopController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $sellerProfile = SellerProfile::where('store_slug', $slug)
            ->where('status', 'approved')
            ->firstOrFail();

        $query = Product::with(['category', 'variants', 'approvedProductReviews'])
            ->withCount('approvedProductReviews as reviews_count')
            ->withAvg('approvedProductReviews as rating', 'rating')
            ->where('seller_id', $sellerProfile->user_id)
            ->where('is_active', true);

        // Filter by category
        if ($request->filled('category')) {
            $categoryId = (int) $request->category;
            $childIds = ProductCategory::where('parent_id', $categoryId)->pluck('id')->toArray();
            $grandchildIds = [];
            if (!empty($childIds)) {
                $grandchildIds = ProductCategory::whereIn('parent_id', $childIds)->pluck('id')->toArray();
            }
            $categoryIds = array_merge([$categoryId], $childIds, $grandchildIds);
            $query->whereIn('product_category_id', $categoryIds);
        }

        // Search in seller's products
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'popular' => $query->orderBy('is_featured', 'desc')->latest(),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        // Get seller's categories
        $sellerCategoryIds = Product::where('seller_id', $sellerProfile->user_id)
            ->where('is_active', true)
            ->pluck('product_category_id')
            ->unique();

        $categories = ProductCategory::whereIn('id', $sellerCategoryIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Calculate store stats
        $totalProducts = Product::where('seller_id', $sellerProfile->user_id)->where('is_active', true)->count();
        $totalSold = \Modules\Ecommerce\Models\OrderItem::where('seller_id', $sellerProfile->user_id)
            ->whereHas('order', function ($q) {
                $q->whereIn('status', ['completed', 'delivered', 'processing', 'pending']);
            })->sum('quantity');

        return view('ecommerce::frontend.shop.show', compact(
            'sellerProfile',
            'products',
            'categories',
            'totalProducts',
            'totalSold'
        ));
    }
}
