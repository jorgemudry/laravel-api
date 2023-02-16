<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use ReflectionClass;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        // AuthorizationException::class,
        // ModelNotFoundException::class,
        // ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, Request $request) {
            $instanceof = get_class($e);

            dd($instanceof);
            switch ($instanceof) {
                case ModelNotFoundException::class:
                    $e = new NotFoundHttpException($e->getMessage(), $e);
                    break;
                case AuthorizationException::class:
                    $e = new HttpException(403, $e->getMessage());
                    break;
            }

            $fe = FlattenException::createFromThrowable($e);
            $message = $fe->getMessage();

            if (empty($message)) {
                $fe->setMessage('Whoops, looks like something went wrong.');
            }

            $response = [
                'metadata' => [
                    'code' => $fe->getStatusCode(),
                    'message' => Response::$statusTexts[$fe->getStatusCode()],
                ],
                'error' => [
                    'message' => $fe->getMessage(),
                    'type' => (new ReflectionClass($fe->getClass()))->getShortName(),
                ],
            ];

            if (config('app.debug')) {
                $response['error']['file'] = $fe->getFile();
                $response['error']['line'] = $fe->getLine();
                $response['error']['trace'] = $fe->getTrace();
            }

            return new JsonResponse($response, $response['metadata']['code'], $fe->getHeaders());
        });
    }
}
