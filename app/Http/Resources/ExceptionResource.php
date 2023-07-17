<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;
use JsonException;
use ReflectionClass;

/**
 * @mixin \App\Exceptions\FlattenException
 */
class ExceptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $exception = $this->getOriginalException();
        $headers = $this->getHeaders();

        $response = [
            'status' => $this->getStatusCode(),
            'type' => (new ReflectionClass($exception))->getShortName(),
            'error' => $this->getMessage(),
        ];

        if ($exception::class === ValidationException::class) {
            $response['error'] = 'Some fields failed to pass validation.';
            $response['fields'] = $exception->errors();
            $headers = [...$headers, 'X-Status-Reason' => 'Validation failed.'];

            $this->setHeaders($headers);
        }

        if (array_key_exists('x-app-debug', $headers) && $headers['x-app-debug']) {
            $response['file'] = $this->getFile();
            $response['line'] = $this->getLine();
            $response['trace'] = $this->getTrace();
        }

        return $response;
    }

    /**
     * Customize the outgoing response for the resource.
     * @throws JsonException
     */
    public function withResponse(Request $request, JsonResponse $response): void
    {
        $headers = $this->getHeaders();

        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        $response->setStatusCode($this->getStatusCode());
        $content = json_decode(
            strval($response->getContent()),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        $response->setContent(strval(json_encode($content['data'], JSON_THROW_ON_ERROR)));
    }
}
