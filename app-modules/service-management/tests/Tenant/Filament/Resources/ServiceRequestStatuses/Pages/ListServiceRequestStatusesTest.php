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
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestStatuses\Pages\ListServiceRequestStatuses;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestStatuses\ServiceRequestStatusResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Features\ServiceRequestStatusArchivingFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('The correct details are displayed on the ListServiceRequestStatuses page', function () {
    ServiceRequestStatus::query()->truncate();

    $serviceRequestStatuses = ServiceRequestStatus::factory()
        ->has(ServiceRequest::factory()->count(fake()->randomNumber(1)), 'serviceRequests')
        ->count(10)
        ->create();

    asSuperAdmin();

    $component = livewire(ListServiceRequestStatuses::class);

    $component
        ->assertSuccessful()
        ->assertCanSeeTableRecords($serviceRequestStatuses)
        ->assertCountTableRecords(10)
        ->assertTableColumnExists('service_requests_count');

    $serviceRequestStatuses->each(
        fn (ServiceRequestStatus $serviceRequestType) => $component
            ->assertTableColumnStateSet(
                'id',
                $serviceRequestType->id,
                $serviceRequestType
            )
            ->assertTableColumnStateSet(
                'name',
                $serviceRequestType->name,
                $serviceRequestType
            )
            ->assertTableColumnFormattedStateSet(
                'classification',
                $serviceRequestType->classification->getLabel(),
                $serviceRequestType
            )
            ->assertTableColumnFormattedStateSet(
                'color',
                $serviceRequestType->color->getLabel(),
                $serviceRequestType
            )
        // Currently setting not test for service_request_count as there is no easy way to check now, relying on underlying package tests
    );
});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListServiceRequestStatuses is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl('index')
        )->assertSuccessful();
});

test('ListServiceRequestStatuses is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl()
        )->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestStatusResource::getUrl()
        )->assertSuccessful();
});

it('only shows the bulk archive action to a user with the settings.delete permission', function () {
    ServiceRequestStatus::factory(15)->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('settings.view-any', 'settings.*.view');

    actingAs($user);

    livewire(ListServiceRequestStatuses::class)
        ->assertActionHidden(TestAction::make('archive')->table()->bulk());

    $user->givePermissionTo('settings.*.delete');

    livewire(ListServiceRequestStatuses::class)
        ->assertActionVisible(TestAction::make('archive')->table()->bulk());
});

describe('archiving', function () {
    it('hides archived service request statuses by default', function () {
        asSuperAdmin();

        $active = ServiceRequestStatus::factory()->create();
        $archived = ServiceRequestStatus::factory()->archived()->create();

        livewire(ListServiceRequestStatuses::class)
            ->assertCanSeeTableRecords([$active])
            ->assertCanNotSeeTableRecords([$archived]);
    });

    it('can list only archived service request statuses through the filter', function () {
        asSuperAdmin();

        $active = ServiceRequestStatus::factory()->create();
        $archived = ServiceRequestStatus::factory()->archived()->create();

        livewire(ListServiceRequestStatuses::class)
            ->filterTable('archived', false)
            ->assertCanSeeTableRecords([$archived])
            ->assertCanNotSeeTableRecords([$active]);
    });

    it('appends an archived marker to the name of an archived service request status', function () {
        asSuperAdmin();

        $archived = ServiceRequestStatus::factory()->archived()->create();

        livewire(ListServiceRequestStatuses::class)
            ->filterTable('archived', false)
            ->assertTableColumnFormattedStateSet('name', "{$archived->name} (Archived)", $archived);
    });

    it('does not append an archived marker to the name of an active service request status', function () {
        asSuperAdmin();

        $active = ServiceRequestStatus::factory()->create();

        livewire(ListServiceRequestStatuses::class)
            ->assertTableColumnFormattedStateSet('name', $active->name, $active);
    });

    it('can archive service request statuses from the bulk action', function () {
        asSuperAdmin();

        $statuses = ServiceRequestStatus::factory()->count(3)->create();

        $statuses->each(fn (ServiceRequestStatus $status) => expect($status->isArchived())->toBeFalse());

        livewire(ListServiceRequestStatuses::class)
            ->selectTableRecords($statuses->modelKeys())
            ->callAction(TestAction::make('archive')->table()->bulk());

        $statuses->each(fn (ServiceRequestStatus $status) => expect($status->refresh()->isArchived())->toBeTrue()
            ->and($status->trashed())->toBeFalse());
    });

    it('does not archive a system protected service request status through the bulk action', function () {
        asSuperAdmin();

        $protected = ServiceRequestStatus::factory()->systemProtected()->create();
        $archivable = ServiceRequestStatus::factory()->create();

        expect($protected->isArchived())->toBeFalse()
            ->and($archivable->isArchived())->toBeFalse();

        livewire(ListServiceRequestStatuses::class)
            ->selectTableRecords([$protected->getKey(), $archivable->getKey()])
            ->callAction(TestAction::make('archive')->table()->bulk());

        expect($protected->refresh()->isArchived())->toBeFalse()
            ->and($archivable->refresh()->isArchived())->toBeTrue();
    });

    it('can unarchive a service request status from the row action', function () {
        asSuperAdmin();

        $archived = ServiceRequestStatus::factory()->archived()->create();

        expect($archived->isArchived())->toBeTrue();

        livewire(ListServiceRequestStatuses::class)
            ->filterTable('archived', false)
            ->callAction(TestAction::make('unarchive')->table($archived));

        expect($archived->refresh()->isArchived())->toBeFalse();
    });

    it('does not re-archive a service request status that is already archived', function () {
        asSuperAdmin();

        $archived = ServiceRequestStatus::factory()->archived()->create();

        $originalArchivedAt = $archived->archived_at;

        $this->travel(1)->minutes();

        livewire(ListServiceRequestStatuses::class)
            ->filterTable('archived', false)
            ->selectTableRecords([$archived->getKey()])
            ->callAction(TestAction::make('archive')->table()->bulk());

        expect($archived->refresh()->archived_at->toDateTimeString())->toBe($originalArchivedAt->toDateTimeString());
    });

    it('warns that bulk archiving will not stop service request type automations', function () {
        asSuperAdmin();

        $automated = ServiceRequestStatus::factory()->create();
        $alsoAutomated = ServiceRequestStatus::factory()->create();
        $notAutomated = ServiceRequestStatus::factory()->create();

        ServiceRequestType::factory()->for($automated, 'automatedStatus')->create(['name' => 'Password Reset']);
        ServiceRequestType::factory()->for($alsoAutomated, 'automatedStatus')->create(['name' => 'VPN Access Request']);

        $component = livewire(ListServiceRequestStatuses::class)
            ->selectTableRecords([$automated->getKey(), $alsoAutomated->getKey(), $notAutomated->getKey()])
            ->mountAction(TestAction::make('archive')->table()->bulk());

        expect($component->instance()->getMountedAction()->getModalDescription())
            ->toBe('2 of the selected statuses are used for automatic status changes by 2 service request types: Password Reset, VPN Access Request. Archiving will not stop that automation.');
    });

    it('does not warn when no selected status is automated by a service request type', function () {
        asSuperAdmin();

        $statuses = ServiceRequestStatus::factory()->count(2)->create();

        $component = livewire(ListServiceRequestStatuses::class)
            ->selectTableRecords($statuses->modelKeys())
            ->mountAction(TestAction::make('archive')->table()->bulk());

        expect($component->instance()->getMountedAction()->getModalDescription())
            ->not->toContain('automatic status changes');
    });

    it('does not offer the bulk delete action', function () {
        asSuperAdmin();

        ServiceRequestStatus::factory()->create();

        livewire(ListServiceRequestStatuses::class)
            ->assertActionDoesNotExist(TestAction::make('delete')->table()->bulk());
    });

    // TODO: Cleanup Task (ServiceRequestStatusArchivingFeature): delete this test — it covers the
    // inactive branch, which no longer exists once the flag is removed.
    it('offers the bulk delete action instead when `ServiceRequestStatusArchivingFeature` is inactive', function () {
        ServiceRequestStatusArchivingFeature::deactivate();

        asSuperAdmin();

        ServiceRequestStatus::factory()->create();

        livewire(ListServiceRequestStatuses::class)
            ->assertActionDoesNotExist(TestAction::make('archive')->table()->bulk())
            ->assertActionVisible(TestAction::make('delete')->table()->bulk());
    });
});
