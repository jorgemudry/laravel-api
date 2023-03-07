<?php

declare(strict_types=1);

use App\Http\Controllers\V1\ServiceAliveController;
use App\Http\Controllers\V1\ServiceReadyController;
use Illuminate\Support\Facades\Route;

Route::get('/ready', ServiceReadyController::class)->name('ready');
Route::get('/alive', ServiceAliveController::class)->name('alive');
