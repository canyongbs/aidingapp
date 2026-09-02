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

use AidingApp\Contact\Models\Contact;
use AidingApp\Portal\Enums\PortalType;
use AidingApp\Portal\Models\PortalAuthentication;
use AidingApp\Portal\Settings\PortalSettings;
use App\Support\AuthenticationCodeRateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;

use function Pest\Laravel\postJson;

beforeEach(function () {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->ai_support_assistant = true;
    $settings->save();
});

function assistantAuthenticate(PortalAuthentication $authentication, array $body): TestResponse
{
    $url = URL::signedRoute(
        name: 'widgets.assistant.api.authenticate',
        parameters: ['authentication' => $authentication],
        absolute: false,
    );

    return postJson($url, $body, ['Origin' => config('app.url')]);
}

test('it authenticates the contact when the code is valid', function () {
    $contact = Contact::factory()->create();

    $plainCode = 123456;

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make($plainCode),
        'created_at' => now(),
    ]);
    $authentication->educatable()->associate($contact);
    $authentication->save();

    $response = assistantAuthenticate($authentication, ['code' => $plainCode]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true);

    expect($response->json('token'))->not->toBeEmpty()
        ->and(auth('contact')->check())->toBeTrue();
});

test('it returns a validation error when the code is invalid', function () {
    $contact = Contact::factory()->create();

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make(112233),
        'created_at' => now(),
    ]);
    $authentication->educatable()->associate($contact);
    $authentication->save();

    assistantAuthenticate($authentication, ['code' => 999999])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['code']);

    expect(auth('contact')->check())->toBeFalse();
});

test('it returns expired when the authentication has expired', function () {
    $contact = Contact::factory()->create();

    $plainCode = 445566;

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make($plainCode),
        'created_at' => now()->subDay()->subMinute(),
    ]);
    $authentication->educatable()->associate($contact);
    $authentication->save();

    assistantAuthenticate($authentication, ['code' => $plainCode])
        ->assertStatus(422)
        ->assertJsonPath('is_expired', true);

    expect(auth('contact')->check())->toBeFalse();
});

test('it locks the authentication after too many invalid code attempts and rejects even the correct code', function () {
    $contact = Contact::factory()->create();

    $plainCode = 778899;

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make($plainCode),
        'created_at' => now(),
    ]);
    $authentication->educatable()->associate($contact);
    $authentication->save();

    foreach (range(1, AuthenticationCodeRateLimiter::MAX_ATTEMPTS) as $attempt) {
        assistantAuthenticate($authentication, ['code' => 999999])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['code' => 'The provided code is invalid.']);
    }

    assistantAuthenticate($authentication, ['code' => $plainCode])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['code' => 'Too many invalid attempts. Please request a new code.']);

    expect(auth('contact')->check())->toBeFalse();
});
