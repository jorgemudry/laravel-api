<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

abstract class ApiResource extends JsonResource
{
    /**
     * Customize the outgoing response for the resource.
     */
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $content = json_decode(
            strval($response->getContent()),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $content['metadata'] = [
            'code' => $response->getStatusCode(),
            'message' => Response::$statusTexts[$response->getStatusCode()],
        ];

        $response->setContent(strval(json_encode($content, JSON_THROW_ON_ERROR)));
    }
}
