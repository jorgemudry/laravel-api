<?php

declare(strict_types=1);

it('has service alive route', function (): void {
    $this->get('/v1/service/alive')
        ->assertStatus(200);
});

it('has service ready route', function (): void {
    $response = $this->get('/v1/service/ready');

    // Route exists - returns 200 (all ok) or 503 (redis/db down)
    expect($response->status())->toBeIn([200, 503]);
});

it('has metrics route', function (): void {
    $route = app('router')->getRoutes()->getByAction('App\Http\Controllers\V1\PrometheusMetricsController');

    expect($route)->not->toBeNull();
});

it('does not have web root route', function (): void {
    $this->get('/')
        ->assertStatus(404);
});
