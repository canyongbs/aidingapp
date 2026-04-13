<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
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

use AidingApp\Ai\Settings\AiSupportAssistantSettings;
use AidingApp\Contact\Models\Contact;
use AidingApp\Portal\Enums\PortalType;
use AidingApp\Portal\Models\PortalAuthentication;
use AidingApp\Portal\Models\PortalGuest;
use AidingApp\Portal\Settings\PortalSettings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\postJson;

beforeEach(function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->knowledge_management_portal_service_management = false;
    $portalSettings->ai_support_assistant = false;
    $portalSettings->save();

    $assistantSettings = app(AiSupportAssistantSettings::class);
    $assistantSettings->is_enabled = false;
    $assistantSettings->save();
});

test('it returns expired and creates guest session when authentication is expired', function () {
    $plainCode = 123456;

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make($plainCode),
        'created_at' => now()->subDay()->subMinute(),
    ]);

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.embedded',
        parameters: ['authentication' => $authentication],
        absolute: false,
    );

    $response = postJson($url, [
        'code' => $plainCode,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('is_expired', true)
        ->assertSessionHas('guest_id');

    expect(PortalGuest::query()->count())->toBe(1);
});

test('it reuses existing guest session for expired authentication', function () {
    $existingGuest = PortalGuest::factory()->create();

    $plainCode = 223344;

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make($plainCode),
        'created_at' => now()->subDay()->subMinute(),
    ]);

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.embedded',
        parameters: ['authentication' => $authentication],
        absolute: false,
    );

    session(['guest_id' => $existingGuest->getKey()]);

    $response = postJson($url, ['code' => $plainCode]);

    $response
        ->assertOk()
        ->assertJsonPath('is_expired', true)
        ->assertSessionHas('guest_id', $existingGuest->getKey());

    expect(PortalGuest::query()->count())->toBe(1);
});

test('it authenticates contact and clears guest session when code is valid', function () {
    $contact = Contact::factory()->create();
    $existingGuest = PortalGuest::factory()->create();

    $plainCode = 654321;

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make($plainCode),
        'created_at' => now(),
    ]);
    $authentication->educatable()->associate($contact);
    $authentication->save();

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.embedded',
        parameters: ['authentication' => $authentication],
        absolute: false,
    );

    session(['guest_id' => $existingGuest->getKey()]);

    $response = postJson($url, ['code' => $plainCode]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('user.id', $contact->getKey())
        ->assertJsonPath('assistant_enabled', false)
        ->assertJsonPath('assistant_widget_loader_url', null)
        ->assertJsonPath('assistant_widget_config_url', null)
        ->assertSessionMissing('guest_id');

    expect($response->json('token'))->not->toBeEmpty()
        ->and(auth('contact')->check())->toBeTrue();
});

test('it returns validation error when code is invalid', function () {
    $contact = Contact::factory()->create();

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make(112233),
        'created_at' => now(),
    ]);
    $authentication->educatable()->associate($contact);
    $authentication->save();

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.embedded',
        parameters: ['authentication' => $authentication],
        absolute: false,
    );

    $response = postJson($url, [
        'code' => 999999,
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['code']);
});

test('it returns validation error when code is missing', function () {
    $contact = Contact::factory()->create();

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make(445566),
        'created_at' => now(),
    ]);
    $authentication->educatable()->associate($contact);
    $authentication->save();

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.embedded',
        parameters: ['authentication' => $authentication],
        absolute: false,
    );

    $response = postJson($url, []);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['code']);
});
