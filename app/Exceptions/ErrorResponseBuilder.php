<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Http\Resources\ExceptionResource;

class ErrorResponseBuilder
{
    public function __construct(
        protected FlattenException $exception
    ) {}

    public static function fromFlatten(FlattenException $exception): self
    {
        return new self($exception);
    }

    public function build(bool $debug = false): ExceptionResource
    {
        $this->exception->setHeaders(array_merge(
            $this->exception->getHeaders(),
            ['x-app-debug' => $debug]
        ));

        return new ExceptionResource($this->exception);
    }
}
