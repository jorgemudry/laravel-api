<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use ReflectionClass;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

class ErrorResponseBuilder
{
    public function __construct(
        protected FlattenException $exception
    ) {
    }

    public static function fromFlatten(FlattenException $exception): self
    {
        return new self($exception);
    }

    public function build(bool $debug = false): JsonResponse
    {
        /** @var class-string $previous */
        $previous = $this->exception->getClass();

        $response = [
            'metadata' => [
                'code' => $this->exception->getStatusCode(),
                'message' => $this->exception->getStatusText(),
            ],
            'error' => [
                'message' => $this->exception->getMessage(),
                'type' => (new ReflectionClass($previous))->getShortName(),
            ],
        ];

        if ($debug) {
            $response['error']['file'] = $this->exception->getFile();
            $response['error']['line'] = $this->exception->getLine();
            $response['error']['trace'] = $this->exception->getTrace();
        }

        return new JsonResponse(
            $response,
            $response['metadata']['code'],
            $this->exception->getHeaders()
        );
    }
}
