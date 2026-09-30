<?php

use App\Models\User;
use Illuminate\Auth\Events\Login;

it('has no last logged in timestamp by default', function () {
    $user = User::factory()->create();

    expect($user->last_logged_in_at)->toBeNull();
});

it('records the last logged in timestamp when a user logs in', function () {
    $user = User::factory()->create();

    event(new Login('web', $user, false));

    expect($user->refresh()->last_logged_in_at)->not->toBeNull();
});

it('does not change updated_at when recording a login', function () {
    $user = User::factory()->create(['updated_at' => now()->subDay()]);
    $updatedAt = $user->updated_at->clone();

    event(new Login('web', $user, false));

    expect($user->refresh()->updated_at->eq($updatedAt))->toBeTrue();
});
