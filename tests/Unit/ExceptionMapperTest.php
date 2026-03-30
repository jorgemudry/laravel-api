<?php

declare(strict_types=1);

use App\Exceptions\ExceptionMapper;
use App\Exceptions\FlattenException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Illuminate\Validation\Validator;

it('maps ModelNotFoundException to 404', function (): void {
    $exception = new ModelNotFoundException();
    $flatten = FlattenException::createFromThrowable($exception);
    $mapped = ExceptionMapper::fromThrowable($exception)->map($flatten);

    expect($mapped->getStatusCode())->toBe(404);
});

it('maps AuthorizationException to 403', function (): void {
    $exception = new AuthorizationException();
    $flatten = FlattenException::createFromThrowable($exception);
    $mapped = ExceptionMapper::fromThrowable($exception)->map($flatten);

    expect($mapped->getStatusCode())->toBe(403);
});

it('maps AuthenticationException to 401', function (): void {
    $exception = new AuthenticationException();
    $flatten = FlattenException::createFromThrowable($exception);
    $mapped = ExceptionMapper::fromThrowable($exception)->map($flatten);

    expect($mapped->getStatusCode())->toBe(401);
});

it('maps ValidationException to 422', function (): void {
    $translator = Mockery::mock(Illuminate\Contracts\Translation\Translator::class);
    $translator->shouldReceive('get')->andReturn('The given data was invalid.');
    $validator = Mockery::mock(Validator::class);
    $validator->shouldReceive('errors')->andReturn(new Illuminate\Support\MessageBag());
    $validator->shouldReceive('getTranslator')->andReturn($translator);
    $exception = new LaravelValidationException($validator);
    $flatten = FlattenException::createFromThrowable($exception);
    $mapped = ExceptionMapper::fromThrowable($exception)->map($flatten);

    expect($mapped->getStatusCode())->toBe(422);
});

it('sets default message when message is empty', function (): void {
    $exception = new ModelNotFoundException();
    $flatten = FlattenException::createFromThrowable($exception);
    $flatten->setMessage('');
    $mapped = ExceptionMapper::fromThrowable($exception)->map($flatten);

    expect($mapped->getMessage())->toBe('Sorry, the page you are looking for could not be found.');
});

it('preserves existing message when not empty', function (): void {
    $exception = new RuntimeException('Custom error message');
    $flatten = FlattenException::createFromThrowable($exception);
    $mapped = ExceptionMapper::fromThrowable($exception)->map($flatten);

    expect($mapped->getMessage())->toBe('Custom error message');
});

it('sets generic message for non-404 empty messages', function (): void {
    $exception = new AuthenticationException();
    $flatten = FlattenException::createFromThrowable($exception);
    $flatten->setMessage('');
    $mapped = ExceptionMapper::fromThrowable($exception)->map($flatten);

    expect($mapped->getMessage())->toBe('Whoops, looks like something went wrong.');
});
