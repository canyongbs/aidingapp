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

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\Pages\ManageServiceRequestTypeAuditors;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Team\Models\Team;
use App\Features\ServiceRequestCategoryRenameFeature;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

// TODO: ServiceRequestCategoryRenameFeature Cleanup - Remove this beforeEach after the feature flag is removed.
beforeEach(function () {
    ServiceRequestCategoryRenameFeature::activate();
});

test('A successful action on the ManageServiceRequestTypeAuditors page', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestTypeResource::getUrl('service-request-type-auditors', [
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )
        ->assertSuccessful();
});

it('can attach auditor users to a service request type', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    $user = User::factory()->create();

    asSuperAdmin();

    livewire(ManageServiceRequestTypeAuditors::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm([
            'auditorUsers' => [$user->getKey()],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($serviceRequestType->refresh()->auditorUsers->pluck('id'))
        ->toContain($user->getKey());
});

it('can attach auditor teams to a service request type', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    $team = Team::factory()->create();

    asSuperAdmin();

    livewire(ManageServiceRequestTypeAuditors::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm([
            'auditorTeams' => [$team->getKey()],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($serviceRequestType->refresh()->auditorTeams->pluck('id'))
        ->toContain($team->getKey());
});

it('can attach both auditor users and auditor teams to a service request type', function () {
    $serviceRequestType = ServiceRequestType::factory()->create();

    $user = User::factory()->create();
    $team = Team::factory()->create();

    asSuperAdmin();

    livewire(ManageServiceRequestTypeAuditors::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm([
            'auditorUsers' => [$user->getKey()],
            'auditorTeams' => [$team->getKey()],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $serviceRequestType->refresh();

    expect($serviceRequestType->auditorUsers->pluck('id'))->toContain($user->getKey());
    expect($serviceRequestType->auditorTeams->pluck('id'))->toContain($team->getKey());
});

// Permission Tests

test('ManageServiceRequestTypeAuditors is gated with proper access control', function () {
    $user = User::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('service-request-type-auditors', [
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )->assertForbidden();

    livewire(ManageServiceRequestTypeAuditors::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('service-request-type-auditors', [
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )->assertSuccessful();

    $auditorUser = User::factory()->create();

    livewire(ManageServiceRequestTypeAuditors::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm([
            'auditorUsers' => [$auditorUser->getKey()],
            'auditorTeams' => [],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($serviceRequestType->refresh()->auditorUsers->pluck('id'))
        ->toContain($auditorUser->getKey());
});

test('ManageServiceRequestTypeAuditors is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    $serviceRequestType = ServiceRequestType::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('service-request-type-auditors', [
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )->assertForbidden();

    livewire(ManageServiceRequestTypeAuditors::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('service-request-type-auditors', [
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )->assertSuccessful();

    $auditorTeam = Team::factory()->create();

    livewire(ManageServiceRequestTypeAuditors::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->fillForm([
            'auditorUsers' => [],
            'auditorTeams' => [$auditorTeam->getKey()],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($serviceRequestType->refresh()->auditorTeams->pluck('id'))
        ->toContain($auditorTeam->getKey());
});
