<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionMapper
{
    public function __construct(protected Throwable $exception)
    {
    }

    public static function fromThrowable(Throwable $exception): self
    {
        return new self($exception);
    }

    public function map(): Throwable
    {
        $instanceof = $this->exception::class;
        $exception = $this->exception;

        switch ($instanceof) {
            case ModelNotFoundException::class:
                return new NotFoundHttpException(
                    message: $this->exception->getMessage(),
                    previous: $this->exception,
                    code: $this->exception->getCode()
                );

            case AuthorizationException::class:
                return new HttpException(
                    statusCode: 403,
                    message: $this->exception->getMessage(),
                    previous: $this->exception,
                    code: $this->exception->getCode()
                );

            case AuthenticationException::class:
                return new HttpException(
                    statusCode: 401,
                    message: $this->exception->getMessage(),
                    previous: $this->exception,
                    code: $this->exception->getCode()
                );
        }

        return $exception;
    }
}
