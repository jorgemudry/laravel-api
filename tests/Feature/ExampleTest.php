<?php

declare(strict_types=1);

it('returns a successful response', function (): void {
    $response = $this->get('/v1/service/alive');

    $response->assertStatus(200);
});
