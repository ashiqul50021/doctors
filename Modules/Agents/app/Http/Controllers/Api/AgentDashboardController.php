<?php

namespace Modules\Agents\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Order;
use Modules\Courses\Models\Enrollment;
use Modules\Agents\Models\AgentTransaction;

class AgentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $agent = $request->user()->agent;
        
        $availableBalance = (float) $agent->wallet_balance;
        
        $totalEarned = (float) AgentTransaction::where('agent_id', $agent->id)
            ->whereIn('type', ['commission_booking', 'commission_product', 'commission_course'])
            ->sum('amount');
            
        $bookingsCount = Appointment::where('agent_id', $agent->id)->count();
        $salesCount = Order::where('agent_id', $agent->id)->count();
        $coursesCount = Enrollment::where('agent_id', $agent->id)->count();

        $recentBookings = Appointment::with('doctor.user', 'patient.user')
            ->where('agent_id', $agent->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'doctor_name' => $b->doctor && $b->doctor->user ? $b->doctor->user->name : 'Doctor',
                    'patient_name' => $b->patient_name ?? ($b->patient && $b->patient->user ? $b->patient->user->name : 'N/A'),
                    'booking_date' => $b->booking_date,
                    'time_slot' => $b->time_slot,
                    'fee' => (float) $b->fee,
                    'status' => $b->status,
                ];
            });

        $recentOrders = Order::where('agent_id', $agent->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($o) {
                return [
                    'id' => $o->id,
                    'order_number' => $o->order_number ?? '#' . $o->id,
                    'grand_total' => (float) $o->grand_total,
                    'status' => $o->status,
                    'created_at' => $o->created_at->toDateTimeString(),
                ];
            });

        $recentTransactions = AgentTransaction::where('agent_id', $agent->id)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'agent_id' => $agent->id,
                'referral_code' => $agent->referral_code,
                'available_balance' => $availableBalance,
                'total_earned' => $totalEarned,
                'bookings_count' => $bookingsCount,
                'sales_count' => $salesCount,
                'courses_count' => $coursesCount,
                'recent_bookings' => $recentBookings,
                'recent_orders' => $recentOrders,
                'recent_transactions' => $recentTransactions,
            ]
        ]);
    }

    public function wallet(Request $request)
    {
        $agent = $request->user()->agent;
        
        $transactions = AgentTransaction::where('agent_id', $agent->id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => [
                'wallet_balance' => (float) $agent->wallet_balance,
                'transactions' => $transactions,
            ]
        ]);
    }

    public function payoutRequest(Request $request)
    {
        $agent = $request->user()->agent;

        $request->validate([
            'amount' => ['required', 'numeric', 'min:500'],
            'payment_method' => ['required', 'string'],
            'account_number' => ['required', 'string'],
        ]);

        $amount = (float) $request->amount;

        if ($agent->wallet_balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have enough wallet balance to request this payout.',
            ], 422);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($agent, $amount, $request) {
            $agent->decrement('wallet_balance', $amount);

            AgentTransaction::create([
                'agent_id' => $agent->id,
                'type' => 'payout_request',
                'amount' => $amount,
                'description' => 'Payout request via ' . $request->payment_method . ' to ' . $request->account_number,
                'status' => 'pending',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Payout request submitted successfully. Please wait for admin approval.',
        ]);
    }
}
