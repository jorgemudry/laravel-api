<?php

declare(strict_types=1);

use App\Exceptions\ErrorResponseBuilder;
use App\Exceptions\FlattenException;
use App\Http\Resources\ExceptionResource;

it('creates instance from flatten exception', function (): void {
    $exception = FlattenException::createFromThrowable(new RuntimeException('test'));
    $builder = ErrorResponseBuilder::fromFlatten($exception);

    expect($builder)->toBeInstanceOf(ErrorResponseBuilder::class);
});

it('builds an exception resource', function (): void {
    $exception = FlattenException::createFromThrowable(new RuntimeException('test'));
    $resource = ErrorResponseBuilder::fromFlatten($exception)->build();

    expect($resource)->toBeInstanceOf(ExceptionResource::class);
});

it('adds debug header based on flag', function (): void {
    $exception = FlattenException::createFromThrowable(new RuntimeException('test'));

    ErrorResponseBuilder::fromFlatten($exception)->build(debug: true);
    expect($exception->getHeaders())->toHaveKey('x-app-debug', true);

    $exception2 = FlattenException::createFromThrowable(new RuntimeException('test'));
    ErrorResponseBuilder::fromFlatten($exception2)->build(debug: false);
    expect($exception2->getHeaders())->toHaveKey('x-app-debug', false);
});
