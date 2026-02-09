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

use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ContactServiceManagement;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\ServiceRequestsRelationManager;
use AidingApp\Contact\Models\Contact;
use AidingApp\Division\Models\Division;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Team\Models\Team;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Forms\Components\Select;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('ServiceRequestsRelationManager can list service requests for a contact', function () {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $contact = Contact::factory()->create();

    $serviceRequest = ServiceRequest::factory()
        ->state([
            'respondent_id' => $contact->getKey(),
        ])
        ->create();

    livewire(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ContactServiceManagement::class,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$serviceRequest]);
});

test('ServiceRequestsRelationManager filters service requests by priority', function () {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $contact = Contact::factory()->create();

    $priority1 = ServiceRequestPriority::factory()->create();
    $priority2 = ServiceRequestPriority::factory()->create();

    $serviceRequest1 = ServiceRequest::factory()->state([
        'respondent_id' => $contact->getKey(),
        'priority_id' => $priority1->getKey(),
    ])->create();

    $serviceRequest2 = ServiceRequest::factory()->state([
        'respondent_id' => $contact->getKey(),
        'priority_id' => $priority2->getKey(),
    ])->create();

    livewire(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ContactServiceManagement::class,
    ])
        ->filterTable('priority', $priority1->getKey())
        ->assertCanSeeTableRecords([$serviceRequest1])
        ->assertCanNotSeeTableRecords([$serviceRequest2]);
});

test('ServiceRequestsRelationManager filters service requests by status', function () {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $contact = Contact::factory()->create();

    $status1 = ServiceRequestStatus::factory()->create();
    $status2 = ServiceRequestStatus::factory()->create();

    $serviceRequest1 = ServiceRequest::factory()->state([
        'respondent_id' => $contact->getKey(),
        'status_id' => $status1->getKey(),
    ])->create();

    $serviceRequest2 = ServiceRequest::factory()->state([
        'respondent_id' => $contact->getKey(),
        'status_id' => $status2->getKey(),
    ])->create();

    livewire(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ContactServiceManagement::class,
    ])
        ->filterTable('status', $status1->getKey())
        ->assertCanSeeTableRecords([$serviceRequest1])
        ->assertCanNotSeeTableRecords([$serviceRequest2]);
});

test('Only service request types managed by user team are available in type select', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();
    $team = Team::factory()->create();
    $user->team()->associate($team)->save();
    $user->givePermissionTo('service_request.create');

    $managedType = ServiceRequestType::factory()->create();
    $managedType->managers()->attach($team);

    $unmanagedType = ServiceRequestType::factory()->create();

    $contact = Contact::factory()->create();

    actingAs($user);

    livewire(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ContactServiceManagement::class,
    ])
        ->mountTableAction('create')
        ->assertFormFieldExists('type_id', 'mountedActionSchema0', function (Select $select) use ($managedType) {
            $options = $select->getOptions();

            return array_key_exists($managedType->getKey(), $options);
        })
        ->assertFormFieldExists('type_id', 'mountedActionSchema0', function (Select $select) use ($unmanagedType) {
            $options = $select->getOptions();

            return ! array_key_exists($unmanagedType->getKey(), $options);
        })
        ->assertSuccessful();
});

test('Super admin can see all service request types', function () {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $type1 = ServiceRequestType::factory()->create();
    $type2 = ServiceRequestType::factory()->create();

    $contact = Contact::factory()->create();

    livewire(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ContactServiceManagement::class,
    ])
        ->mountTableAction('create')
        ->assertFormFieldExists('type_id', 'mountedActionSchema0', function (Select $select) use ($type1, $type2) {
            $options = $select->getOptions();

            return array_key_exists($type1->getKey(), $options) && array_key_exists($type2->getKey(), $options);
        })
        ->assertSuccessful();
});

test('Priority select is populated based on selected type', function () {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $type = ServiceRequestType::factory()->create();
    ServiceRequestPriority::factory()->state(['type_id' => $type->getKey()])->create();
    ServiceRequestPriority::factory()->state(['type_id' => $type->getKey()])->create();

    $contact = Contact::factory()->create();

    livewire(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ContactServiceManagement::class,
    ])
        ->mountTableAction('create')
        ->setTableActionData([
            'type_id' => $type->getKey(),
        ])
        ->assertFormFieldExists('priority_id', 'mountedActionSchema0')
        ->assertSuccessful();
});

test('Division field is visible when multiple divisions exist', function () {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    Division::factory()->count(2)->create();

    $contact = Contact::factory()->create();

    livewire(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ContactServiceManagement::class,
    ])
        ->mountTableAction('create')
        ->assertFormFieldExists('division_id', 'mountedActionSchema0')
        ->assertSuccessful();
});

test('Division field is hidden when only one division exists', function () {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    Division::query()->delete();
    Division::factory()->create();

    $contact = Contact::factory()->create();

    livewire(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ContactServiceManagement::class,
    ])
        ->mountTableAction('create')
        ->assertFormFieldIsHidden('division_id', 'mountedActionSchema0')
        ->assertSuccessful();
});

test('Can create a service request for a contact', function () {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $contact = Contact::factory()->create();
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->getKey()])->create();
    $status = ServiceRequestStatus::factory()->create();
    $division = Division::factory()->create();

    livewire(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ContactServiceManagement::class,
    ])
        ->callTableAction('create', data: [
            'division_id' => $division->getKey(),
            'status_id' => $status->getKey(),
            'type_id' => $type->getKey(),
            'priority_id' => $priority->getKey(),
            'title' => 'Test Service Request',
            'close_details' => 'Description details',
        ])
        ->assertSuccessful();

    expect($contact->serviceRequests()->count())->toBe(1);
    expect($contact->serviceRequests()->first()->title)->toBe('Test Service Request');
    expect($contact->serviceRequests()->first()->respondent_id)->toBe($contact->getKey());
});

test('Can view a service request', function () {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $contact = Contact::factory()->create();
    $serviceRequest = ServiceRequest::factory()->state([
        'respondent_id' => $contact->getKey(),
    ])->create();

    livewire(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ContactServiceManagement::class,
    ])
        ->callTableAction('view', $serviceRequest)
        ->assertSuccessful();
});

test('Can edit a service request', function () {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $contact = Contact::factory()->create();
    $serviceRequest = ServiceRequest::factory()->state([
        'respondent_id' => $contact->getKey(),
    ])->create();

    $newStatus = ServiceRequestStatus::factory()->create();

    livewire(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ContactServiceManagement::class,
    ])
        ->callTableAction('edit', $serviceRequest, data: [
            'status_id' => $newStatus->getKey(),
            'title' => 'Updated Title',
        ])
        ->assertSuccessful();

    $serviceRequest->refresh();
    expect($serviceRequest->title)->toBe('Updated Title');
    expect($serviceRequest->status_id)->toBe($newStatus->getKey());
});

test('Non-super admin can only see service requests from managed or audited types', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();
    $team = Team::factory()->create();
    $user->team()->associate($team)->save();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');

    $managedType = ServiceRequestType::factory()->create();
    $managedType->managers()->attach($team);

    $auditedType = ServiceRequestType::factory()->create();
    $auditedType->auditors()->attach($team);

    $unmanagedType = ServiceRequestType::factory()->create();

    $contact = Contact::factory()->create();

    $managedServiceRequest = ServiceRequest::factory()->state([
        'respondent_id' => $contact->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->state(['type_id' => $managedType->getKey()]),
    ])->create();

    $auditedServiceRequest = ServiceRequest::factory()->state([
        'respondent_id' => $contact->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->state(['type_id' => $auditedType->getKey()]),
    ])->create();

    $unmanagedServiceRequest = ServiceRequest::factory()->state([
        'respondent_id' => $contact->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->state(['type_id' => $unmanagedType->getKey()]),
    ])->create();

    actingAs($user);

    livewire(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ContactServiceManagement::class,
    ])
        ->assertCanSeeTableRecords([$managedServiceRequest, $auditedServiceRequest])
        ->assertCanNotSeeTableRecords([$unmanagedServiceRequest]);
});
