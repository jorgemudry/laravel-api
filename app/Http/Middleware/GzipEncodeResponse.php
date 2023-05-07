<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GzipEncodeResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Check if the client accepts gzip encoding
        if (in_array('gzip', $request->getEncodings())) {
            // Compress the response content using gzip
            $compressedContent = gzencode($response->getContent(), 9);
            $response->setContent($compressedContent);

            // Set the response headers
            $response->header('Content-Encoding', 'gzip');
            $response->header('Content-Length', mb_strlen(strval($compressedContent)));
            $response->header('Vary', 'Accept-Encoding');
        }

        return $response;
    }
}
