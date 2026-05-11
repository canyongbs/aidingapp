<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Enums\PresenceStatus;
use App\Models\User;
use App\Settings\PresenceSettings;

it('returns Active when last activity is within active threshold', function () {
    $settings = app(PresenceSettings::class);

    $user = User::factory()->create([
        'last_activity_at' => now()->subMinutes($settings->active_threshold - 1),
    ]);

    expect($user->presenceStatus())->toBe(PresenceStatus::Active);
});

it('returns Idle when last activity is between active and idle thresholds', function () {
    $settings = app(PresenceSettings::class);

    $user = User::factory()->create([
        'last_activity_at' => now()->subMinutes($settings->active_threshold + 1),
    ]);

    expect($user->presenceStatus())->toBe(PresenceStatus::Idle);
});

it('returns Inactive when last activity is between idle and inactive thresholds', function () {
    $settings = app(PresenceSettings::class);

    $user = User::factory()->create([
        'last_activity_at' => now()->subMinutes($settings->idle_threshold + 1),
    ]);

    expect($user->presenceStatus())->toBe(PresenceStatus::Inactive);
});

it('returns Offline when last activity exceeds inactive threshold', function () {
    $settings = app(PresenceSettings::class);

    $user = User::factory()->create([
        'last_activity_at' => now()->subMinutes($settings->inactive_threshold + 1),
    ]);

    expect($user->presenceStatus())->toBe(PresenceStatus::Offline);
});

it('returns Offline when last_activity_at is null', function () {
    $user = User::factory()->create([
        'last_activity_at' => null,
    ]);

    expect($user->presenceStatus())->toBe(PresenceStatus::Offline);
});

it('returns Active at exactly the boundary', function () {
    $settings = app(PresenceSettings::class);

    $user = User::factory()->create([
        'last_activity_at' => now(),
    ]);

    expect($user->presenceStatus())->toBe(PresenceStatus::Active);
});
