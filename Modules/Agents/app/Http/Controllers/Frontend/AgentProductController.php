<?php

namespace Modules\Agents\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class AgentProductController extends Controller
{
    public function index()
    {
        // Since we integrated agent checkouts directly into the Ecommerce ProductController,
        // we can simply redirect the agent to the main products shop.
        // Once they checkout, they will be redirected back to the Agent Dashboard.
        return redirect()->route('ecommerce.products');
    }
}
