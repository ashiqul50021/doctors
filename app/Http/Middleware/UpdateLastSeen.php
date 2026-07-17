<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Cache;

class UpdateLastSeen
{
    /**
     * Keep a lightweight heartbeat for authenticated users.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = $request->user();
        if (!$user) {
            return $response;
        }

        // Cache online status for 3 minutes (which is > 2 min threshold)
        Cache::put('user-online-' . $user->id, true, now()->addMinutes(3));

        $now = now();
        $lastTouchedAt = (int) $request->session()->get('last_seen_touched_at', 0);

        // Update database last_seen_at once every 5 minutes to reduce DB write load
        if (($now->timestamp - $lastTouchedAt) >= 300) {
            $user->forceFill([
                'last_seen_at' => $now,
            ])->saveQuietly();

            $request->session()->put('last_seen_touched_at', $now->timestamp);
        }

        return $response;
    }
}
