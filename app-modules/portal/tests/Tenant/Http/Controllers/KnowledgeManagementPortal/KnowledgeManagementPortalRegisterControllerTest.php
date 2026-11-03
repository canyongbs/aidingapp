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
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Models\Organization;
use AidingApp\Portal\Enums\PortalType;
use AidingApp\Portal\Models\PortalAuthentication;
use AidingApp\Portal\Settings\PortalSettings;
use App\Features\ContactArchivingFeature;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\PersonalAccessToken;

use function Pest\Laravel\postJson;
use function Pest\Laravel\withHeader;
use function Pest\Laravel\withoutExceptionHandling;

beforeEach(function (): void {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->save();
});

test('it registers a new portal contact for matching organization domain', function (): void {
    ContactType::factory()->create([
        'is_default' => true,
    ]);

    $organization = Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'example.com'],
        ],
    ]);

    $plainCode = 123456;

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make((string) $plainCode),
        'created_at' => now(),
    ]);

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $authentication, 'email' => 'new.contact@example.com'],
        absolute: false,
    );

    $response = postJson($url, [
        'email' => 'new.contact@example.com',
        'first_name' => 'New',
        'last_name' => 'Contact',
        'preferred' => 'Preferred Name',
        'mobile' => '+15550001111',
        'phone' => '+15550002222',
        'code' => $plainCode,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('user.email', 'new.contact@example.com');

    $contact = Contact::query()->where('email', 'new.contact@example.com')->first();

    expect($contact)
        ->not()->toBeNull()
        ->and($contact?->full_name)->toBe('New Contact')
        ->and($contact?->organization_id)->toBe($organization->id);

    expect($response->json('token'))->not()->toBeEmpty();
});

it('unarchives and updates an archived contact with the same email', function (): void {
    ContactArchivingFeature::activate();

    ContactType::factory()->create([
        'is_default' => true,
    ]);

    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'example.com'],
        ],
    ]);

    $originalType = ContactType::factory()->create();

    $archivedContact = Contact::factory()->archived()->create([
        'email' => 'archived.contact@example.com',
        'type_id' => $originalType->getKey(),
    ]);

    $plainCode = 123456;

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make((string) $plainCode),
        'created_at' => now(),
    ]);

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $authentication, 'email' => 'archived.contact@example.com'],
        absolute: false,
    );

    $contactCount = Contact::query()->count();

    $response = postJson($url, [
        'email' => 'archived.contact@example.com',
        'first_name' => 'Returning',
        'last_name' => 'Contact',
        'mobile' => '+15550008888',
        'code' => $plainCode,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('user.id', $archivedContact->getKey());

    $archivedContact->refresh();

    expect(Contact::query()->count())->toBe($contactCount)
        ->and($archivedContact->isArchived())->toBeFalse()
        ->and($archivedContact->first_name)->toBe('Returning')
        ->and($archivedContact->last_name)->toBe('Contact')
        ->and($archivedContact->full_name)->toBe('Returning Contact')
        ->and($archivedContact->mobile)->toBe('+15550008888')
        ->and($archivedContact->type_id)->toBe($originalType->getKey())
        ->and($archivedContact->audits()->where('event', 'unarchived')->exists())->toBeTrue()
        ->and(PersonalAccessToken::findToken($response->json('token'))?->tokenable?->is($archivedContact))->toBeTrue();
});

it('fails to register with an archived contact\'s email when the contact archiving feature is inactive', function (): void {
    ContactArchivingFeature::deactivate();

    ContactType::factory()->create([
        'is_default' => true,
    ]);

    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'example.com'],
        ],
    ]);

    $archivedContact = Contact::factory()->archived()->create([
        'email' => 'archived.contact@example.com',
        'first_name' => 'Original',
    ]);

    $plainCode = 123456;

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make((string) $plainCode),
        'created_at' => now(),
    ]);

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $authentication, 'email' => 'archived.contact@example.com'],
        absolute: false,
    );

    withoutExceptionHandling();

    // A savepoint keeps the test transaction usable after the expected constraint violation.
    expect(fn () => DB::connection('tenant')->transaction(fn () => postJson($url, [
        'email' => 'archived.contact@example.com',
        'first_name' => 'Returning',
        'last_name' => 'Contact',
        'mobile' => '+15550008888',
        'code' => $plainCode,
    ])))->toThrow(UniqueConstraintViolationException::class);

    $archivedContact->refresh();

    expect($archivedContact->isArchived())->toBeTrue()
        ->and($archivedContact->first_name)->toBe('Original');
});

test('it does not rotate the csrf token shared with the admin panel', function (): void {
    ContactType::factory()->create([
        'is_default' => true,
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
        'code' => Hash::make((string) $plainCode),
        'created_at' => now(),
    ]);

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $authentication, 'email' => 'new.contact@example.com'],
        absolute: false,
    );

    $host = parse_url(route('api.portal.define'), PHP_URL_HOST);

    config(['sanctum.stateful' => [$host]]);

    session()->start();

    $token = session()->token();

    expect($token)->not()->toBeEmpty();

    withHeader('Referer', 'https://' . $host)
        ->postJson($url, [
            'email' => 'new.contact@example.com',
            'first_name' => 'New',
            'last_name' => 'Contact',
            'mobile' => '+15550001111',
            'code' => $plainCode,
        ])
        ->assertOk();

    expect(session()->token())->toBe($token)
        ->and(auth('contact')->check())->toBeTrue();
});

test('it returns expired flag when portal authentication is expired', function (): void {
    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'test.example.com'],
        ],
    ]);

    $plainCode = 222222;

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make((string) $plainCode),
        'created_at' => now()->subDay()->subMinute(),
    ]);

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $authentication, 'email' => 'expired.user@test.example.com'],
        absolute: false,
    );

    $response = postJson($url, [
        'email' => 'expired.user@test.example.com',
        'first_name' => 'Expired',
        'last_name' => 'User',
        'mobile' => '+15550003333',
        'code' => $plainCode,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('is_expired', true);

    expect(Contact::query()->where('email', 'expired.user@test.example.com')->exists())->toBeFalse();
});

test('it forbids registration when email domain does not match an enabled organization', function (): void {
    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'allowed.com'],
        ],
    ]);

    $plainCode = 333333;

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make((string) $plainCode),
        'created_at' => now(),
    ]);

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $authentication, 'email' => 'user@not-allowed.com'],
        absolute: false,
    );

    $response = postJson($url, [
        'email' => 'user@not-allowed.com',
        'first_name' => 'No',
        'last_name' => 'Match',
        'mobile' => '+15550004444',
        'code' => $plainCode,
    ]);

    $response->assertForbidden();
});

test('it normalizes stored www domain and allows only base-domain email registration', function (): void {
    ContactType::factory()->create([
        'is_default' => true,
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
        'code' => Hash::make((string) $allowedCode),
        'created_at' => now(),
    ]);

    $allowedUrl = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $allowedAuthentication, 'email' => 'myemail@example.com'],
        absolute: false,
    );

    $allowedResponse = postJson($allowedUrl, [
        'email' => 'myemail@example.com',
        'first_name' => 'My',
        'last_name' => 'Email',
        'mobile' => '+15550006666',
        'code' => $allowedCode,
    ]);

    $allowedResponse
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('user.email', 'myemail@example.com');

    $blockedCode = 876543;

    $blockedAuthentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make((string) $blockedCode),
        'created_at' => now(),
    ]);

    $blockedUrl = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $blockedAuthentication, 'email' => 'myemail@www.example.com'],
        absolute: false,
    );

    $blockedResponse = postJson($blockedUrl, [
        'email' => 'myemail@www.example.com',
        'first_name' => 'My',
        'last_name' => 'Email',
        'mobile' => '+15550007777',
        'code' => $blockedCode,
    ]);

    $blockedResponse->assertForbidden();
});

test('it returns validation error when code is invalid', function (): void {
    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'example.com'],
        ],
    ]);

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make((string) 444444),
        'created_at' => now(),
    ]);

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $authentication, 'email' => 'new.user@example.com'],
        absolute: false,
    );

    $response = postJson($url, [
        'email' => 'new.user@example.com',
        'first_name' => 'New',
        'last_name' => 'User',
        'mobile' => '+15550005555',
        'code' => 111111,
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['code']);
});

it('does not register with an email other than the one the code was sent to', function (array $signedParameters): void {
    ContactArchivingFeature::activate();

    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'example.com'],
        ],
    ]);

    $archivedContact = Contact::factory()->archived()->create([
        'email' => 'archived.contact@example.com',
        'first_name' => 'Original',
    ]);

    $authentication = PortalAuthentication::factory()->create([
        'portal_type' => PortalType::KnowledgeManagement,
        'code' => Hash::make((string) 123456),
        'created_at' => now(),
    ]);

    $url = URL::signedRoute(
        name: 'api.portal.authenticate.register.embedded',
        parameters: ['authentication' => $authentication, ...$signedParameters],
        absolute: false,
    );

    $contactCount = Contact::query()->count();

    postJson($url, [
        'email' => 'archived.contact@example.com',
        'first_name' => 'Someone',
        'last_name' => 'Else',
        'mobile' => '+15550009999',
        'code' => 123456,
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email' => 'The email must match the address the code was sent to.']);

    $archivedContact->refresh();

    expect(Contact::query()->count())->toBe($contactCount)
        ->and($archivedContact->isArchived())->toBeTrue()
        ->and($archivedContact->first_name)->toBe('Original');
})->with([
    'a different email' => [['email' => 'requester@example.com']],
    'no email' => [[]],
]);

test('it generates registration url for unknown contact when organization domain matches', function (): void {
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

    expect($authentication)->not()->toBeNull()
        ->and($authentication?->portal_type)->toBe(PortalType::KnowledgeManagement)
        ->and($authentication?->educatable_id)->toBeNull();
});

test('it generates spa registration url for unknown contact when organization domain matches', function (): void {
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

    expect($authentication)->not()->toBeNull()
        ->and($authentication?->portal_type)->toBe(PortalType::KnowledgeManagement)
        ->and($authentication?->educatable_id)->toBeNull();
});
