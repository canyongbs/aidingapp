<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestStatusResource;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestStatusResource\Pages\CreateServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\CreateServiceRequestStatusRequestFactory;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Validation\Rules\Enum;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Tests\asSuperAdmin;

test('A successful action on the CreateServiceRequestStatus page', function () {
    asSuperAdmin()
        ->get(
            ServiceRequestStatusResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = CreateServiceRequestStatusRequestFactory::new()->create();

    livewire(CreateServiceRequestStatus::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(2, ServiceRequestStatus::all());

    assertDatabaseHas(ServiceRequestStatus::class, $request);
});

test('CreateServiceRequestStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    ServiceRequestStatus::query()->truncate();

    livewire(CreateServiceRequestStatus::class)
        ->fillForm(CreateServiceRequestStatusRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertDatabaseMissing((new ServiceRequestStatus())->getTable(), CreateServiceRequestStatusRequestFactory::new($data)->create());
})->with(
    [
        'name missing' => [CreateServiceRequestStatusRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateServiceRequestStatusRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [CreateServiceRequestStatusRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [CreateServiceRequestStatusRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

// Permission Tests

test('CreateServiceRequestStatus is gated with proper access control', function () {
    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateServiceRequestStatus::class)
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.create');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateServiceRequestStatusRequestFactory::new()->create());

    livewire(CreateServiceRequestStatus::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(2, ServiceRequestStatus::all());

    assertDatabaseHas(ServiceRequestStatus::class, $request->toArray());
});

test('CreateServiceRequestStatus is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.create');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateServiceRequestStatus::class)
        ->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateServiceRequestStatusRequestFactory::new()->create());

    livewire(CreateServiceRequestStatus::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(2, ServiceRequestStatus::all());

    assertDatabaseHas(ServiceRequestStatus::class, $request->toArray());
});
