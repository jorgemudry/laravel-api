<?php

declare(strict_types=1);

use App\Http\Controllers\ServiceAliveController;
use App\Http\Controllers\ServiceReadyController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/', function () {
    /** @var Application $app */
    $app = app();

    return $app->version();
});
Route::get('/validation', function (Request $request): void {
    $request->validate([
        'email' => [
            'required',
            'email',
        ],
        'password' => [
            'required',
            'string',
            'min:8',
        ],
    ]);
});
Route::get('/service/ready', ServiceReadyController::class);
Route::get('/service/alive', ServiceAliveController::class);

Route::middleware('auth:sanctum')->get('/users', fn (Request $request) => $request->user());
