<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = $user->supplierProfile;

        return view('frontend.supplier.dashboard', compact('user', 'profile'));
    }
}
