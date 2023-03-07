<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function (): void {
            Route::middleware('api')->group(base_path('routes/api.php'));
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request): Limit {
            /**
             * @var User|null $user
             */
            $user = $request->user();
            $by = $user === null ? $request->ip() : $user->id;

            return Limit::perMinute(60)->by($by);
        });
    }
}
