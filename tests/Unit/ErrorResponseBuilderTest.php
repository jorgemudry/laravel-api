<?php

declare(strict_types=1);

use App\Exceptions\ErrorResponseBuilder;
use App\Exceptions\FlattenException;
use App\Http\Resources\ExceptionResource;

it('creates a new instance from the static method', function (): void {
    $exception = new FlattenException();
    $instance = ErrorResponseBuilder::fromFlatten($exception);

    expect($instance)->toBeInstanceOf(ErrorResponseBuilder::class);
});

it('returns a ExceptionResource from the build method', function (): void {
    $exception = FlattenException::createFromThrowable(new Exception('This is my exception message.'), 500);
    $builder = ErrorResponseBuilder::fromFlatten($exception);

    expect($builder->build())->toBeInstanceOf(ExceptionResource::class);
});
