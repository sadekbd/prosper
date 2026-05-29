<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrAbove
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('admin')->user();

        if (!$user || !in_array($user->role, ['super_admin', 'admin'])) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}