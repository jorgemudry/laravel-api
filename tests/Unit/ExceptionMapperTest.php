<?php

declare(strict_types=1);

use App\Exceptions\ExceptionMapper;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

it('returns a ExceptionMapper from fromThrowable method', function (): void {
    $instance = ExceptionMapper::fromThrowable(new \InvalidArgumentException('Some exception message'));

    expect($instance)->toBeInstanceOf(ExceptionMapper::class);
});

it('maps ModelNotFoundException to 404', function (): void {
    $exception = new ModelNotFoundException();
    $flattenException = FlattenException::createFromThrowable($exception);

    $mapper = ExceptionMapper::fromThrowable($exception);
    $mapped = $mapper->map($flattenException);

    expect($mapped->getStatusCode())->toBe(Response::HTTP_NOT_FOUND);
    expect($mapped->getMessage())->toBe('Sorry, the page you are looking for could not be found.');
});

it('maps AuthorizationException to 403', function (): void {
    $exception = new AuthorizationException();
    $flattenException = FlattenException::createFromThrowable($exception);

    $mapper = ExceptionMapper::fromThrowable($exception);
    $mapped = $mapper->map($flattenException);

    expect($mapped->getStatusCode())->toBe(Response::HTTP_FORBIDDEN);
    expect($mapped->getMessage())->toBe('This action is unauthorized.');
});

it('maps AuthenticationException to 401', function (): void {
    $exception = new AuthenticationException();
    $flattenException = FlattenException::createFromThrowable($exception);

    $mapper = ExceptionMapper::fromThrowable($exception);
    $mapped = $mapper->map($flattenException);

    expect($mapped->getStatusCode())->toBe(Response::HTTP_UNAUTHORIZED);
    expect($mapped->getMessage())->toBe('Unauthenticated.');
});

it('maps ValidationException to 422', function (): void {
    $exception = new ValidationException(Validator::make([], []));
    $flattenException = FlattenException::createFromThrowable($exception);

    $mapper = ExceptionMapper::fromThrowable($exception);
    $mapped = $mapper->map($flattenException);

    expect($mapped->getStatusCode())->toBe(Response::HTTP_UNPROCESSABLE_ENTITY);
    expect($mapped->getMessage())->toBe('The given data was invalid.');
});

it('maps generic Exception to 500', function (): void {
    $exception = new \Exception();
    $flattenException = FlattenException::createFromThrowable($exception);

    $mapper = ExceptionMapper::fromThrowable($exception);
    $mapped = $mapper->map($flattenException);

    expect($mapped->getStatusCode())->toBe(Response::HTTP_INTERNAL_SERVER_ERROR);
    expect($mapped->getMessage())->toBe('Whoops, looks like something went wrong.');
});
