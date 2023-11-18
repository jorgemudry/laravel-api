<?php

declare(strict_types=1);

namespace App\Traits;

trait HasToArrayEnum
{
    /**
     * @return array<int, mixed>
     */
    public static function toArray(): array
    {
        $cases = self::cases();
        $result = [];

        foreach ($cases as $case) {
            $result[] = $case->value;
        }

        return $result;
    }
}
