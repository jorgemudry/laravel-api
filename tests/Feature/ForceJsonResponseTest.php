<?php

declare(strict_types=1);

it('returns json response without accept header', function (): void {
    $this->get('/v1/service/alive')
        ->assertHeader('Content-Type', 'application/json');
});

it('overrides accept header to json', function (): void {
    $this->get('/v1/service/alive', ['Accept' => 'text/html'])
        ->assertHeader('Content-Type', 'application/json');
});
