<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventPageCaching
{
    /**
     * Handle an incoming request.
     *
     * Injects strict Cache-Control and security headers to prevent local browser
     * caching of confidential patient health information (PHI) and protect
     * browser back-button history navigation after session logout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Strict Anti-Caching Headers (prevents memory/disk caching of sensitive healthcare data)
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');

        // Security Defense Headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
