<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\ResetPasswordMail;

class PasswordResetController extends Controller
{
    // Show request link form
    public function showLinkRequestForm()
    {
        return view('frontend.forgot-password');
    }

    // Send reset link email
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'We cannot find a user with that email address.',
        ]);

        $token = Str::random(60);

        // Delete any existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Store new token (using Hash::make for security, matching Laravel defaults)
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        // Generate reset URL
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);

        // Send email
        Mail::to($request->email)->send(new ResetPasswordMail($resetUrl));

        return back()->with('status', 'We have emailed your password reset link!');
    }

    // Show reset form
    public function showResetForm(Request $request, $token)
    {
        $email = $request->email;
        return view('frontend.reset-password', compact('token', 'email'));
    }

    // Reset password
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Retrieve token record
        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record) {
            return back()->withErrors(['email' => 'This password reset link is invalid.']);
        }

        // Verify token
        if (!Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'This password reset token is invalid.']);
        }

        // Check if token has expired (60 minutes expiration)
        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'This password reset link has expired.']);
        }

        // Find user and update password
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'We cannot find a user with that email address.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Clean up token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Your password has been reset successfully! Please login with your new password.');
    }
}
