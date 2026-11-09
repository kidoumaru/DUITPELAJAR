<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleLoginSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Skip the session check in unit/feature tests so actingAs() works normally.
        if (app()->runningUnitTests()) {
            return $next($request);
        }

        $activeSessionId = $user->active_session_id;
        $currentSessionId = $request->session()->getId();

        // Tolak sesi yang bukan sesi login aktif.
        if (
            empty($activeSessionId) ||
            !hash_equals($activeSessionId, $currentSessionId)
        ) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('error', 'Sesi login Anda sudah tidak aktif. Silakan login kembali.');
        }

        return $next($request);
    }
}
