<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('admin')->user();

        if (!$user || $user->role !== 'super_admin') {
            abort(403, 'Access denied. Super Admin privileges required.');
        }

        return $next($request);
    }
}