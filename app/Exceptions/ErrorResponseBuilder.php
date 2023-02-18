<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use ReflectionClass;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

class ErrorResponseBuilder
{
    public function __construct(
        protected FlattenException $exception
    ) {
    }

    public function response(): JsonResponse
    {
        $response = [
            'metadata' => [
                'code' => $this->exception->getStatusCode(),
                'message' => Response::$statusTexts[$this->exception->getStatusCode()],
            ],
            'error' => [
                'message' => $this->exception->getMessage(),
                'type' => (new ReflectionClass($this->exception))->getShortName(),
            ],
        ];

        if (config('app.debug')) {
            $response['error']['file'] = $this->exception->getFile();
            $response['error']['line'] = $this->exception->getLine();
            $response['error']['trace'] = $this->exception->getTrace();
        }

        return new JsonResponse($response, $response['metadata']['code'], $this->exception->getHeaders());
    }
}
