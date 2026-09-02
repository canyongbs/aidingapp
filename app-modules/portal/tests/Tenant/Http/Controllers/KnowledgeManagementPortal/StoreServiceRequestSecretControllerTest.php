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
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Models\Secret;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Features\PasswordFormFieldFeature;
use Illuminate\Support\Facades\Crypt;

use function Pest\Laravel\actingAs;

use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->save();
});

it('stores an encrypted secret and returns only its id', function () {
    $contact = Contact::factory()->create();

    $response = actingAs($contact, 'contact')->postJson(
        route('api.portal.service-request.store-secret'),
        ['value' => 'service-request-password'],
    );

    $response->assertSuccessful()
        ->assertJsonStructure(['id'])
        ->assertJsonMissing(['value' => 'service-request-password']);

    $secret = Secret::query()->findOrFail($response->json('id'));

    expect($secret->value)->not->toBe('service-request-password')
        ->and(Crypt::decryptString($secret->value))->toBe('service-request-password')
        ->and($secret->author->is($contact))->toBeTrue()
        ->and($secret->related_id)->toBeNull();
});

it('denies unauthenticated secret storage', function () {
    $this->postJson(
        route('api.portal.service-request.store-secret'),
        ['value' => 'service-request-password'],
    )->assertUnauthorized();

    expect(Secret::query()->exists())->toBeFalse();
});

it('replaces an unattached secret owned by the contact', function () {
    $contact = Contact::factory()->create();
    $previousSecret = Secret::factory()->for($contact, 'author')->create();

    $response = actingAs($contact, 'contact')->postJson(
        route('api.portal.service-request.store-secret'),
        [
            'value' => 'replacement-password',
            'previous_secret_id' => $previousSecret->getKey(),
        ],
    );

    $response->assertSuccessful();

    $secret = Secret::query()->findOrFail($response->json('id'));

    expect($secret->is($previousSecret))->toBeTrue()
        ->and(Crypt::decryptString($secret->value))->toBe('replacement-password')
        ->and($secret->author->is($contact))->toBeTrue()
        ->and(Secret::query()->count())->toBe(1);
});

it('retries replacing an unattached secret with the same id', function () {
    $contact = Contact::factory()->create();
    $secret = Secret::factory()->for($contact, 'author')->create();

    foreach (range(1, 2) as $attempt) {
        actingAs($contact, 'contact')->postJson(
            route('api.portal.service-request.store-secret'),
            [
                'value' => 'replacement-password',
                'previous_secret_id' => $secret->getKey(),
            ],
        )
            ->assertSuccessful()
            ->assertJson(['id' => $secret->getKey()]);
    }

    expect(Crypt::decryptString($secret->refresh()->value))->toBe('replacement-password')
        ->and(Secret::query()->count())->toBe(1);
});

it('clears an unattached secret owned by the contact', function () {
    $contact = Contact::factory()->create();
    $secret = Secret::factory()->for($contact, 'author')->create();

    actingAs($contact, 'contact')->postJson(
        route('api.portal.service-request.store-secret'),
        [
            'value' => null,
            'previous_secret_id' => $secret->getKey(),
        ],
    )
        ->assertSuccessful()
        ->assertJson(['id' => null]);

    expect($secret->fresh())->toBeNull();
});

it('retries clearing an unattached secret', function () {
    $contact = Contact::factory()->create();
    $secret = Secret::factory()->for($contact, 'author')->create();

    foreach (range(1, 2) as $attempt) {
        actingAs($contact, 'contact')->postJson(
            route('api.portal.service-request.store-secret'),
            [
                'value' => null,
                'previous_secret_id' => $secret->getKey(),
            ],
        )
            ->assertSuccessful()
            ->assertJson(['id' => null]);
    }

    expect($secret->fresh())->toBeNull();
});

it('does not replace a secret owned by another contact', function () {
    $contact = Contact::factory()->create();
    $secret = Secret::factory()->for(Contact::factory(), 'author')->create();

    actingAs($contact, 'contact')->postJson(
        route('api.portal.service-request.store-secret'),
        [
            'value' => 'replacement-password',
            'previous_secret_id' => $secret->getKey(),
        ],
    )->assertUnprocessable();

    expect($secret->fresh())->not->toBeNull()
        ->and(Secret::query()->count())->toBe(1);
});

it('does not replace a secret that is already attached', function () {
    $contact = Contact::factory()->create();
    $secret = Secret::factory()
        ->for($contact, 'author')
        ->for(ServiceRequest::factory(), 'related')
        ->create();

    actingAs($contact, 'contact')->postJson(
        route('api.portal.service-request.store-secret'),
        [
            'value' => 'replacement-password',
            'previous_secret_id' => $secret->getKey(),
        ],
    )->assertUnprocessable();

    expect($secret->fresh())->not->toBeNull()
        ->and(Secret::query()->count())->toBe(1);
});

it('does not store secrets when the feature is inactive', function () {
    PasswordFormFieldFeature::deactivate();

    $contact = Contact::factory()->create();

    actingAs($contact, 'contact')->postJson(
        route('api.portal.service-request.store-secret'),
        ['value' => 'service-request-password'],
    )->assertNotFound();

    expect(Secret::query()->exists())->toBeFalse();
});

it('rate limits secret storage per contact', function () {
    $contact = Contact::factory()->create();

    foreach (range(1, 30) as $attempt) {
        actingAs($contact, 'contact')->postJson(
            route('api.portal.service-request.store-secret'),
            ['value' => "service-request-password-{$attempt}"],
        )->assertSuccessful();
    }

    actingAs($contact, 'contact')->postJson(
        route('api.portal.service-request.store-secret'),
        ['value' => 'rate-limited-password'],
    )->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);
});
