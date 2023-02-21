<?php

declare(strict_types=1);

use App\Exceptions\ErrorResponseBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

it('creates a new instance from the static method', function (): void {
    $exception = new FlattenException();
    $instance = ErrorResponseBuilder::fromFlatten($exception);

    expect($instance)->toBeInstanceOf(ErrorResponseBuilder::class);
});

it('returns a JsonResponse from the build method', function (): void {
    $exception = FlattenException::createFromThrowable(new \Exception('This is my exception message.'), 500);
    $builder = ErrorResponseBuilder::fromFlatten($exception);

    expect($builder->build())->toBeInstanceOf(JsonResponse::class);
});

it('adds the propper code and message in the metadata', function (): void {
    $exception = FlattenException::createFromThrowable(
        new \InvalidArgumentException('This is my exception message.'),
        400
    );
    $builder = ErrorResponseBuilder::fromFlatten($exception);
    $message =  json_decode(strval($builder->build()->getContent()), true);
    $expect = [
        'code' => 400,
        'message' => Response::$statusTexts[400],
    ];

    expect($message['metadata'])->toEqual($expect);
});

it('adds the exception message and the exception class to the response', function (): void {
    $exception = FlattenException::createFromThrowable(
        new \InvalidArgumentException('This is my exception message.'),
        400
    );
    $builder = ErrorResponseBuilder::fromFlatten($exception);
    $message =  json_decode(strval($builder->build()->getContent()), true);

    $expect = [
        'message' => 'This is my exception message.',
        'type' => \InvalidArgumentException::class,
    ];

    expect($message['error'])->toEqual($expect);
});

it('does not return extra info by default', function (): void {
    $exception = FlattenException::createFromThrowable(
        new \InvalidArgumentException('This is my exception message.'),
        400
    );
    $builder = ErrorResponseBuilder::fromFlatten($exception);
    $message =  json_decode(strval($builder->build()->getContent()), true);

    expect($message)->not->toHaveKeys(['error.file', 'error.line', 'error.trace']);
});

it('returns extra info when requested', function (): void {
    $exception = FlattenException::createFromThrowable(
        new \InvalidArgumentException('This is my exception message.'),
        400
    );
    $builder = ErrorResponseBuilder::fromFlatten($exception);
    $message =  json_decode(strval($builder->build(debug: true)->getContent()), true);

    expect($message)->toHaveKeys(['error.file', 'error.line', 'error.trace']);
});
