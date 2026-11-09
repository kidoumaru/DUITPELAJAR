<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotAdmin
{
    /**
     * Block admin accounts from accessing user-facing pages.
     * Admins should only use /admin/* routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
