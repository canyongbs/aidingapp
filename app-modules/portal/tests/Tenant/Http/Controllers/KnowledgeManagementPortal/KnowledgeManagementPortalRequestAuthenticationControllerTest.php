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

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\Portal\Models\PortalAuthentication;
use AidingApp\Portal\Notifications\AuthenticatePortalNotification;
use AidingApp\Portal\Settings\PortalSettings;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\postJson;

beforeEach(function () {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->save();
});

test('it sends authentication for an existing contact', function () {
    Notification::fake();

    $contact = Contact::factory()->create([
        'email' => 'contact@example.com',
    ]);

    $url = URL::signedRoute(name: 'api.portal.request-authentication', absolute: false);

    $response = postJson($url, [
        'email' => $contact->email,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', "We've sent an authentication code to {$contact->email}.")
        ->assertJsonStructure([
            'message',
            'authentication_url',
        ]);

    $authentication = PortalAuthentication::query()->latest('id')->first();

    expect($authentication)
        ->not->toBeNull()
        ->and($authentication?->educatable_id)->toBe($contact->getKey());

    Notification::assertSentOnDemand(AuthenticatePortalNotification::class, function (AuthenticatePortalNotification $notification, array $channels, object $notifiable) use ($contact, $authentication) {
        $mailRoute = $notifiable->routes['mail'];

        // When a Contact exists, the mail route is ['email@example.com' => 'Display Name']
        $emailMatches = is_array($mailRoute)
            ? array_key_exists($contact->email, $mailRoute)
            : $mailRoute === $contact->email;

        return $notification->authentication->is($authentication) && $emailMatches;
    });
});

test('it allows registration flow when email domain matches an organization domain', function () {
    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'www.Example.com'],
        ],
    ]);

    $url = URL::signedRoute(name: 'api.portal.request-authentication', absolute: false);

    $response = postJson($url, [
        'email' => 'new.user@EXAMPLE.com',
    ]);

    $response
        ->assertStatus(404)
        ->assertJsonPath('registrationAllowed', true)
        ->assertJsonPath('message', "We've sent an authentication code to new.user@EXAMPLE.com.");

    expect($response->json('authentication_url'))->toContain('/api/portal/register/');

    $authentication = PortalAuthentication::query()->latest('id')->first();

    expect($authentication)
        ->not->toBeNull()
        ->and($authentication?->educatable_id)->toBeNull();
});

test('it generates spa registration url when requested for unknown contact', function () {
    Organization::factory()->create([
        'is_contact_generation_enabled' => true,
        'domains' => [
            ['domain' => 'example.com'],
        ],
    ]);

    $url = URL::signedRoute(name: 'api.portal.request-authentication', absolute: false);

    $response = postJson($url, [
        'email' => 'spa.user@example.com',
        'isSpa' => true,
    ]);

    $response
        ->assertStatus(404)
        ->assertJsonPath('registrationAllowed', true);

    expect($response->json('authentication_url'))->toContain('/portal/register/');
});

test('it returns validation error when contact and organization domain are not found', function () {
    $url = URL::signedRoute(name: 'api.portal.request-authentication', absolute: false);

    $response = postJson($url, [
        'email' => 'missing@unknown-domain.test',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
