<?php

declare(strict_types=1);

use Illuminate\Support\Str;

use function Pest\Laravel\getJson;

it('returns a json response when a route is not found', function (): void {
    $response = getJson('/' . Str::random(6));

    $response->assertStatus(404);
    $response->assertJsonStructure();
});
