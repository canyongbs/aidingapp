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
use AidingApp\Portal\Models\PortalAuthentication;
use AidingApp\Portal\Notifications\AuthenticatePortalNotification;
use AidingApp\Portal\Settings\PortalSettings;
use App\Support\AuthenticationCodeRateLimiter;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;

use function Pest\Laravel\postJson;

beforeEach(function () {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->ai_support_assistant = true;
    $settings->save();
});

function requestAssistantAuthentication(array $body): TestResponse
{
    $url = URL::signedRoute(name: 'widgets.assistant.api.authenticate.request', absolute: false);

    return postJson($url, $body, ['Origin' => config('app.url')]);
}

test('it sends assistant authentication for an existing contact', function () {
    Notification::fake();

    $contact = Contact::factory()->create([
        'email' => 'contact@example.com',
    ]);

    $response = requestAssistantAuthentication(['email' => $contact->email]);

    $response
        ->assertOk()
        ->assertJsonPath('message', "We've sent an authentication code to {$contact->email}.");

    $authentication = PortalAuthentication::query()->latest('id')->first();

    expect($authentication?->educatable_id)->toBe($contact->getKey());

    Notification::assertSentOnDemand(AuthenticatePortalNotification::class);
});

test('it resolves an existing contact when the email case differs', function () {
    Notification::fake();

    $contact = Contact::factory()->create([
        'email' => 'Contact@Example.com',
    ]);

    $response = requestAssistantAuthentication(['email' => 'contact@example.com']);

    $response
        ->assertOk()
        ->assertJsonPath('message', "We've sent an authentication code to contact@example.com.");

    $authentication = PortalAuthentication::query()->latest('id')->first();

    expect($authentication?->educatable_id)->toBe($contact->getKey());
});

test('it returns a validation error when no contact matches the email', function () {
    $response = requestAssistantAuthentication(['email' => 'missing@example.com']);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('it throttles repeated assistant authentication requests for the same contact', function () {
    $contact = Contact::factory()->create([
        'email' => 'contact@example.com',
    ]);

    requestAssistantAuthentication(['email' => $contact->email])->assertOk();

    requestAssistantAuthentication(['email' => $contact->email])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);

    expect(PortalAuthentication::query()->count())->toBe(1);
});

test('it invalidates a contact\'s prior authentication when a new assistant code is requested', function () {
    Notification::fake();

    $contact = Contact::factory()->create([
        'email' => 'contact@example.com',
    ]);

    requestAssistantAuthentication(['email' => $contact->email])->assertOk();

    $firstAuthentication = PortalAuthentication::query()->latest('id')->first();

    expect($firstAuthentication)->not->toBeNull();

    // Clear the per-target mint cooldown so a second request is allowed.
    RateLimiter::clear(app(AuthenticationCodeRateLimiter::class)->codeRequestKey($contact, 'assistant-widget'));

    requestAssistantAuthentication(['email' => $contact->email])->assertOk();

    $authentications = PortalAuthentication::query()->where('educatable_id', $contact->getKey())->get();

    expect($authentications)->toHaveCount(1)
        ->and($authentications->first()->id)->not->toBe($firstAuthentication?->id);
});
