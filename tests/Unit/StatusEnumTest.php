<?php

declare(strict_types=1);

use App\Contracts\ToArrayEnum;
use App\Enums\Status;

it('has active and inactive cases', function (): void {
    expect(Status::ACTIVE->value)->toBe('active');
    expect(Status::INACTIVE->value)->toBe('inactive');
});

it('converts to array with all values', function (): void {
    expect(Status::toArray())->toBe(['active', 'inactive']);
});

it('implements ToArrayEnum interface', function (): void {
    expect(Status::ACTIVE)->toBeInstanceOf(ToArrayEnum::class);
});
