<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\ErrorHandler\Exception\FlattenException as BaseFlattenException;
use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class FlattenException extends BaseFlattenException
{
    protected Throwable $original;

    /**
     * @param array<string, string> $headers
     */
    public static function createFromThrowable(Throwable $exception, ?int $statusCode = null, array $headers = []): static
    {

        $e = new static(); /** @phpstan-ignore-line */
        $e->setMessage($exception->getMessage());
        $e->setCode($exception->getCode());
        $e->setOriginalException($exception);

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $headers = array_merge($headers, $exception->getHeaders());
        } elseif ($exception instanceof RequestExceptionInterface) {
            $statusCode = 400;
        }

        $statusCode ??= 500;

        if (class_exists(Response::class) && isset(Response::$statusTexts[$statusCode])) {
            $statusText = Response::$statusTexts[$statusCode];
        } else {
            $statusText = 'Whoops, looks like something went wrong.';
        }

        $e->setStatusText($statusText);
        $e->setStatusCode($statusCode);
        $e->setHeaders($headers);
        $e->setTraceFromThrowable($exception);
        $e->setClass(get_debug_type($exception));
        $e->setFile($exception->getFile());
        $e->setLine($exception->getLine());

        $previous = $exception->getPrevious();

        if ($previous instanceof Throwable) {
            $e->setPrevious(static::createFromThrowable($previous));
        }

        return $e;
    }

    public function setOriginalException(Throwable $exception): void
    {
        $this->original = $exception;
    }

    public function getOriginalException(): Throwable
    {
        return $this->original;
    }
}
