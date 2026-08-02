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
            'store_logo' => 'nullable|image|max:2048',
            'store_banner' => 'nullable|image|max:4096',
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
            // Delete old logo if exists
            if ($seller->store_logo && file_exists(public_path($seller->store_logo))) {
                @unlink(public_path($seller->store_logo));
            }
            $logo = $request->file('store_logo');
            $filename = 'logo_' . time() . '_' . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('uploads/sellers'), $filename);
            $data['store_logo'] = 'uploads/sellers/' . $filename;
        }

        // Handle Store Banner Upload
        if ($request->hasFile('store_banner')) {
            // Delete old banner if exists
            if ($seller->store_banner && file_exists(public_path($seller->store_banner))) {
                @unlink(public_path($seller->store_banner));
            }
            $banner = $request->file('store_banner');
            $filename = 'banner_' . time() . '_' . uniqid() . '.' . $banner->getClientOriginalExtension();
            $banner->move(public_path('uploads/sellers'), $filename);
            $data['store_banner'] = 'uploads/sellers/' . $filename;
        }

        $seller->update($data);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
