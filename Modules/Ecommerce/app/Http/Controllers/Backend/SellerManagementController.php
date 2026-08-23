<?php

namespace Modules\Ecommerce\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Ecommerce\Models\SellerProfile;

class SellerManagementController extends Controller
{
    public function index()
    {
        $sellers = SellerProfile::with('user')->withCount('products')->latest()->paginate(15);
        return view('ecommerce::backend.sellers.index', compact('sellers'));
    }

    public function create()
    {
        return view('ecommerce::backend.sellers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'store_name' => 'required|string|max:255|unique:seller_profiles,store_name',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:pending,approved,suspended',
            'store_logo' => 'nullable|image|max:20480',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Create the user
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'role' => 'seller',
                ]);

                // 2. Handle store logo upload
                $logoPath = null;
                if ($request->hasFile('store_logo')) {
                    $logoPath = \App\Services\ImageService::upload($request->file('store_logo'), 'sellers', 85, 400);
                }

                // 3. Create the seller profile
                $user->sellerProfile()->create([
                    'store_name' => $request->store_name,
                    'store_slug' => Str::slug($request->store_name),
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'commission_rate' => $request->commission_rate,
                    'status' => $request->status,
                    'store_logo' => $logoPath,
                    'wallet_balance' => 0.00,
                ]);
            });

            return redirect()->route('ecommerce.admin.sellers.index')->with('success', 'Seller created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to create seller: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $seller = SellerProfile::with('user')->findOrFail($id);
        return view('ecommerce::backend.sellers.edit', compact('seller'));
    }

    public function update(Request $request, $id)
    {
        $seller = SellerProfile::with('user')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $seller->user_id,
            'password' => 'nullable|string|min:6',
            'store_name' => 'required|string|max:255|unique:seller_profiles,store_name,' . $seller->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:pending,approved,suspended',
            'store_logo' => 'nullable|image|max:20480',
        ]);

        try {
            DB::transaction(function () use ($request, $seller) {
                // 1. Update user
                $userData = [
                    'name' => $request->name,
                    'email' => $request->email,
                ];
                if ($request->filled('password')) {
                    $userData['password'] = bcrypt($request->password);
                }
                $seller->user->update($userData);

                // 2. Handle store logo upload
                $sellerData = [
                    'store_name' => $request->store_name,
                    'store_slug' => Str::slug($request->store_name),
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'commission_rate' => $request->commission_rate,
                    'status' => $request->status,
                ];

                if ($request->hasFile('store_logo')) {
                    if ($seller->store_logo) {
                        \App\Services\ImageService::delete($seller->store_logo);
                    }
                    $sellerData['store_logo'] = \App\Services\ImageService::upload($request->file('store_logo'), 'sellers', 85, 400);
                }

                // 3. Update seller profile
                $seller->update($sellerData);
            });

            return redirect()->route('ecommerce.admin.sellers.index')->with('success', 'Seller updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update seller: ' . $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,suspended',
        ]);

        $seller = SellerProfile::findOrFail($id);
        $seller->status = $request->status;
        $seller->save();

        return redirect()->back()->with('success', 'Seller status updated successfully.');
    }
}

