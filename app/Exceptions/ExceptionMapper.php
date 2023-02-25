<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException as LaravelAuthorizationException;
use Illuminate\Auth\AuthenticationException as LaravelAuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException as LaravelValidationException;
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

        switch ($instanceof) {
            case ModelNotFoundException::class:
                return new NotFoundHttpException(
                    message: $this->exception->getMessage(),
                    previous: $this->exception,
                    code: $this->exception->getCode()
                );

            case LaravelAuthorizationException::class:
                return new AuthorizationException(
                    message: $this->exception->getMessage(),
                    previous: $this->exception,
                    code: $this->exception->getCode()
                );

            case LaravelAuthenticationException::class:
                return new AuthenticationException(
                    message: $this->exception->getMessage(),
                    previous: $this->exception,
                    code: $this->exception->getCode()
                );

            case LaravelValidationException::class:
                /** @var LaravelValidationException $exception */
                $exception = $this->exception;

                return new ValidationException(
                    errors: $exception->errors(),
                    previous: $exception,
                    code: $exception->getCode()
                );
        }

        return $this->exception;
    }
}
