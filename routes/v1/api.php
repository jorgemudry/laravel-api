<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('service')->as('service:')->group(
    base_path('routes/v1/service.php'),
);

Route::prefix('metrics')->as('metrics:')->group(
    base_path('routes/v1/metrics.php'),
);
