<?php

namespace Modules\Agents\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Order;
use Modules\Courses\Models\Enrollment;
use Modules\Agents\Models\AgentTransaction;

class AgentDashboardController extends Controller
{
    public function index()
    {
        $agent = Auth::user()->agent;
        
        $availableBalance = $agent->wallet_balance;
        
        $totalEarned = AgentTransaction::where('agent_id', $agent->id)
            ->whereIn('type', ['commission_booking', 'commission_product', 'commission_course'])
            ->sum('amount');
            
        $bookingsCount = Appointment::where('agent_id', $agent->id)->count();
        $salesCount = Order::where('agent_id', $agent->id)->count();
        $coursesCount = Enrollment::where('agent_id', $agent->id)->count();

        $recentBookings = Appointment::with('doctor.user', 'patient.user')
            ->where('agent_id', $agent->id)
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = Order::where('agent_id', $agent->id)
            ->latest()
            ->take(5)
            ->get();

        $recentTransactions = AgentTransaction::where('agent_id', $agent->id)
            ->latest()
            ->take(5)
            ->get();

        return view('agents::frontend.dashboard', compact(
            'agent',
            'availableBalance',
            'totalEarned',
            'bookingsCount',
            'salesCount',
            'coursesCount',
            'recentBookings',
            'recentOrders',
            'recentTransactions'
        ));
    }

    public function wallet()
    {
        $agent = Auth::user()->agent;
        $transactions = AgentTransaction::where('agent_id', $agent->id)
            ->latest()
            ->paginate(10);
            
        return view('agents::frontend.wallet', compact('agent', 'transactions'));
    }

    public function payoutRequest(Request $request)
    {
        $agent = Auth::user()->agent;

        $request->validate([
            'amount' => ['required', 'numeric', 'min:500'],
            'payment_method' => ['required', 'string'],
            'account_number' => ['required', 'string'],
        ]);

        $amount = $request->amount;

        if ($agent->wallet_balance < $amount) {
            return back()->with('error', 'You do not have enough wallet balance to request this payout.');
        }

        // Create transaction of type payout_request (pending)
        AgentTransaction::create([
            'agent_id' => $agent->id,
            'type' => 'payout_request',
            'amount' => $amount,
            'description' => 'Payout request via ' . $request->payment_method . ' to ' . $request->account_number,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Payout request submitted successfully. Please wait for admin approval.');
    }
}
