<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use ReflectionClass;

class ExceptionResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'error';

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
            'message' => $this->getMessage(),
            'type' => (new ReflectionClass($previous))->getShortName(),
        ];

        if ($previous === ValidationException::class) {
            $errors = json_decode((string) $this->getMessage(), true, 512, JSON_THROW_ON_ERROR);
            $response['message'] = $errors['message'];
            $response['fields'] = $errors['fields'];
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
    }
}
