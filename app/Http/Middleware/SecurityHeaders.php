<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * OWASP: Security Headers
 * - X-Content-Type-Options: prevents MIME-sniffing attacks.
 * - X-Frame-Options: prevents clickjacking.
 * - Referrer-Policy: limits referrer information leakage.
 * - Permissions-Policy: restricts browser features.
 * - Cache-Control: prevents caching of sensitive JSON responses.
 * - Removes X-Powered-By: prevents framework fingerprinting.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->is('api/*')) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->remove('X-Powered-By');
        }

        return $response;
    }
}
