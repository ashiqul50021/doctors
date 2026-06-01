<?php

namespace Modules\Agents\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class AgentCourseController extends Controller
{
    public function index()
    {
        // Redirect agents to the main courses list.
        // Once they share their referral link, anyone signing up will earn them commission.
        return redirect()->route('courses.index');
    }
}
