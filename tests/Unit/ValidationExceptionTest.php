<?php

declare(strict_types=1);

use App\Exceptions\ApiException;
use App\Exceptions\ValidationException;

it('sets status code to 422', function (): void {
    $exception = new ValidationException(['email' => 'required']);

    expect($exception->getStatusCode())->toBe(422);
});

it('json encodes errors in message', function (): void {
    $errors = ['email' => 'The email field is required.'];
    $exception = new ValidationException($errors);
    $decoded = json_decode($exception->getMessage(), true);

    expect($decoded)->toHaveKey('message', 'Some fields failed to pass validation.');
    expect($decoded)->toHaveKey('fields');
    expect($decoded['fields'])->toBe($errors);
});

it('adds validation failed header', function (): void {
    $exception = new ValidationException(['name' => 'required']);

    expect($exception->getHeaders())->toHaveKey('X-Status-Reason', 'Validation failed.');
});

it('extends ApiException', function (): void {
    $exception = new ValidationException([]);

    expect($exception)->toBeInstanceOf(ApiException::class);
});
