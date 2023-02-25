<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourceResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $content = json_decode(strval($response->getContent()), true);
            $content['metadata'] = [
                'code' => $response->getStatusCode(),
                'message' => Response::$statusTexts[$response->getStatusCode()],
            ];
            $content = $this->parsePagination($content);
            $response->setContent(strval(json_encode($content)));
        }

        return $response;
    }

    /**
     * @param array<string, mixed> $content
     *
     * @return array<string, mixed>
     */
    protected function parsePagination(array $content): array
    {
        // pagination
        if (array_key_exists('meta', $content)) {
            $meta = $content['meta'];

            //in order to prevent large responses, "links" property inside meta is removed
            unset($meta['links']);

            $content['pagination'] = $meta;
            unset($content['meta']);
        }

        return $content;
    }
}
