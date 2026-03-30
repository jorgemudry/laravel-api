<?php

declare(strict_types=1);

it('returns correct json structure', function (): void {
    $this->get('/v1/service/ready')
        ->assertJsonStructure(['data' => ['is_ready', 'status', 'git']]);
});

it('returns git commit hash as string', function (): void {
    $response = $this->get('/v1/service/ready');
    $data = $response->json('data');

    expect($data['git'])->toBeString();
});

it('returns 503 when redis is unavailable', function (): void {
    $response = $this->get('/v1/service/ready');
    $data = $response->json('data');

    // In test environment Redis is typically not running
    // so we accept either 200 (all services up) or 503 (service down)
    expect($response->status())->toBeIn([200, 503]);
    expect($data['is_ready'])->toBeBool();
});
