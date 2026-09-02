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

use AidingApp\ServiceManagement\Models\Secret;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Crypt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

use Symfony\Component\HttpFoundation\Response;

use function Tests\asSuperAdmin;

beforeEach(function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();
});

it('stores encrypted secrets authored by the staff user', function () {
    $user = User::factory()->create();
    asSuperAdmin($user);

    $response = postJson(route('service-request.store-secret'), [
        'value' => 'service-request-password',
    ]);

    $response->assertOk()->assertJsonStructure(['id']);

    $secret = Secret::query()->findOrFail($response->json('id'));

    expect(Crypt::decryptString($secret->value))->toBe('service-request-password')
        ->and($secret->author->is($user))->toBeTrue()
        ->and($secret->related_id)->toBeNull();
});

it('denies staff users without permission to create service requests', function () {
    actingAs(User::factory()->create());

    postJson(route('service-request.store-secret'), [
        'value' => 'service-request-password',
    ])->assertForbidden();

    expect(Secret::query()->exists())->toBeFalse();
});

it('denies unauthenticated staff secret storage', function () {
    postJson(route('service-request.store-secret'), [
        'value' => 'service-request-password',
    ])->assertUnauthorized();

    expect(Secret::query()->exists())->toBeFalse();
});

it('rate limits staff secret storage per user', function () {
    $user = User::factory()->create();
    asSuperAdmin($user);

    foreach (range(1, 30) as $attempt) {
        postJson(route('service-request.store-secret'), [
            'value' => "service-request-password-{$attempt}",
        ])->assertOk();
    }

    postJson(route('service-request.store-secret'), [
        'value' => 'rate-limited-password',
    ])->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);
});
