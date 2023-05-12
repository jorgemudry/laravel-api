<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EncodingResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request):(Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);
        $content = $response->getContent();
        $encodings = $request->getEncodings();

        if (in_array('br', $encodings) && function_exists('brotli_compress')) {
            $content = brotli_compress($response->getContent(), 4);
            $response->header('Content-Encoding', 'br');
        }

        if (in_array('deflate', $encodings)) {
            $content = gzdeflate(strval($response->getContent()), 9);
            $response->header('Content-Encoding', 'deflate');
        }

        if (in_array('gzip', $encodings)) {
            $content = gzencode(strval($response->getContent()), 9);
            $response->header('Content-Encoding', 'gzip');
        }

        $response->setContent($content);
        $response->header('Content-Length', strval(mb_strlen(strval($content))));
        $response->header('Vary', 'Accept-Encoding');

        return $response;
    }
}
