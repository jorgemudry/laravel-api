<?php

declare(strict_types=1);

use App\Models\UnGuardedModel;

it('has empty guarded array for mass assignment', function (): void {
    $model = new UnGuardedModel();

    expect($model->getGuarded())->toBe([]);
});

it('uses ulid primary keys', function (): void {
    $model = new UnGuardedModel();

    expect($model->newUniqueId())->toBeString();
    expect(mb_strlen($model->newUniqueId()))->toBe(26);
});
