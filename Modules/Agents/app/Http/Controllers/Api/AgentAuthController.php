<?php

namespace Modules\Agents\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SiteSetting;
use Modules\Agents\Models\Agent;

class AgentAuthController extends Controller
{
    public function settings()
    {
        $logo = SiteSetting::get('logo');
        $siteName = SiteSetting::get('site_name', 'abcsheba');

        return response()->json([
            'success' => true,
            'settings' => [
                'site_name' => $siteName,
                'logo_url' => $logo ? asset($logo) : asset('assets/img/logo.png'),
            ]
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->orWhere('phone', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        if (!$user->isAgent() || !$user->agent) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Account is not registered as an Agent.',
            ], 403);
        }

        if ($user->agent->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your agent account is currently ' . $user->agent->status . '. Please contact support.',
            ], 403);
        }

        $token = $user->createToken('agent_app_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'profile_image' => $user->profile_image_url,
                'agent_info' => [
                    'id' => $user->agent->id,
                    'referral_code' => $user->agent->referral_code,
                    'status' => $user->agent->status,
                    'wallet_balance' => (float) $user->agent->wallet_balance,
                ]
            ]
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        $agent = $user->agent;

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile_image' => $user->profile_image_url,
                'agent_info' => [
                    'id' => $agent->id,
                    'referral_code' => $agent->referral_code,
                    'status' => $agent->status,
                    'wallet_balance' => (float) $agent->wallet_balance,
                ]
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = $request->user();
        $user->update([
            'fcm_token' => $request->fcm_token,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FCM Token updated successfully',
        ]);
    }
}
