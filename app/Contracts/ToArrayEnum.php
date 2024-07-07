<?php

declare(strict_types=1);

namespace App\Contracts;

interface ToArrayEnum
{
    /**
     * @return array<int, mixed>
     */
    public static function toArray(): array;
}
