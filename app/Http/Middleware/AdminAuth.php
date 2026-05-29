<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        // Not logged in → redirect to admin login
        if (!auth('admin')->check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Please log in to access the admin panel.');
        }

        $user = auth('admin')->user();

        // Account not active → force logout
        if ($user->status !== 'active') {
            auth('admin')->logout();
            $request->session()->invalidate();

            $message = match($user->status) {
                'pending' => 'Your account is pending approval. Please contact the administrator.',
                'blocked' => 'Your account has been suspended. Please contact support.',
                default   => 'Your account is not active.',
            };

            return redirect()->route('admin.login')->with('error', $message);
        }

        return $next($request);
    }
}