<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
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

    public function map(FlattenException $exception): FlattenException
    {
        $exception = $this->setStatusCode($exception);

        return $this->setMessage($exception);
    }

    protected function setStatusCode(FlattenException $exception): FlattenException
    {
        $instanceof = $this->exception::class;

        switch ($instanceof) {
            case ModelNotFoundException::class:
                $exception->setStatusCode(Response::HTTP_NOT_FOUND);
                break;

            case AuthorizationException::class:
                $exception->setStatusCode(Response::HTTP_FORBIDDEN);
                break;

            case AuthenticationException::class:
                $exception->setStatusCode(Response::HTTP_UNAUTHORIZED);
                break;
            case ValidationException::class:
                $exception->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
                break;
        }

        return $exception;
    }

    protected function setMessage(FlattenException $exception): FlattenException
    {
        $message = $exception->getMessage();

        if (empty($message) === false) {
            return $exception;
        }

        $exception->setMessage('Whoops, looks like something went wrong.');

        if ($exception->getStatusCode() === 404) {
            $exception->setMessage('Sorry, the page you are looking for could not be found.');
        }

        return $exception;
    }
}
