<?php

namespace Modules\Agents\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Modules\Agents\Models\Agent;
use Modules\Agents\Models\AgentTransaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminAgentController extends Controller
{
    public function index()
    {
        $agents = Agent::with('user')->latest()->get();
        return view('agents::backend.index', compact('agents'));
    }

    public function create()
    {
        return view('agents::backend.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:15'],
            'password' => ['required', 'string', 'min:8'],
            'booking_commission_rate' => ['required', 'numeric', 'min:0'],
            'product_commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'course_commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'in:pending,active,suspended'],
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'agent',
            ]);

            // Generate unique referral code
            do {
                $referralCode = 'AGT-' . strtoupper(Str::random(6));
            } while (Agent::where('referral_code', $referralCode)->exists());

            Agent::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'referral_code' => $referralCode,
                'can_book_appointments' => $request->has('can_book_appointments'),
                'can_sell_products' => $request->has('can_sell_products'),
                'can_sell_courses' => $request->has('can_sell_courses'),
                'booking_commission_rate' => $request->booking_commission_rate,
                'product_commission_rate' => $request->product_commission_rate,
                'course_commission_rate' => $request->course_commission_rate,
                'wallet_balance' => 0.00,
                'status' => $request->status,
            ]);

            DB::commit();
            return redirect()->route('admin.agents.index')->with('success', 'Agent created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create agent: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $agent = Agent::with('user')->findOrFail($id);
        return view('agents::backend.edit', compact('agent'));
    }

    public function update(Request $request, $id)
    {
        $agent = Agent::findOrFail($id);
        $user = $agent->user;

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'max:15'],
            'password' => ['nullable', 'string', 'min:8'],
            'booking_commission_rate' => ['required', 'numeric', 'min:0'],
            'product_commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'course_commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'in:pending,active,suspended'],
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $agent->update([
                'phone' => $request->phone,
                'can_book_appointments' => $request->has('can_book_appointments'),
                'can_sell_products' => $request->has('can_sell_products'),
                'can_sell_courses' => $request->has('can_sell_courses'),
                'booking_commission_rate' => $request->booking_commission_rate,
                'product_commission_rate' => $request->product_commission_rate,
                'course_commission_rate' => $request->course_commission_rate,
                'status' => $request->status,
            ]);

            DB::commit();
            return redirect()->route('admin.agents.index')->with('success', 'Agent updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update agent: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $agent = Agent::findOrFail($id);
        $user = $agent->user;

        DB::beginTransaction();
        try {
            $agent->delete();
            if ($user) {
                $user->delete();
            }
            DB::commit();
            return redirect()->route('admin.agents.index')->with('success', 'Agent deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.agents.index')->with('error', 'Failed to delete agent: ' . $e->getMessage());
        }
    }

    public function payoutsIndex()
    {
        $payouts = AgentTransaction::with('agent.user')
            ->where('type', 'payout_request')
            ->latest()
            ->get();
        return view('agents::backend.payouts', compact('payouts'));
    }

    public function payoutsApprove(Request $request, $id)
    {
        $payoutRequest = AgentTransaction::findOrFail($id);
        $agent = $payoutRequest->agent;

        if ($payoutRequest->status !== 'pending') {
            return back()->with('error', 'This payout request has already been processed.');
        }

        if ($agent->wallet_balance < $payoutRequest->amount) {
            return back()->with('error', 'Agent has insufficient wallet balance.');
        }

        DB::beginTransaction();
        try {
            // Deduct from agent wallet
            $agent->decrement('wallet_balance', $payoutRequest->amount);

            // Update original request to completed
            $payoutRequest->update(['status' => 'completed']);

            // Create log for payout approval
            AgentTransaction::create([
                'agent_id' => $agent->id,
                'type' => 'payout_approved',
                'amount' => $payoutRequest->amount,
                'description' => 'Payout of ৳' . number_format($payoutRequest->amount, 2) . ' approved by Admin.',
                'reference_id' => $payoutRequest->id,
                'status' => 'completed',
            ]);

            DB::commit();
            return back()->with('success', 'Payout request approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve payout: ' . $e->getMessage());
        }
    }

    public function payoutsReject(Request $request, $id)
    {
        $payoutRequest = AgentTransaction::findOrFail($id);

        if ($payoutRequest->status !== 'pending') {
            return back()->with('error', 'This payout request has already been processed.');
        }

        DB::beginTransaction();
        try {
            $payoutRequest->update(['status' => 'rejected']);

            // Log rejection
            AgentTransaction::create([
                'agent_id' => $payoutRequest->agent_id,
                'type' => 'payout_rejected',
                'amount' => $payoutRequest->amount,
                'description' => 'Payout request rejected by Admin.',
                'reference_id' => $payoutRequest->id,
                'status' => 'rejected',
            ]);

            DB::commit();
            return back()->with('success', 'Payout request rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject payout: ' . $e->getMessage());
        }
    }
}
