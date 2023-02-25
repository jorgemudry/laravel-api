<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

class ValidationException extends ApiException
{
    /**
     * @param array<string, string> $errors
     * @param array<string, string> $headers
     */
    public function __construct(
        array $errors,
        Throwable|null $previous = null,
        array $headers = [],
        int $code = 0,
    ) {
        $message = strval(json_encode([
            'message' => 'Some fields failed to pass validation.',
            'fields' => $errors,
        ]));
        $headers = array_merge($headers, ['X-Status-Reason' => 'Validation failed.']);

        parent::__construct(422, $message, $previous, $headers, $code);
    }
}
