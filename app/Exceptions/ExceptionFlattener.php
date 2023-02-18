<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Throwable;

class ExceptionFlattener
{
    public function __construct(protected Throwable $exception)
    {
    }

    public static function fromThrowable(Throwable $exception): self
    {
        return new self($exception);
    }

    public function normalize(): FlattenException
    {
        $exception = (new ExceptionMapper($this->exception))();
        $exception = FlattenException::createFromThrowable($exception);
        $exception->setMessage($this->message($exception));

        return $exception;
    }

    protected function message(FlattenException $fe): string
    {
        $message = $fe->getMessage();

        if (empty($message) === false) {
            return $message;
        }

        if ($fe->getStatusCode() === 404) {
            return 'Sorry, the page you are looking for could not be found.';
        }

        return 'Whoops, looks like something went wrong.';
    }
}
