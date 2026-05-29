<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAdminAuthenticated
{
    /** Redirect already-logged-in admins away from auth pages. */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('admin')->check() && auth('admin')->user()->status === 'active') {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}