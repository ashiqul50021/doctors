<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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

        $now = now();
        $lastTouchedAt = (int) $request->session()->get('last_seen_touched_at', 0);

        if (($now->timestamp - $lastTouchedAt) >= 60) {
            $user->forceFill([
                'last_seen_at' => $now,
            ])->saveQuietly();

            $request->session()->put('last_seen_touched_at', $now->timestamp);
        }

        return $response;
    }
}
