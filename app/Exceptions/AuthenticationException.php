<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

class AuthenticationException extends ApiException
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        string $message = '',
        Throwable|null $previous = null,
        array $headers = [],
        int $code = 0,
    ) {
        parent::__construct(401, $message, $previous, $headers, $code);
    }
}
