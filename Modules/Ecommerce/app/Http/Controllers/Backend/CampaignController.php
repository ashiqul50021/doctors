<?php

namespace Modules\Ecommerce\Http\Controllers\Backend;

use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Ecommerce\Models\Campaign;
use Modules\Ecommerce\Models\Product;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::withCount('products')->latest()->paginate(15);
        return view('ecommerce::backend.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return view('ecommerce::backend.campaigns.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'discount_type' => 'required|in:percentage,fixed,custom_price',
            'discount_value' => 'required|numeric|min:0',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'show_on_homepage' => 'boolean',
        ]);

        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        while (Campaign::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $bannerPath = null;
        if ($request->hasFile('banner_image')) {
            $bannerPath = ImageService::upload($request->file('banner_image'), 'campaigns');
        }

        $campaign = Campaign::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $request->description,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'banner_image' => $bannerPath,
            'is_active' => $request->boolean('is_active', true),
            'show_on_homepage' => $request->boolean('show_on_homepage', true),
        ]);

        return redirect()->route('ecommerce.admin.campaigns.products', $campaign->id)
            ->with('success', 'Campaign created successfully! Now assign products to this campaign.');
    }

    public function edit($id)
    {
        $campaign = Campaign::findOrFail($id);
        return view('ecommerce::backend.campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'discount_type' => 'required|in:percentage,fixed,custom_price',
            'discount_value' => 'required|numeric|min:0',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'description' => 'nullable|string',
        ]);

        $bannerPath = $campaign->banner_image;
        if ($request->hasFile('banner_image')) {
            if ($bannerPath) {
                ImageService::delete($bannerPath);
            }
            $bannerPath = ImageService::upload($request->file('banner_image'), 'campaigns');
        }

        $campaign->update([
            'title' => $validated['title'],
            'description' => $request->description,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'banner_image' => $bannerPath,
            'is_active' => $request->boolean('is_active'),
            'show_on_homepage' => $request->boolean('show_on_homepage'),
        ]);

        return redirect()->route('ecommerce.admin.campaigns.index')
            ->with('success', 'Campaign updated successfully.');
    }

    public function destroy($id)
    {
        $campaign = Campaign::findOrFail($id);
        if ($campaign->banner_image) {
            ImageService::delete($campaign->banner_image);
        }
        $campaign->delete();

        return redirect()->route('ecommerce.admin.campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    public function products($id)
    {
        $campaign = Campaign::with('products')->findOrFail($id);
        $assignedProductIds = $campaign->products->pluck('id')->toArray();
        $availableProducts = Product::where('is_active', true)->latest()->get();

        return view('ecommerce::backend.campaigns.products', compact('campaign', 'availableProducts', 'assignedProductIds'));
    }

    public function addProducts(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        foreach ($request->product_ids as $productId) {
            if (!$campaign->products()->where('product_id', $productId)->exists()) {
                $campaign->products()->attach($productId, [
                    'campaign_price' => null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Products added to campaign.');
    }

    public function removeProduct($campaignId, $productId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        $campaign->products()->detach($productId);

        return redirect()->back()->with('success', 'Product removed from campaign.');
    }

    public function updateProductPrice(Request $request, $campaignId, $productId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        $campaign->products()->updateExistingPivot($productId, [
            'campaign_price' => $request->filled('campaign_price') ? (float) $request->campaign_price : null,
        ]);

        return redirect()->back()->with('success', 'Campaign price updated for product.');
    }

    public function toggleStatus($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->update(['is_active' => !$campaign->is_active]);

        return redirect()->back()->with('success', 'Campaign status toggled.');
    }
}
