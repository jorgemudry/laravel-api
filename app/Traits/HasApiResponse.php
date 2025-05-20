<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use JsonException;

trait HasApiResponse
{
    /**
     * @throws JsonException
     */
    protected function response(JsonResource $resource): JsonResponse
    {
        $response = $resource->response();
        $content = json_decode(strval($response->getContent()), true, 512, JSON_THROW_ON_ERROR);
        $content['metadata'] = [
            'code' => $response->getStatusCode(),
            'message' => Response::$statusTexts[$response->getStatusCode()],
        ];
        $content = $this->parsePagination($content);
        $response->setContent(strval(json_encode($content, JSON_THROW_ON_ERROR)));

        return $response;
    }

    /**
     * @param array<string, mixed> $content
     * @return array<string, mixed>
     */
    protected function parsePagination(array $content): array
    {
        // pagination
        if (array_key_exists('meta', $content)) {
            $meta = $content['meta'];

            // in order to prevent large responses, "links" property inside meta is removed
            unset($meta['links']);

            $content['pagination'] = $meta;
            unset($content['meta']);
        }

        return $content;
    }
}
