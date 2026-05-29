<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleWriterAccess
{
    /** Article writers can only manage their own articles. */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('admin')->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        // All roles pass — but controllers further restrict by role
        return $next($request);
    }
}