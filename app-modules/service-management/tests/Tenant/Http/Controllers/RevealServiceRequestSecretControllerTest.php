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
      in the software, and you may not remove or obscure any functionality that
      is protected by the license key.
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

use AidingApp\ServiceManagement\Actions\ResolveServiceRequestSecretEncrypter;
use AidingApp\ServiceManagement\Models\Secret;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Features\PasswordFormFieldFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;
use function Tests\asSuperAdmin;

beforeEach(function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();
});

function revealableSecretFor(User $manager, bool $isAssigned = true): Secret
{
    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managerUsers()->attach($manager);

    $serviceRequest = ServiceRequest::factory()
        ->for(ServiceRequestPriority::factory()->for($serviceRequestType, 'type'), 'priority')
        ->create();

    $serviceRequest->secret_key = Crypt::encryptString(
        'base64:' . base64_encode(Encrypter::generateKey(config('app.cipher')))
    );
    $serviceRequest->save();

    if ($isAssigned) {
        ServiceRequestAssignment::factory()
            ->active()
            ->for($serviceRequest, 'serviceRequest')
            ->for($manager, 'user')
            ->create();
    }

    return Secret::factory()
        ->for($serviceRequest, 'related')
        ->create([
            'value' => app(ResolveServiceRequestSecretEncrypter::class)($serviceRequest)
                ->encryptString('service-request-password'),
        ]);
}

it('reveals a secret to the assigned manager', function () {
    $manager = user(permissions: ['service_request.*.update']);
    $secret = revealableSecretFor($manager);

    actingAs($manager);

    postJson(route('service-request.reveal-secret'), [
        'secret_id' => $secret->getKey(),
    ])
        ->assertOk()
        ->assertHeader('Cache-Control', 'no-store, private')
        ->assertExactJson(['value' => 'service-request-password']);
});

it('denies an unassigned service request secret to a super admin', function () {
    $superAdmin = User::factory()->create();
    $secret = revealableSecretFor($superAdmin, isAssigned: false);

    asSuperAdmin($superAdmin);

    postJson(route('service-request.reveal-secret'), [
        'secret_id' => $secret->getKey(),
    ])->assertForbidden();
});

it('denies a secret to a manager who is not assigned to the service request', function () {
    $assignedManager = user(permissions: ['service_request.*.update']);
    $secret = revealableSecretFor($assignedManager);
    $otherManager = user(permissions: ['service_request.*.update']);
    $secret->related->priority->type->managerUsers()->attach($otherManager);

    actingAs($otherManager);

    postJson(route('service-request.reveal-secret'), [
        'secret_id' => $secret->getKey(),
    ])->assertForbidden();
});

it('does not reveal an unattached secret', function () {
    $manager = user(permissions: ['service_request.*.update']);
    $secret = Secret::factory()->for($manager, 'author')->create();

    actingAs($manager);

    postJson(route('service-request.reveal-secret'), [
        'secret_id' => $secret->getKey(),
    ])->assertNotFound();
});

it('does not reveal secrets when the feature is inactive', function () {
    $manager = user(permissions: ['service_request.*.update']);
    $secret = revealableSecretFor($manager);

    PasswordFormFieldFeature::deactivate();
    actingAs($manager);

    postJson(route('service-request.reveal-secret'), [
        'secret_id' => $secret->getKey(),
    ])->assertNotFound();
});

it('denies unauthenticated secret reveals', function () {
    $secret = revealableSecretFor(user(permissions: ['service_request.*.update']));

    postJson(route('service-request.reveal-secret'), [
        'secret_id' => $secret->getKey(),
    ])->assertUnauthorized();
});
