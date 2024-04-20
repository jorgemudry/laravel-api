<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /** @var array<int, string> */
    private array $unwantedHeaders = ['X-Powered-By', 'server', 'Server'];

    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        if (app()->environment('local') === false) {
            $response->headers->set(
                'Referrer-Policy',
                'no-referrer-when-downgrade'
            );
            $response->headers->set(
                'X-XSS-Protection',
                '1; mode=block'
            );
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; script-src 'self' platform.twitter.com plausible.io utteranc.es *.cloudflare.com 'unsafe-inline' 'unsafe-eval' plausible.io/js/plausible.js utteranc.es/client.js; style-src 'self' *.cloudflare.com 'unsafe-inline'; img-src 'self' * data:; font-src 'self' data: ; connect-src 'self' plausible.io/api/event; media-src 'self'; frame-src 'self' platform.twitter.com plausible.io utteranc.es github.com *.youtube.com *.vimeo.com; object-src 'none'; base-uri 'self';" //phpcs:ignore
            );
            $response->headers->set(
                'Expect-CT',
                'enforce, max-age=30'
            );
            $response->headers->set(
                'Permissions-Policy',
                'autoplay=(self), camera=(), encrypted-media=(self), fullscreen=(), geolocation=(self), gyroscope=(self), magnetometer=(), microphone=(), midi=(), payment=(), sync-xhr=(self), usb=()' //phpcs:ignore
            );
            $response->headers->set(
                'Access-Control-Allow-Origin',
                '*'
            );
            $response->headers->set(
                'Access-Control-Allow-Methods',
                'GET,POST,PUT,PATCH,DELETE,OPTIONS'
            );
            $response->headers->set(
                'Access-Control-Allow-Headers',
                'Content-Type,Authorization,X-Requested-With,X-CSRF-Token'
            );

            $this->removeUnwantedHeaders($this->unwantedHeaders);
        }

        return $response;
    }

    /**
     * @param  array<int, string>  $headers
     */
    private function removeUnwantedHeaders(array $headers): void
    {
        foreach ($headers as $header) {
            header_remove($header);
        }
    }
}
