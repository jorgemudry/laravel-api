<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;
use ReflectionClass;

/**
 * @mixin \Symfony\Component\ErrorHandler\Exception\FlattenException
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
        /** @var class-string $previous */
        $previous = $this->getClass();
        $headers = $this->getHeaders();

        $response = [
            'status' => $this->getStatusCode(),
            'type' => (new ReflectionClass($previous))->getShortName(),
            'error' => $this->getMessage(),
        ];

        if ($previous === ValidationException::class) {
            /** @var ValidationException $exception */
            $exception = $this->getPrevious();

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
