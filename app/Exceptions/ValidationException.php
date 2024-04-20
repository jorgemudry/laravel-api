<?php

declare(strict_types=1);

namespace App\Exceptions;

use JsonException;
use Throwable;

class ValidationException extends ApiException
{
    /**
     * @param array<string, string> $errors
     * @param array<string, string> $headers
     *
     * @throws JsonException
     */
    public function __construct(
        array $errors,
        ?Throwable $previous = null,
        array $headers = [],
        int $code = 0,
    ) {
        $message = strval(json_encode([
            'message' => 'Some fields failed to pass validation.',
            'fields' => $errors,
        ], JSON_THROW_ON_ERROR));
        $headers = [...$headers, 'X-Status-Reason' => 'Validation failed.'];

        parent::__construct(422, $message, $previous, $headers, $code);
    }
}
