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
    public function __invoke(Throwable $exception): Throwable
    {
        $instanceof = $exception::class;

        switch ($instanceof) {
            case ModelNotFoundException::class:
                $exception = new NotFoundHttpException(
                    message: $exception->getMessage(),
                    previous: $exception,
                    code: $exception->getCode()
                );
                break;
            case AuthorizationException::class:
                $exception = new HttpException(
                    statusCode: 403,
                    message: $exception->getMessage(),
                    previous: $exception,
                    code: $exception->getCode()
                );
                break;
            case AuthenticationException::class:
                $exception = new HttpException(
                    statusCode: 401,
                    message: $exception->getMessage(),
                    previous: $exception,
                    code: $exception->getCode()
                );
                break;
        }

        return $exception;
    }
}
