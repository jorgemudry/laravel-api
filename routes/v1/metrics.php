<?php

declare(strict_types=1);

use App\Http\Controllers\V1\PrometheusMetricsController;
use Illuminate\Support\Facades\Route;

Route::get('/', PrometheusMetricsController::class);
