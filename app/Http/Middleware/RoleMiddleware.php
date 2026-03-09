<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!Auth::check()) {
            if ($role === 'admin') {
                return redirect()->route('admin.login');
            }

            if ($role === 'doctor') {
                return redirect()->route('doctor.login');
            }

            return redirect()->route('patient.login');
        }

        $user = Auth::user();
        if ($user->role !== $role) {
            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'doctor') {
                return redirect()->route('doctors.dashboard');
            }
            return redirect()->route('patient.dashboard');
        }

        return $next($request);
    }
}
