<?php

declare(strict_types=1);

it('returns 200 status code', function (): void {
    $this->get('/v1/service/alive')
        ->assertStatus(200);
});

it('returns correct json structure', function (): void {
    $this->get('/v1/service/alive')
        ->assertJsonStructure(['data' => ['is_alive', 'status']]);
});

it('returns is_alive as true', function (): void {
    $this->get('/v1/service/alive')
        ->assertJson(['data' => ['is_alive' => true, 'status' => 200]]);
});

it('returns json content type', function (): void {
    $this->get('/v1/service/alive')
        ->assertHeader('Content-Type', 'application/json');
});
