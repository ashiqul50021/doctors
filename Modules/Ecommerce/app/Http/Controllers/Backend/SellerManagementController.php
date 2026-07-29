<?php

namespace Modules\Ecommerce\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Ecommerce\Models\SellerProfile;

class SellerManagementController extends Controller
{
    public function index()
    {
        $sellers = SellerProfile::with('user')->withCount('products')->latest()->paginate(15);
        return view('ecommerce::backend.sellers.index', compact('sellers'));
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
