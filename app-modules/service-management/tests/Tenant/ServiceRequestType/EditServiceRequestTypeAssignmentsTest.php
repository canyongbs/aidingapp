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
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeAssignmentTypes;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\EditServiceRequestTypeAssignments;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Rules\ServiceRequestTypeAssignmentsIndividualUserMustBeAManager;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\EditServiceRequestTypeAssignmentsRequestFactory;
use AidingApp\Team\Models\Team;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;
use function Tests\asSuperAdmin;

test('A successful action on the EditServiceRequestTypeAssignments page', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin()
        ->get(
            EditServiceRequestTypeAssignments::getUrl([
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditServiceRequestTypeAssignmentsRequestFactory::new()->withRandomTypeNotIncludingIndividual()->create();

    livewire(EditServiceRequestTypeAssignments::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertFormSet([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::None,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['assignment_type'], $serviceRequestType->fresh()->assignment_type->value);
});

test('A successful action on the EditServiceRequestTypeAssignments page when the type selected is Individual', function () {
    $managerTeam = Team::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()
        ->hasAttached(
            factory: $managerTeam,
            relationship: 'managers'
        )
        ->create();

    asSuperAdmin()
        ->get(
            EditServiceRequestTypeAssignments::getUrl([
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditServiceRequestTypeAssignmentsRequestFactory::new()
        ->withIndividualType()
        ->withIndividualId($managerTeam)
        ->create();

    livewire(EditServiceRequestTypeAssignments::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['assignment_type'], $serviceRequestType->fresh()->assignment_type);
});

test('EditServiceRequestTypeAssignments requires valid data', function (EditServiceRequestTypeAssignmentsRequestFactory $data, $errors) {
    asSuperAdmin();

    $serviceRequestType = ServiceRequestType::factory()->create();

    livewire(EditServiceRequestTypeAssignments::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm($data->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequestType::class, $serviceRequestType->fresh()->toArray());
})->with(
    [
        'assignment_type is required' => [EditServiceRequestTypeAssignmentsRequestFactory::new()->state(['assignment_type' => null]), ['assignment_type' => 'required']],
        'assignment_type is not a valid enum value' => [EditServiceRequestTypeAssignmentsRequestFactory::new()->state(['assignment_type' => 'blah']), ['assignment_type' => 'in']],
        'assignment_type_individual_id is required when assignment_type is Individual' => [EditServiceRequestTypeAssignmentsRequestFactory::new()->withIndividualType()->state(['assignment_type_individual_id' => null]), ['assignment_type_individual_id' => 'required']],
        'assignment_type_individual_id must be a User in the ServiceRequestTypes managers' => [EditServiceRequestTypeAssignmentsRequestFactory::new()->withIndividualType()->state(['assignment_type_individual_id' => User::factory()]), ['assignment_type_individual_id' => ServiceRequestTypeAssignmentsIndividualUserMustBeAManager::class]],
    ]
);

// Permission Tests

test('EditServiceRequestTypeAssignments is gated with proper access control', function () {
    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    actingAs($user)
        ->get(
            EditServiceRequestTypeAssignments::getUrl([
                'record' => $serviceRequestType,
            ])
        )->assertForbidden();

    livewire(EditServiceRequestTypeAssignments::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    actingAs($user)
        ->get(
            EditServiceRequestTypeAssignments::getUrl([
                'record' => $serviceRequestType,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestTypeAssignmentsRequestFactory::new()->withRandomTypeNotIncludingIndividual()->create());

    livewire(EditServiceRequestTypeAssignments::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['assignment_type'], $serviceRequestType->fresh()->assignment_type->value);
});

test('EditServiceRequestTypeAssignments is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    $serviceRequestType = ServiceRequestType::factory()->create();

    actingAs($user)
        ->get(
            EditServiceRequestTypeAssignments::getUrl([
                'record' => $serviceRequestType,
            ])
        )->assertForbidden();

    livewire(EditServiceRequestTypeAssignments::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            EditServiceRequestTypeAssignments::getUrl([
                'record' => $serviceRequestType,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestTypeAssignmentsRequestFactory::new()->withRandomTypeNotIncludingIndividual()->create());

    livewire(EditServiceRequestTypeAssignments::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['assignment_type'], $serviceRequestType->fresh()->assignment_type->value);
});
