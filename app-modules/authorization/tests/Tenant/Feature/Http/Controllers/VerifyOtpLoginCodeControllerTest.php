<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor's trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\Authorization\Models\OtpLoginCode;
use Filament\Facades\Filament;

use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\post;

it('rejects an OTP code that is older than 20 minutes', function () {
    $code = '123456';

    $otpCode = OtpLoginCode::factory()->withCode($code)->create([
        'created_at' => now()->subMinutes(21),
    ]);

    $panel = Filament::getPanel('admin');

    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => $code,
    ])
        ->assertForbidden();

    assertGuest($panel->getAuthGuard());
});

it('rejects an OTP code that has already been used', function () {
    $code = '123456';

    $otpCode = OtpLoginCode::factory()->withCode($code)->used()->create();

    $panel = Filament::getPanel('admin');

    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => $code,
    ])
        ->assertForbidden();

    assertGuest($panel->getAuthGuard());
});

it('requires the code field', function () {
    $otpCode = OtpLoginCode::factory()->create();

    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => null,
    ])
        ->assertInvalid(['code' => 'required']);
});

it('requires the code to be exactly 6 digits', function (string $code, string $error) {
    $otpCode = OtpLoginCode::factory()->create();

    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => $code,
    ])
        ->assertInvalid(['code' => $error]);
})
    ->with([
        'too short' => ['12345', 'digits'],
        'too long' => ['1234567', 'digits'],
        'non-numeric' => ['abcdef', 'digits'],
    ]);

it('returns an error when the OTP code is incorrect', function () {
    $code = '123456';

    $otpCode = OtpLoginCode::factory()->withCode($code)->create();

    $panel = Filament::getPanel('admin');

    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => '654321',
    ])
        ->assertRedirect()
        ->assertSessionHasErrors(['code' => 'The OTP code you entered is incorrect. Please try again.']);

    assertGuest($panel->getAuthGuard());

    $otpCode->refresh();

    expect($otpCode->used_at)->toBeNull();
});

it('logs in the user and redirects to the admin panel home with a valid code', function () {
    $code = '123456';

    $otpCode = OtpLoginCode::factory()->withCode($code)->create();

    $panel = Filament::getPanel('admin');

    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => $code,
    ])
        ->assertRedirect($panel->getHomeUrl());

    assertAuthenticatedAs($otpCode->user, $panel->getAuthGuard());

    $otpCode->refresh();

    expect($otpCode->used_at)->not->toBeNull();
});

it('marks the OTP code as used after successful verification', function () {
    $code = '999999';

    $otpCode = OtpLoginCode::factory()->withCode($code)->create();

    expect($otpCode->used_at)->toBeNull();

    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => $code,
    ])
        ->assertRedirect();

    $otpCode->refresh();

    expect($otpCode->used_at)->not->toBeNull();
});

it('prevents reuse of an OTP code after successful verification', function () {
    $code = '123456';

    $otpCode = OtpLoginCode::factory()->withCode($code)->create();

    $panel = Filament::getPanel('admin');

    // First verification should succeed
    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => $code,
    ])
        ->assertRedirect($panel->getHomeUrl());

    assertAuthenticatedAs($otpCode->user, $panel->getAuthGuard());

    // Log out to test reuse
    auth()->guard($panel->getAuthGuard())->logout();

    // Second verification should be rejected
    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => $code,
    ])
        ->assertForbidden();

    assertGuest($panel->getAuthGuard());
});
