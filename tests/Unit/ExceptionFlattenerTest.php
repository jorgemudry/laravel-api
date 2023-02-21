<?php

declare(strict_types=1);

use App\Exceptions\ExceptionFlattener;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

it('creates a new instance from the fromThrowable method', function (): void {
    $instance = ExceptionFlattener::fromThrowable(new \InvalidArgumentException('Some exception message'));

    expect($instance)->toBeInstanceOf(ExceptionFlattener::class);
});

it('converts a Throwable into a FlattenException', function (): void {
    $flattener = ExceptionFlattener::fromThrowable(new \InvalidArgumentException('Some exception message'));
    $exception = $flattener->flatten();

    expect($exception)->toBeInstanceOf(FlattenException::class);
    expect($exception->getMessage())->toBe('Some exception message');
    expect($exception->getStatusCode())->toBe(500);
    expect($exception->getClass())->toBe(\InvalidArgumentException::class);
});
