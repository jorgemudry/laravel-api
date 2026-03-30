<?php

declare(strict_types=1);

use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;

it('returns 200 status', function (): void {
    $this->app->instance(CollectorRegistry::class, new CollectorRegistry(new InMemory()));

    $this->get('/v1/metrics/')
        ->assertStatus(200);
});

it('returns prometheus text format content type', function (): void {
    $this->app->instance(CollectorRegistry::class, new CollectorRegistry(new InMemory()));

    $response = $this->get('/v1/metrics/');
    $content_type = $response->headers->get('Content-Type');

    expect($content_type)->toContain('text/plain');
});
