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
