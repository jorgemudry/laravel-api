<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis;

class PrometheusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(
            CollectorRegistry::class,
            function () {
                Redis::setDefaultOptions(
                    Arr::only(
                        config('database.redis.default'),
                        ['host', 'port', 'password']
                    )
                );

                return CollectorRegistry::getDefault();
            }
        );
    }
}
