<?php

namespace Modules\Agents\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Modules\Agents\Models\Agent;

class ReferralTracker
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('ref')) {
            $refCode = $request->query('ref');
            
            // Validate the agent is active
            $agent = Agent::where('referral_code', $refCode)->where('status', 'active')->first();
            
            if ($agent) {
                // Store in session
                session(['ref_code' => $refCode]);
                
                // Store in long-lived cookie (30 days)
                Cookie::queue('ref_code', $refCode, 60 * 24 * 30);
            }
        }

        return $next($request);
    }
}
