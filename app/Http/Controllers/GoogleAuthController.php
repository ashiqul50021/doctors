<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleMeetService;

class GoogleAuthController extends Controller
{
    /**
     * Handle Google OAuth callback
     */
    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('home')
                ->with('error', 'Google authorization was denied: ' . $request->get('error'));
        }

        $code = $request->get('code');
        if (!$code) {
            return redirect()->route('home')
                ->with('error', 'No authorization code received from Google.');
        }

        $meetService = new GoogleMeetService();
        $success = $meetService->authenticate($code);

        if ($success) {
            return redirect()->route('home')
                ->with('success', 'Google Calendar connected successfully! Video consultations will now generate Meet links.');
        }

        return redirect()->route('home')
            ->with('error', 'Failed to connect Google Calendar. Please try again.');
    }

    /**
     * Start the Google OAuth flow (admin only)
     */
    public function connect()
    {
        $meetService = new GoogleMeetService();

        if (!$meetService->hasCredentials()) {
            return redirect()->back()
                ->with('error', 'Google credentials.json not found. Please upload it first.');
        }

        if ($meetService->hasValidToken()) {
            return redirect()->back()
                ->with('success', 'Google Calendar is already connected!');
        }

        return redirect()->away($meetService->getAuthUrl());
    }

    /**
     * Check Google Meet status (API endpoint)
     */
    public function status()
    {
        $meetService = new GoogleMeetService();

        return response()->json([
            'credentials' => $meetService->hasCredentials(),
            'token' => $meetService->hasValidToken(),
            'ready' => $meetService->isReady(),
        ]);
    }
}
