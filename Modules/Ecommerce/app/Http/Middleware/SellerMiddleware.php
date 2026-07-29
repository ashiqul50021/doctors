<?php

namespace Modules\Ecommerce\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SellerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->role !== 'seller') {
            abort(403, 'Unauthorized access. Seller account required.');
        }

        if (!$user->sellerProfile || $user->sellerProfile->status !== 'approved') {
            abort(403, 'Your seller account is pending approval or suspended.');
        }

        return $next($request);
    }
}
