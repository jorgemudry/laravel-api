<?php

declare(strict_types=1);

namespace App\Prometheus;

use Illuminate\Support\Facades\Facade;
use Prometheus\CollectorRegistry;

/**
 * @mixin CollectorRegistry
 */
class Prom extends Facade
{
    public static function fake(): void
    {
        // static::swap(new Fake());
    }

    protected static function getFacadeAccessor(): string
    {
        return CollectorRegistry::class;
    }
}
