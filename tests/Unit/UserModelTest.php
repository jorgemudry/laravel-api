<?php

declare(strict_types=1);

use App\Models\User;

it('has correct fillable attributes', function (): void {
    $user = new User();

    expect($user->getFillable())->toBe(['name', 'email', 'password']);
});

it('has correct hidden attributes', function (): void {
    $user = new User();

    expect($user->getHidden())->toBe(['password', 'remember_token']);
});

it('creates user via factory', function (): void {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBeString();
    expect($user->email)->toBeString();
});

it('hashes the password automatically', function (): void {
    $user = User::factory()->create(['password' => 'secret123']);

    expect($user->password)->not->toBe('secret123');
    expect(password_verify('secret123', $user->password))->toBeTrue();
});
