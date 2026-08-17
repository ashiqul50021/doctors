<?php

namespace Modules\Ecommerce\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Ecommerce\Models\Campaign;
use Modules\Ecommerce\Models\ProductCategory;

class CampaignController extends Controller
{
    public function index()
    {
        $activeCampaigns = Campaign::active()->withCount('products')->latest()->get();
        $upcomingCampaigns = Campaign::where('is_active', true)
            ->where('start_date', '>', now())
            ->latest()
            ->get();

        return view('ecommerce::frontend.campaigns.index', compact('activeCampaigns', 'upcomingCampaigns'));
    }

    public function show(Request $request, string $slug)
    {
        $campaign = Campaign::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $query = $campaign->products()
            ->with(['category', 'variants', 'approvedProductReviews'])
            ->withCount('approvedProductReviews as reviews_count')
            ->withAvg('approvedProductReviews as rating', 'rating')
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

        // Search within campaign products
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

        $campaignCategories = ProductCategory::whereIn('id', $campaign->products->pluck('product_category_id')->unique())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('ecommerce::frontend.campaigns.show', compact('campaign', 'products', 'campaignCategories'));
    }
}
