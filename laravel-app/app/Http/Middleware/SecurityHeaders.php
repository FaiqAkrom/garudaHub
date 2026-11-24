<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Suppress PHP version disclosure (medium & low-4)
        @ini_set('expose_php', 'off');
        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
        }

        $response = $next($request);

        // Suppress server signature / version headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        $response->headers->set('Server', 'webserver');

        // Prevent MIME type sniffing (low-5)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent XSS
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Feature / Permissions policy
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Content Security Policy without unsafe-inline (medium-1, medium-2, medium-3)
        $csp = "default-src 'self'; " .
               "script-src 'self'; " .
               "style-src 'self'; " .
               "img-src 'self' data:; " .
               "font-src 'self' data:; " .
               "connect-src 'self'; " .
               "frame-ancestors 'self'; " .
               "form-action 'self'; " .
               "base-uri 'self'; " .
               "object-src 'none';";
        $response->headers->set('Content-Security-Policy', $csp);

        // Fix low-1 & low-2: Ensure redirect responses do not leak sensitive details or large bodies
        if ($response->isRedirection()) {
            $response->setContent('');
        }

        // Fix low-3: Ensure all cookies have HttpOnly flag set to prevent JavaScript access
        foreach ($response->headers->getCookies() as $cookie) {
            if (! $cookie->isHttpOnly()) {
                $response->headers->setCookie(
                    new Cookie(
                        $cookie->getName(),
                        $cookie->getValue(),
                        $cookie->getExpiresTime(),
                        $cookie->getPath(),
                        $cookie->getDomain(),
                        $cookie->isSecure(),
                        true, // HttpOnly
                        $cookie->isRaw(),
                        $cookie->getSameSite()
                    )
                );
            }
        }

        return $response;
    }
}
