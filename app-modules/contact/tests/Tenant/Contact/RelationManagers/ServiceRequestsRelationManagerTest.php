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

use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ViewContact;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\ServiceRequestsRelationManager;
use AidingApp\Contact\Models\Contact;
use AidingApp\Department\Models\Department;
use AidingApp\Form\Filament\Blocks\PasswordFormFieldBlock;
use AidingApp\Group\Models\Group;
use AidingApp\ServiceManagement\Actions\ResolveServiceRequestSecretEncrypter;
use AidingApp\ServiceManagement\Enums\ServiceRequestCategory;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\Secret;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Models\User;
use App\Settings\LicenseSettings;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

/**
 * Flattens a `SelectTree` tree (as returned by `getTree()`) into a flat list of its option values.
 *
 * @param  Collection<int, mixed>|array<int, mixed>  $tree
 *
 * @return array<int, string>
 */
$flattenServiceRequestTypeTree = function (Collection|array $tree): array {
    $flatten = function (array $nodes) use (&$flatten): array {
        $values = [];

        foreach ($nodes as $node) {
            $values[] = $node['value'];
            $values = array_merge($values, $flatten($node['children'] ?? []));
        }

        return $values;
    };

    return $flatten(collect($tree)->toArray());
};

test('ServiceRequestsRelationManager hides the create and edit actions when read only while still listing records', function (): void {
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

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
        'readOnly' => true,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$serviceRequest])
        ->assertActionHidden(TestAction::make('create')->table())
        ->assertActionHidden(TestAction::make('edit')->table($serviceRequest))
        ->assertActionVisible(TestAction::make('view')->table($serviceRequest));
});

test('ServiceRequestsRelationManager shows the create action when not read only', function (): void {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $contact = Contact::factory()->create();

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->assertActionVisible(TestAction::make('create')->table());
});

test('ServiceRequestsRelationManager can list service requests for a contact', function (): void {
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

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$serviceRequest]);
});

test('ServiceRequestsRelationManager filters service requests by priority', function (): void {
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

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->filterTable('priority', $priority1->getKey())
        ->assertCanSeeTableRecords([$serviceRequest1])
        ->assertCanNotSeeTableRecords([$serviceRequest2]);
});

test('ServiceRequestsRelationManager filters service requests by status', function (): void {
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

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->filterTable('status', $status1->getKey())
        ->assertCanSeeTableRecords([$serviceRequest1])
        ->assertCanNotSeeTableRecords([$serviceRequest2]);
});

test('All non-archived service request types are available in the type select for a department-based non-manager', function () use ($flattenServiceRequestTypeTree): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $user = User::factory()->create();
    $department = Department::factory()->create();
    $user->department()->associate($department)->save();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.create');

    $managedType = ServiceRequestType::factory()->create();
    $managedType->managerDepartments()->attach($department);

    $unmanagedType = ServiceRequestType::factory()->create();

    $contact = Contact::factory()->create();

    actingAs($user);

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->mountTableAction('create')
        ->assertFormFieldExists('type_id', 'mountedActionSchema0', function (SelectTree $field) use ($managedType, $unmanagedType, $flattenServiceRequestTypeTree): bool {
            $values = $flattenServiceRequestTypeTree($field->getTree());

            return in_array($managedType->getKey(), $values, true)
                && in_array($unmanagedType->getKey(), $values, true);
        })
        ->assertSuccessful();
});

test('All non-archived service request types are available in the type select for a non-manager', function () use ($flattenServiceRequestTypeTree): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.create');

    $managedType = ServiceRequestType::factory()->create();
    $managedType->managerUsers()->attach($user);

    $unmanagedType = ServiceRequestType::factory()->create();

    $contact = Contact::factory()->create();

    actingAs($user);

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->mountTableAction('create')
        ->assertFormFieldExists('type_id', 'mountedActionSchema0', function (SelectTree $field) use ($managedType, $unmanagedType, $flattenServiceRequestTypeTree): bool {
            $values = $flattenServiceRequestTypeTree($field->getTree());

            return in_array($managedType->getKey(), $values, true)
                && in_array($unmanagedType->getKey(), $values, true);
        })
        ->assertSuccessful();
});

test('Archived service request types are not available in the type select', function () use ($flattenServiceRequestTypeTree): void {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $activeType = ServiceRequestType::factory()->create();
    $archivedType = ServiceRequestType::factory()->create(['archived_at' => now()]);

    $contact = Contact::factory()->create();

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->mountTableAction('create')
        ->assertFormFieldExists('type_id', 'mountedActionSchema0', function (SelectTree $field) use ($activeType, $archivedType, $flattenServiceRequestTypeTree): bool {
            $values = $flattenServiceRequestTypeTree($field->getTree());

            return in_array($activeType->getKey(), $values, true)
                && ! in_array($archivedType->getKey(), $values, true);
        })
        ->assertSuccessful();
});

test('Super admin can see all service request types', function () use ($flattenServiceRequestTypeTree): void {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $type1 = ServiceRequestType::factory()->create();
    $type2 = ServiceRequestType::factory()->create();

    $contact = Contact::factory()->create();

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->mountTableAction('create')
        ->assertFormFieldExists('type_id', 'mountedActionSchema0', function (SelectTree $field) use ($type1, $type2, $flattenServiceRequestTypeTree): bool {
            $values = $flattenServiceRequestTypeTree($field->getTree());

            return in_array($type1->getKey(), $values, true) && in_array($type2->getKey(), $values, true);
        })
        ->assertSuccessful();
});

describe('inaccessible type warning banner', function (): void {
    beforeEach(function (): void {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->serviceManagement = true;
        $settings->save();
    });

    test('is shown when a manager selects a type they neither manage nor audit', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('service_request.view-any');
        $user->givePermissionTo('service_request.create');

        ServiceRequestType::factory()->hasAttached($user, [], 'managerUsers')->create();

        $inaccessibleType = ServiceRequestType::factory()->create();

        $contact = Contact::factory()->create();

        actingAs($user);

        Livewire::test(ServiceRequestsRelationManager::class, [
            'ownerRecord' => $contact,
            'pageClass' => ViewContact::class,
        ])
            ->mountTableAction('create')
            ->assertSchemaComponentHidden('inaccessibleTypeWarning', 'mountedActionSchema0')
            ->setTableActionData(['type_id' => $inaccessibleType->getKey()])
            ->assertSchemaComponentVisible('inaccessibleTypeWarning', 'mountedActionSchema0');
    });

    test('is not shown when the selected type is managed by the user', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('service_request.view-any');
        $user->givePermissionTo('service_request.create');

        $managedType = ServiceRequestType::factory()->hasAttached($user, [], 'managerUsers')->create();

        $contact = Contact::factory()->create();

        actingAs($user);

        Livewire::test(ServiceRequestsRelationManager::class, [
            'ownerRecord' => $contact,
            'pageClass' => ViewContact::class,
        ])
            ->mountTableAction('create')
            ->setTableActionData(['type_id' => $managedType->getKey()])
            ->assertSchemaComponentHidden('inaccessibleTypeWarning', 'mountedActionSchema0');
    });

    test('is not shown when the selected type is audited by the user', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('service_request.view-any');
        $user->givePermissionTo('service_request.create');

        ServiceRequestType::factory()->hasAttached($user, [], 'managerUsers')->create();

        $auditedType = ServiceRequestType::factory()->hasAttached($user, [], 'auditorUsers')->create();

        $contact = Contact::factory()->create();

        actingAs($user);

        Livewire::test(ServiceRequestsRelationManager::class, [
            'ownerRecord' => $contact,
            'pageClass' => ViewContact::class,
        ])
            ->mountTableAction('create')
            ->setTableActionData(['type_id' => $auditedType->getKey()])
            ->assertSchemaComponentHidden('inaccessibleTypeWarning', 'mountedActionSchema0');
    });

    test('is not shown for a super admin', function (): void {
        asSuperAdmin();

        $type = ServiceRequestType::factory()->create();

        $contact = Contact::factory()->create();

        Livewire::test(ServiceRequestsRelationManager::class, [
            'ownerRecord' => $contact,
            'pageClass' => ViewContact::class,
        ])
            ->mountTableAction('create')
            ->setTableActionData(['type_id' => $type->getKey()])
            ->assertSchemaComponentHidden('inaccessibleTypeWarning', 'mountedActionSchema0');
    });
});

test('Selecting a type sets the category from the type default category', function (): void {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $type = ServiceRequestType::factory()->create([
        'default_category' => ServiceRequestCategory::Incident,
    ]);

    $contact = Contact::factory()->create();

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->mountTableAction('create')
        ->setTableActionData(['type_id' => $type->getKey()])
        ->assertTableActionDataSet([
            'category' => ServiceRequestCategory::Incident,
        ]);
});

test('Can create a service request for a contact with a category', function (): void {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $contact = Contact::factory()->create();
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->getKey()])->create();
    $status = ServiceRequestStatus::factory()->create();

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->callTableAction('create', data: [
            'status_id' => $status->getKey(),
            'type_id' => $type->getKey(),
            'priority_id' => $priority->getKey(),
            'category' => ServiceRequestCategory::Incident->value,
            'title' => 'Test Service Request',
        ])
        ->assertHasNoTableActionErrors();

    $serviceRequest = $contact->serviceRequests()->sole();

    expect($serviceRequest->category)->toBe(ServiceRequestCategory::Incident);
});

test('A non-super-admin manager can create a service request for a contact under a type they neither manage nor audit', function (): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.create');

    // The user manages some other type, so they are a manager in general, but not of the selected type.
    ServiceRequestType::factory()->hasAttached($user, [], 'managerUsers')->create();

    $inaccessibleType = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $inaccessibleType->getKey()])->create();
    $status = ServiceRequestStatus::factory()->create();

    $contact = Contact::factory()->create();

    actingAs($user);

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->callTableAction('create', data: [
            'status_id' => $status->getKey(),
            'type_id' => $inaccessibleType->getKey(),
            'priority_id' => $priority->getKey(),
            'category' => ServiceRequestCategory::Request->value,
            'title' => 'Test Service Request',
        ])
        ->assertHasNoTableActionErrors();

    $serviceRequest = $contact->serviceRequests()->sole();

    expect($serviceRequest->respondent_id)->toBe($contact->getKey())
        ->and($serviceRequest->priority->type->is($inaccessibleType))->toBeTrue();
});

test('Priority select is populated based on selected type', function (): void {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $type = ServiceRequestType::factory()->create();
    ServiceRequestPriority::factory()->state(['type_id' => $type->getKey()])->create();
    ServiceRequestPriority::factory()->state(['type_id' => $type->getKey()])->create();

    $contact = Contact::factory()->create();

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->mountTableAction('create')
        ->setTableActionData([
            'type_id' => $type->getKey(),
        ])
        ->assertFormFieldExists('priority_id', 'mountedActionSchema0')
        ->assertSuccessful();
});

test('Can create a service request for a contact', function (): void {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $contact = Contact::factory()->create();
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->getKey()])->create();
    $status = ServiceRequestStatus::factory()->create();

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->callTableAction('create', data: [
            'status_id' => $status->getKey(),
            'type_id' => $type->getKey(),
            'priority_id' => $priority->getKey(),
            'category' => ServiceRequestCategory::Request->value,
            'title' => 'Test Service Request',
            'close_details' => 'Description details',
        ])
        ->assertSuccessful();

    expect($contact->serviceRequests()->count())->toBe(1);
    expect($contact->serviceRequests()->first()->title)->toBe('Test Service Request');
    expect($contact->serviceRequests()->first()->respondent_id)->toBe($contact->getKey());
});

test('Attaches password secrets to a service request created for a contact', function (): void {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $author = auth()->user();
    $contact = Contact::factory()->create();
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->state(['type_id' => $type->getKey()])->create();
    $status = ServiceRequestStatus::factory()->create();

    $form = ServiceRequestForm::factory()->for($type, 'type')->create();
    $field = new ServiceRequestFormField([
        'label' => 'Private credential',
        'type' => PasswordFormFieldBlock::type(),
        'is_required' => true,
        'config' => [],
    ]);
    $field->submissible()->associate($form);
    $field->save();

    $form->setAttribute('content', [
        'type' => 'doc',
        'content' => [[
            'type' => 'customBlock',
            'attrs' => [
                'id' => PasswordFormFieldBlock::type(),
                'config' => [
                    'fieldId' => $field->getKey(),
                    'label' => $field->label,
                    'isRequired' => true,
                ],
            ],
        ]],
    ]);
    $form->save();

    $secret = Secret::factory()
        ->for($author, 'author')
        ->create(['value' => Crypt::encryptString('service-request-password')]);

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->callTableAction('create', data: [
            'status_id' => $status->getKey(),
            'type_id' => $type->getKey(),
            'priority_id' => $priority->getKey(),
            'category' => ServiceRequestCategory::Request->value,
            'title' => 'Test Service Request',
            'dynamic_fields' => [
                $field->getKey() => $secret->getKey(),
            ],
        ])
        ->assertHasNoTableActionErrors();

    $serviceRequest = $contact->serviceRequests()->sole();
    $secret->refresh();

    expect($secret->related->is($serviceRequest))->toBeTrue()
        ->and(app(ResolveServiceRequestSecretEncrypter::class)($serviceRequest)->decryptString($secret->value))
        ->toBe('service-request-password');
});

test('Can view a service request', function (): void {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $contact = Contact::factory()->create();
    $serviceRequest = ServiceRequest::factory()->state([
        'respondent_id' => $contact->getKey(),
    ])->create();

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->callTableAction('view', $serviceRequest)
        ->assertSuccessful();
});

test('Can edit a service request', function (): void {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $contact = Contact::factory()->create();
    $openStatus = ServiceRequestStatus::factory()->open()->create();
    $serviceRequest = ServiceRequest::factory()->state([
        'respondent_id' => $contact->getKey(),
        'status_id' => $openStatus->getKey(),
    ])->create();

    $newStatus = ServiceRequestStatus::factory()->inProgress()->create();

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
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

test('Non-super admin can only see service requests from managed or audited types', function (): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $user = User::factory()->create();
    $department = Department::factory()->create();
    $user->department()->associate($department)->save();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');

    $managedType = ServiceRequestType::factory()->create();
    $managedType->managerDepartments()->attach($department);

    $auditedType = ServiceRequestType::factory()->create();
    $auditedType->auditorDepartments()->attach($department);

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

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->assertCanSeeTableRecords([$managedServiceRequest, $auditedServiceRequest])
        ->assertCanNotSeeTableRecords([$unmanagedServiceRequest]);
});

test('Non-super admin can only see service requests from directly managed or directly audited types', function (): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');

    $managedType = ServiceRequestType::factory()->create();
    $managedType->managerUsers()->attach($user);

    $auditedType = ServiceRequestType::factory()->create();
    $auditedType->auditorUsers()->attach($user);

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

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->assertCanSeeTableRecords([$managedServiceRequest, $auditedServiceRequest])
        ->assertCanNotSeeTableRecords([$unmanagedServiceRequest]);
});

test('Non-super admin can see service requests from group-managed and group-audited types', function (): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');

    $managedType = ServiceRequestType::factory()->create();
    $managerGroup = Group::factory()->create();
    $managerGroup->users()->attach($user);
    $managedType->managerGroups()->attach($managerGroup);

    $auditedType = ServiceRequestType::factory()->create();
    $auditorGroup = Group::factory()->create();
    $auditorGroup->users()->attach($user);
    $auditedType->auditorGroups()->attach($auditorGroup);

    $contact = Contact::factory()->create();

    $managedServiceRequest = ServiceRequest::factory()->state([
        'respondent_id' => $contact->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->state(['type_id' => $managedType->getKey()]),
    ])->create();

    $auditedServiceRequest = ServiceRequest::factory()->state([
        'respondent_id' => $contact->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->state(['type_id' => $auditedType->getKey()]),
    ])->create();

    actingAs($user);

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])->assertCanSeeTableRecords([$managedServiceRequest, $auditedServiceRequest]);
});

describe('feedback visibility', function (): void {
    it('shows the feedback summary when the `FeedbackManagement` feature is enabled', function (): void {
        asSuperAdmin();

        $contact = Contact::factory()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($contact, 'respondent')
            ->create();

        ServiceRequestFeedback::factory()
            ->for($serviceRequest, 'serviceRequest')
            ->for($contact, 'contact')
            ->create(['csat_answer' => 4, 'nps_answer' => 8]);

        Livewire::test(ServiceRequestsRelationManager::class, [
            'ownerRecord' => $contact,
            'pageClass' => ViewContact::class,
        ])
            ->mountTableAction('view', $serviceRequest)
            ->assertSchemaComponentVisible('feedback.csat_answer', 'mountedActionSchema0');
    });

    it('hides the feedback summary when the `FeedbackManagement` feature is disabled', function (): void {
        asSuperAdmin();

        $contact = Contact::factory()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($contact, 'respondent')
            ->create();

        ServiceRequestFeedback::factory()
            ->for($serviceRequest, 'serviceRequest')
            ->for($contact, 'contact')
            ->create(['csat_answer' => 4, 'nps_answer' => 8]);

        $settings = app(LicenseSettings::class);
        $settings->data->addons->feedbackManagement = false;
        $settings->save();

        Livewire::test(ServiceRequestsRelationManager::class, [
            'ownerRecord' => $contact,
            'pageClass' => ViewContact::class,
        ])
            ->mountTableAction('view', $serviceRequest)
            ->assertSchemaComponentHidden('feedback.csat_answer', 'mountedActionSchema0');
    });
});

describe('archiving', function (): void {
    test('the status select still offers the archived status a service request is already on', function (): void {
        asSuperAdmin();

        $settings = app(LicenseSettings::class);
        $settings->data->addons->serviceManagement = true;
        $settings->save();

        $archivedStatus = ServiceRequestStatus::factory()->archived()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ]);

        $contact = Contact::factory()->create();

        $serviceRequest = ServiceRequest::factory()
            ->for($contact, 'respondent')
            ->create(['status_id' => $archivedStatus->getKey()]);

        Livewire::test(ServiceRequestsRelationManager::class, [
            'ownerRecord' => $contact,
            'pageClass' => ViewContact::class,
        ])
            ->assertCanSeeTableRecords([$serviceRequest])
            ->mountTableAction('edit', $serviceRequest)
            ->assertFormFieldExists('status_id', 'mountedActionSchema0', function (Select $select) use ($archivedStatus): bool {
                $optionIds = collect($select->getOptions())
                    ->flatMap(fn (mixed $group): array => collect($group)->keys()->all())
                    ->all();

                return in_array($archivedStatus->getKey(), $optionIds, true);
            });
    });

    test('the status select does not offer archived statuses', function (): void {
        asSuperAdmin();

        $settings = app(LicenseSettings::class);
        $settings->data->addons->serviceManagement = true;
        $settings->save();

        $activeStatus = ServiceRequestStatus::factory()->create();
        $archivedStatus = ServiceRequestStatus::factory()->archived()->create();

        $contact = Contact::factory()->create();

        Livewire::test(ServiceRequestsRelationManager::class, [
            'ownerRecord' => $contact,
            'pageClass' => ViewContact::class,
        ])
            ->mountTableAction('create')
            ->assertFormFieldExists('status_id', 'mountedActionSchema0', function (Select $select) use ($activeStatus, $archivedStatus): bool {
                $optionIds = collect($select->getOptions())
                    ->flatMap(fn (mixed $group): array => collect($group)->keys()->all())
                    ->all();

                return in_array($activeStatus->getKey(), $optionIds, true)
                    && ! in_array($archivedStatus->getKey(), $optionIds, true);
            })
            ->assertSuccessful();
    });
});

test('ServiceRequestsRelationManager searches the assigned to column by full name, including a quoted phrase', function (string $search): void {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $contact = Contact::factory()->create();

    $matching = ServiceRequest::factory()->state(['respondent_id' => $contact->getKey()])->create();
    $other = ServiceRequest::factory()->state(['respondent_id' => $contact->getKey()])->create();

    foreach ([[$matching, 'Avery', 'Smith'], [$other, 'Jordan', 'Lee']] as [$serviceRequest, $firstName, $lastName]) {
        $assignee = User::factory()->create(['first_name' => $firstName, 'last_name' => $lastName]);

        $serviceRequest->priority->type->managerUsers()->attach($assignee);

        ServiceRequestAssignment::factory()->active()->create([
            'service_request_id' => $serviceRequest->getKey(),
            'user_id' => $assignee->getKey(),
        ]);
    }

    Livewire::test(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ViewContact::class,
    ])
        ->searchTable($search)
        ->assertCountTableRecords(1)
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$other]);
})->with([
    'first name' => 'Avery',
    'last name' => 'Smith',
    'full name' => 'Avery Smith',
    'quoted full name' => '"Avery Smith"',
]);
