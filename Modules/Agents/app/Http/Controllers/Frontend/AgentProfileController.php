<?php

namespace Modules\Agents\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;
use Modules\Agents\Models\Agent;

class AgentProfileController extends Controller
{
    public function show($slug)
    {
        $agent = Agent::with('user')->where('slug', $slug)->firstOrFail();

        // Automatically tag referral code when visiting agent's profile page
        if ($agent->status === 'active') {
            session(['ref_code' => $agent->referral_code]);
            Cookie::queue('ref_code', $agent->referral_code, 60 * 24 * 30); // 30 days
        }

        return view('agents::frontend.agent-profile', compact('agent'));
    }
}
