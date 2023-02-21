<?php

declare(strict_types=1);

use App\Exceptions\ExceptionMapper;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

it('returns a ExceptionMapper from fromThrowable method', function (): void {
    $instance = ExceptionMapper::fromThrowable(new \InvalidArgumentException('Some exception message'));

    expect($instance)->toBeInstanceOf(ExceptionMapper::class);
});

it('converts ModelNotFoundException into NotFoundHttpException', function (): void {
    $mapper = ExceptionMapper::fromThrowable(new ModelNotFoundException('Some exception message'));
    /** @var NotFoundHttpException $map */
    $map = $mapper->map();

    expect($map)->toBeInstanceOf(NotFoundHttpException::class);
    expect($map->getStatusCode())->toBe(404);
    expect($map->getMessage())->toBe('Some exception message');
});

it('converts AuthorizationException into HttpException', function (): void {
    $mapper = ExceptionMapper::fromThrowable(new AuthorizationException('Some exception message'));
    /** @var HttpException $map */
    $map = $mapper->map();

    expect($mapper->map())->toBeInstanceOf(HttpException::class);
    expect($map->getStatusCode())->toBe(403);
    expect($map->getMessage())->toBe('Some exception message');
});

it('converts AuthenticationException into HttpException', function (): void {
    $mapper = ExceptionMapper::fromThrowable(new AuthenticationException('Some exception message'));
    /** @var HttpException $map */
    $map = $mapper->map();

    expect($mapper->map())->toBeInstanceOf(HttpException::class);
    expect($map->getStatusCode())->toBe(401);
    expect($map->getMessage())->toBe('Some exception message');
});
