<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // ── Prevent MIME-type sniffing ─────────────────────────
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // ── Prevent clickjacking ──────────────────────────────
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // ── XSS protection (legacy browsers) ──────────────────
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // ── Referrer policy ───────────────────────────────────
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ── Feature/Permissions policy ────────────────────────
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), payment=()'
        );

        // ── HSTS — only in production ─────────────────────────
        if (app()->isProduction()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // ── Remove server fingerprinting ──────────────────────
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}