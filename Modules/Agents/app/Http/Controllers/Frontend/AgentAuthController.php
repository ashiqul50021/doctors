<?php

namespace Modules\Agents\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Modules\Agents\Models\Agent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class AgentAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'agent') {
                return redirect()->route('agent.dashboard');
            }
            return redirect('/');
        }
        return view('agents::frontend.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role !== 'agent') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'This account is not registered as an agent.',
                ])->onlyInput('email');
            }

            $agent = $user->agent;
            if (!$agent) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Agent profile not found for this account.',
                ]);
            }

            if ($agent->status === 'pending') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Your agent account is pending admin approval.',
                ]);
            }

            if ($agent->status === 'suspended') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Your agent account has been suspended.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->route('agent.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('agents::frontend.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:15'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'agent',
        ]);

        // Generate a unique referral code
        do {
            $referralCode = 'AGT-' . strtoupper(Str::random(6));
        } while (Agent::where('referral_code', $referralCode)->exists());

        // Create Agent profile (status pending by default)
        Agent::create([
            'user_id' => $user->id,
            'phone' => $request->mobile,
            'referral_code' => $referralCode,
            'can_book_appointments' => true,
            'can_sell_products' => true,
            'can_sell_courses' => true,
            'booking_commission_rate' => 50.00,  // default flat ৳50 per booking
            'product_commission_rate' => 5.00,   // default 5% per product sale
            'course_commission_rate' => 10.00,   // default 10% per course sale
            'wallet_balance' => 0.00,
            'status' => 'pending', // Pending admin approval
        ]);

        return redirect()->route('agent.login')->with('success', 'Your registration was successful! Please wait for admin approval.');
    }
}
