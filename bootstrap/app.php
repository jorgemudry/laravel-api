<?php

declare(strict_types=1);

use App\Exceptions\ErrorResponseBuilder;
use App\Exceptions\ExceptionMapper;
use App\Exceptions\FlattenException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->group('web', []);
        $middleware->group('api', [
            App\Http\Middleware\ForceJsonResponse::class,
            Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            // \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class . ':api',
            Illuminate\Routing\Middleware\SubstituteBindings::class,
            // TreblleMiddleware::class,
            'cache.headers:public;max_age=2628000;etag',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn (Request $request, Throwable $e) => true);
        $exceptions->render(function (Throwable $e, Request $request) {
            $exception = FlattenException::createFromThrowable($e);
            $exception = ExceptionMapper::fromThrowable($e)->map($exception);
            $debug = boolval(config('app.debug'));

            return ErrorResponseBuilder::fromFlatten($exception)->build(debug: $debug);
        });
    })->create();
