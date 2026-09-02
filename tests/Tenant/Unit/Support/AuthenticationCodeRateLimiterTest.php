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
use App\Support\AuthenticationCodeRateLimiter;
use Illuminate\Validation\ValidationException;

it('allows the first code request and blocks a rapid second request for the same target', function () {
    $rateLimiter = new AuthenticationCodeRateLimiter();

    $user = User::factory()->create();

    $rateLimiter->ensureCanRequestCode($user, 'otp-login');
    $rateLimiter->recordCodeRequest($user, 'otp-login');

    expect(fn () => $rateLimiter->ensureCanRequestCode($user, 'otp-login'))
        ->toThrow(ValidationException::class);
});

it('scopes the code-request cooldown per target', function () {
    $rateLimiter = new AuthenticationCodeRateLimiter();

    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $rateLimiter->recordCodeRequest($user, 'otp-login');

    // Same person, different scope: not throttled.
    $rateLimiter->ensureCanRequestCode($user, 'km-portal');

    // Different person, same scope: not throttled.
    $rateLimiter->ensureCanRequestCode($otherUser, 'otp-login');
})->throwsNoExceptions();
