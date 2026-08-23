<?php

namespace Modules\Ecommerce\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Ecommerce\Models\SellerProfile;

class ProfileController extends Controller
{
    public function edit()
    {
        $seller = auth()->user()->sellerProfile;
        
        if (!$seller) {
            abort(404, 'Seller profile not found.');
        }

        return view('ecommerce::seller.profile.edit', compact('seller'));
    }

    public function update(Request $request)
    {
        $seller = auth()->user()->sellerProfile;

        if (!$seller) {
            abort(404, 'Seller profile not found.');
        }

        $request->validate([
            'store_name' => 'required|string|max:255|unique:seller_profiles,store_name,' . $seller->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'store_logo' => 'nullable|image|max:20480',
            'store_banner' => 'nullable|image|max:20480',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
        ]);

        $data = [
            'store_name' => $request->store_name,
            'store_slug' => Str::slug($request->store_name),
            'phone' => $request->phone,
            'address' => $request->address,
            'bank_name' => $request->bank_name,
            'bank_account_name' => $request->bank_account_name,
            'bank_account_number' => $request->bank_account_number,
        ];

        // Handle Store Logo Upload
        if ($request->hasFile('store_logo')) {
            if ($seller->store_logo) {
                \App\Services\ImageService::delete($seller->store_logo);
            }
            $data['store_logo'] = \App\Services\ImageService::upload($request->file('store_logo'), 'sellers', 85, 400);
        }

        // Handle Store Banner Upload
        if ($request->hasFile('store_banner')) {
            if ($seller->store_banner) {
                \App\Services\ImageService::delete($seller->store_banner);
            }
            $data['store_banner'] = \App\Services\ImageService::upload($request->file('store_banner'), 'sellers', 85, 1200);
        }

        $seller->update($data);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
