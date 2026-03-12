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
      of the licensor in the software. Any use of the licensor’s trademarks is subject
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

use AidingApp\Contact\Enums\SystemContactClassification;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Models\Organization;
use AidingApp\Portal\Enums\PortalType;
use AidingApp\Portal\Models\PortalAuthentication;
use AidingApp\Portal\Settings\PortalSettings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\postJson;

beforeEach(function () {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->save();
});

test('it registers a new portal contact for matching organization domain', function () {
    ContactType::factory()->create([
        'classification' => SystemContactClassification::New,
    ]);

    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'example.com'],
        ],
    ]);

    $plainCode = 123456;

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make($plainCode),
        'created_at' => now(),
    ]);

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $authentication],
        absolute: false,
    );

    $response = postJson($url, [
        'email' => 'new.contact@example.com',
        'first_name' => 'New',
        'last_name' => 'Contact',
        'preferred' => 'Preferred Name',
        'mobile' => '+15550001111',
        'phone' => '+15550002222',
        'sms_opt_out' => false,
        'code' => $plainCode,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('user.email', 'new.contact@example.com');

    $contact = Contact::query()->where('email', 'new.contact@example.com')->first();

    expect($contact)
        ->not->toBeNull()
        ->and($contact?->full_name)->toBe('New Contact');

    expect($response->json('token'))->not->toBeEmpty();
});

test('it returns expired flag when portal authentication is expired', function () {
    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'example.com'],
        ],
    ]);

    $plainCode = 222222;

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make($plainCode),
        'created_at' => now()->subDay()->subMinute(),
    ]);

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $authentication],
        absolute: false,
    );

    $response = postJson($url, [
        'email' => 'expired.user@example.com',
        'first_name' => 'Expired',
        'last_name' => 'User',
        'mobile' => '+15550003333',
        'sms_opt_out' => true,
        'code' => $plainCode,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('is_expired', true);

    expect(Contact::query()->where('email', 'expired.user@example.com')->exists())->toBeFalse();
});

test('it forbids registration when email domain does not match an enabled organization', function () {
    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'allowed.com'],
        ],
    ]);

    $plainCode = 333333;

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make($plainCode),
        'created_at' => now(),
    ]);

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $authentication],
        absolute: false,
    );

    $response = postJson($url, [
        'email' => 'user@not-allowed.com',
        'first_name' => 'No',
        'last_name' => 'Match',
        'mobile' => '+15550004444',
        'sms_opt_out' => false,
        'code' => $plainCode,
    ]);

    $response->assertForbidden();
});

test('it normalizes stored www domain and allows only base-domain email registration', function () {
    ContactType::factory()->create([
        'classification' => SystemContactClassification::New,
    ]);

    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'www.example.com'],
        ],
    ]);

    $allowedCode = 987654;

    $allowedAuthentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make($allowedCode),
        'created_at' => now(),
    ]);

    $allowedUrl = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $allowedAuthentication],
        absolute: false,
    );

    $allowedResponse = postJson($allowedUrl, [
        'email' => 'myemail@example.com',
        'first_name' => 'My',
        'last_name' => 'Email',
        'mobile' => '+15550006666',
        'sms_opt_out' => false,
        'code' => $allowedCode,
    ]);

    $allowedResponse
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('user.email', 'myemail@example.com');

    $blockedCode = 876543;

    $blockedAuthentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make($blockedCode),
        'created_at' => now(),
    ]);

    $blockedUrl = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $blockedAuthentication],
        absolute: false,
    );

    $blockedResponse = postJson($blockedUrl, [
        'email' => 'myemail@www.example.com',
        'first_name' => 'My',
        'last_name' => 'Email',
        'mobile' => '+15550007777',
        'sms_opt_out' => false,
        'code' => $blockedCode,
    ]);

    $blockedResponse->assertForbidden();
});

test('it returns validation error when code is invalid', function () {
    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'example.com'],
        ],
    ]);

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make(444444),
        'created_at' => now(),
    ]);

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $authentication],
        absolute: false,
    );

    $response = postJson($url, [
        'email' => 'new.user@example.com',
        'first_name' => 'New',
        'last_name' => 'User',
        'mobile' => '+15550005555',
        'sms_opt_out' => false,
        'code' => 111111,
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['code']);
});

test('it generates registration url for unknown contact when organization domain matches', function () {
    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'example.com'],
        ],
    ]);

    $url = URL::signedRoute(name: 'api.portal.request-authentication', absolute: false);

    $response = postJson($url, [
        'email' => 'new.user@example.com',
    ]);

    $response
        ->assertStatus(404)
        ->assertJsonPath('registrationAllowed', true)
        ->assertJsonPath('message', "We've sent an authentication code to new.user@example.com.");

    expect($response->json('authentication_url'))->toContain('/api/portal/register/');

    $authentication = PortalAuthentication::query()->latest('id')->first();

    expect($authentication)->not->toBeNull()
        ->and($authentication?->portal_type)->toBe(PortalType::KnowledgeManagement)
        ->and($authentication?->educatable_id)->toBeNull();
});

test('it generates spa registration url for unknown contact when organization domain matches', function () {
    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'example.com'],
        ],
    ]);

    $url = URL::signedRoute(name: 'api.portal.request-authentication', absolute: false);

    $response = postJson($url, [
        'email' => 'new.user@example.com',
    ]);

    $response
        ->assertStatus(404)
        ->assertJsonPath('registrationAllowed', true)
        ->assertJsonPath('message', "We've sent an authentication code to new.user@example.com.");

    expect($response->json('authentication_url'))->toContain('/api/portal/register/');

    $authentication = PortalAuthentication::query()->latest('id')->first();

    expect($authentication)->not->toBeNull()
        ->and($authentication?->portal_type)->toBe(PortalType::KnowledgeManagement)
        ->and($authentication?->educatable_id)->toBeNull();
});
