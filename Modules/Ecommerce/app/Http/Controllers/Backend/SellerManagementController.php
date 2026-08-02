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
            'store_logo' => 'nullable|image|max:2048',
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
                    $logo = $request->file('store_logo');
                    $filename = time() . '_' . uniqid() . '.' . $logo->getClientOriginalExtension();
                    $logo->move(public_path('uploads/sellers'), $filename);
                    $logoPath = 'uploads/sellers/' . $filename;
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

    public function show($id)
    {
        $seller = SellerProfile::with(['user', 'products'])->findOrFail($id);
        return view('ecommerce::backend.sellers.show', compact('seller'));
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

