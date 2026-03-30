<?php

declare(strict_types=1);

use App\Exceptions\FlattenException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

it('creates from generic exception with status 500', function (): void {
    $exception = new RuntimeException('Something failed');
    $flatten = FlattenException::createFromThrowable($exception);

    expect($flatten->getStatusCode())->toBe(500);
    expect($flatten->getMessage())->toBe('Something failed');
});

it('creates from http exception with custom status code', function (): void {
    $exception = new NotFoundHttpException('Not found');
    $flatten = FlattenException::createFromThrowable($exception);

    expect($flatten->getStatusCode())->toBe(404);
});

it('preserves exception message and code', function (): void {
    $exception = new RuntimeException('Test message', 42);
    $flatten = FlattenException::createFromThrowable($exception);

    expect($flatten->getMessage())->toBe('Test message');
    expect($flatten->getCode())->toBe(42);
});

it('stores and retrieves original exception', function (): void {
    $exception = new RuntimeException('Original');
    $flatten = FlattenException::createFromThrowable($exception);

    expect($flatten->getOriginalException())->toBe($exception);
});

it('handles previous exceptions', function (): void {
    $previous = new RuntimeException('Previous');
    $exception = new RuntimeException('Current', 0, $previous);
    $flatten = FlattenException::createFromThrowable($exception);

    expect($flatten->getPrevious())->not->toBeNull();
    expect($flatten->getPrevious()->getMessage())->toBe('Previous');
});
