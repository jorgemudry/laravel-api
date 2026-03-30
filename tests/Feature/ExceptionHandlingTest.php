<?php

declare(strict_types=1);

it('returns json for 404 errors', function (): void {
    $this->get('/v1/non-existent-route')
        ->assertStatus(404)
        ->assertJsonStructure(['status', 'type', 'error']);
});

it('returns error message for 404 errors', function (): void {
    $response = $this->get('/v1/non-existent-route');
    $json = $response->json();

    expect($json['status'])->toBe(404);
    expect($json['error'])->toBeString()->not->toBeEmpty();
});

it('does not include debug info when debug is disabled', function (): void {
    config(['app.debug' => false]);

    $response = $this->get('/v1/non-existent-route');
    $json = $response->json();

    expect($json)->not->toHaveKeys(['file', 'line', 'trace']);
});

it('includes debug info when debug is enabled', function (): void {
    config(['app.debug' => true]);

    $response = $this->get('/v1/non-existent-route');
    $json = $response->json();

    expect($json)->toHaveKeys(['file', 'line', 'trace']);
});

it('returns json even without accept header', function (): void {
    $this->get('/v1/non-existent-route', ['Accept' => 'text/html'])
        ->assertHeader('Content-Type', 'application/json')
        ->assertStatus(404);
});

it('returns no web routes', function (): void {
    $this->get('/')
        ->assertStatus(404);
});
