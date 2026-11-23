<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuyerProfile;
use App\Models\SupplierProfile;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = SupplierProfile::with('user')->latest()->get();

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,suspended',
        ]);

        $supplier = SupplierProfile::findOrFail($id);
        $supplier->status = $request->status;
        $supplier->save();

        return back()->with('success', 'Supplier status updated successfully.');
    }

    public function buyers()
    {
        $buyers = BuyerProfile::with('user')->latest()->get();

        return view('admin.buyers.index', compact('buyers'));
    }
}
