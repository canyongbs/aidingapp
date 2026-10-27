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

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Models\Organization;
use AidingApp\Department\Models\Department;
use AidingApp\Form\Filament\Blocks\PasswordFormFieldBlock;
use AidingApp\ServiceManagement\Actions\RecordServiceRequestAssignmentRemovalHistory;
use AidingApp\ServiceManagement\Actions\ResolveServiceRequestSecretEncrypter;
use AidingApp\ServiceManagement\Enums\ServiceRequestTab;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateType;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages\ViewServiceRequest;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\AssignedToRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\AssignmentHistoryRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\CreatedByRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\ServiceRequestConversationsRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\ServiceRequestResource;
use AidingApp\ServiceManagement\Filament\Widgets\ServiceRequestMediaTable;
use AidingApp\ServiceManagement\Livewire\ServiceRequestUpdates;
use AidingApp\ServiceManagement\Models\Secret;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AidingApp\Timeline\Livewire\TimelineList;
use AidingApp\Timeline\Models\Timeline;
use App\Models\User;
use App\Settings\LicenseSettings;
use Carbon\CarbonImmutable;
use Filament\Actions\Testing\TestAction;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;
use function Tests\richContentText;

/**
 * A Service Request the given user may view, because they manage its type.
 */
$serviceRequestManagedBy = function (User $user): ServiceRequest {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');
    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managerUsers()->attach($user);

    return ServiceRequest::factory()
        ->for(
            ServiceRequestPriority::factory()->for($serviceRequestType, 'type'),
            'priority'
        )
        ->create();
};

/**
 * A Service Request whose type controls whether feedback collection is enabled.
 *
 * @param  array<string, mixed>  $attributes
 */
$serviceRequestWithFeedbackCollection = function (bool $enabled = true, SystemServiceRequestClassification $classification = SystemServiceRequestClassification::Closed, array $attributes = []): ServiceRequest {
    $type = ServiceRequestType::factory()->create([
        'has_enabled_feedback_collection' => $enabled,
    ]);

    return ServiceRequest::factory()
        ->for(ServiceRequestStatus::factory()->state(['classification' => $classification]), 'status')
        ->for(ServiceRequestPriority::factory()->for($type, 'type'), 'priority')
        ->create($attributes);
};

test('The correct details are displayed on the ViewServiceRequest page', function (): void {
    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin();

    $component = Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
        ->assertSuccessful();

    $expected = [
        'status.name' => $serviceRequest->status->name,
        'priority.name' => $serviceRequest->priority->name,
        'close_details' => $serviceRequest->close_details,
    ];

    expect(\Tests\infolistState($component->instance(), array_keys($expected)))->toEqual($expected)
        ->and(\Tests\htmlText($component->html(), '//section/header'))->toContain($serviceRequest->priority->type->name);
});

test('the customer contact organization is displayed when the contact has an organization', function (): void {
    $organization = Organization::factory()->create(['name' => 'Acme University']);
    $contactType = ContactType::factory()->create(['name' => 'Employee']);
    $contact = Contact::factory()->create([
        'organization_id' => $organization->getKey(),
        'type_id' => $contactType->getKey(),
    ]);
    $serviceRequest = ServiceRequest::factory()->create(['respondent_id' => $contact->getKey()]);

    asSuperAdmin();

    $component = Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
        ->assertSuccessful();
    $state = \Tests\infolistState($component->instance(), ['respondent']);

    expect(\Tests\htmlText((string) $state['respondent'], '//body'))
        ->toContain('Acme University', 'Employee')
        ->not()->toContain('(Employee)');
});

test('the customer contact organization is withheld when the contact has no organization', function (): void {
    $contact = Contact::factory()->create(['organization_id' => null]);
    $serviceRequest = ServiceRequest::factory()->create(['respondent_id' => $contact->getKey()]);

    asSuperAdmin();

    $component = Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
        ->assertSuccessful();
    $state = \Tests\infolistState($component->instance(), ['respondent']);

    expect(\Tests\htmlText((string) $state['respondent'], '//body'))->not()->toContain('Unaffiliated');
});

test('the status, priority, and type still display and remain editable when soft-deleted', function (): void {
    $serviceRequestType = ServiceRequestType::factory()->create();

    $status = ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::Open,
    ]);

    $priority = ServiceRequestPriority::factory()->for($serviceRequestType, 'type')->create();

    $serviceRequest = ServiceRequest::factory()->create([
        'status_id' => $status->getKey(),
        'priority_id' => $priority->getKey(),
    ]);

    $status->delete();
    $priority->delete();

    asSuperAdmin();

    $component = Livewire::test(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible(TestAction::make('editStatus')->schemaComponent('status.name'))
        ->assertActionVisible(TestAction::make('editPriority')->schemaComponent('priority.name'));

    expect(\Tests\infolistState($component->instance(), ['status.name', 'priority.name']))
        ->toBe(['status.name' => $status->name, 'priority.name' => $priority->name])
        ->and(\Tests\htmlText($component->html(), '//section/header'))->toContain($serviceRequestType->name);
});

test('The Description entry has Markdown rendering enabled on the underlying component', function (): void {
    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin();

    $descriptionEntry = Livewire::test(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSuccessful()
        ->instance()
        ->getSchema('infolist')
        ->getComponent(fn ($component): bool => $component instanceof TextEntry && $component->getName() === 'close_details');

    assert($descriptionEntry instanceof TextEntry);

    expect($descriptionEntry->isMarkdown())->toBeTrue();
});

// Permission Tests

test('ViewServiceRequest is gated with proper access control', function (): void {
    $user = User::factory()->create();

    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin($user);

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('view', [
                'record' => $serviceRequest,
            ])
        )->assertSuccessful();
});

test('ViewServiceRequest is gated with proper feature access control', function (): void {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->create();

    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin($user)
        ->get(
            ServiceRequestResource::getUrl('view', [
                'record' => $serviceRequest,
            ])
        )->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('view', [
                'record' => $serviceRequest,
            ])
        )->assertSuccessful();
});

test('service request lock icon is shown when status classification closed', function (): void {
    $user = User::factory()->create();

    asSuperAdmin($user);

    $serviceRequest = ServiceRequest::factory([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
        ])->getKey(),
    ])->create();

    Livewire::test(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSeeHtml('data-identifier="service_request_closed"');
});

test('service requests not authorized if user is not an auditor or manager of the service request type', function (): void {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');

    $user->refresh();

    actingAs($user);

    $serviceRequest = ServiceRequest::factory()
        ->create();

    Livewire::test(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertForbidden();
});

test('view service request page visible if the user is an auditor of the service request type', function (): void {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');

    $department = Department::factory()->create();

    $user->department()->associate($department)->save();

    $user->refresh();

    actingAs($user);

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->auditorDepartments()->attach($department);

    $serviceRequestsWithAuditor = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    Livewire::test(ViewServiceRequest::class, [
        'record' => $serviceRequestsWithAuditor->getRouteKey(),
    ])
        ->assertSuccessful();
});

test('view service request page visible if the user is a direct auditorUser of the service request type', function (): void {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');

    actingAs($user);

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->auditorUsers()->attach($user);

    $serviceRequestsWithAuditor = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    Livewire::test(ViewServiceRequest::class, [
        'record' => $serviceRequestsWithAuditor->getRouteKey(),
    ])
        ->assertSuccessful();
});

test('view service request page visible if the user is a manager of the service request type', function (): void {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');

    $department = Department::factory()->create();

    $user->department()->associate($department)->save();

    $user->refresh();

    actingAs($user);

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerDepartments()->attach($department);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    Livewire::test(ViewServiceRequest::class, [
        'record' => $serviceRequestsWithManager->getRouteKey(),
    ])
        ->assertSuccessful();
});

test('view service request page visible if the user is a direct managerUser of the service request type', function (): void {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');

    actingAs($user);

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managerUsers()->attach($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    Livewire::test(ViewServiceRequest::class, [
        'record' => $serviceRequestsWithManager->getRouteKey(),
    ])
        ->assertSuccessful();
});

test('ViewServiceRequest page displays the uploaded files on the files tab', function (): void {
    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin()
        ->get(ServiceRequestResource::getUrl('view', ['record' => $serviceRequest, 'tab' => ServiceRequestTab::Files->value]))
        ->assertSuccessful()
        ->assertSeeLivewire(ServiceRequestMediaTable::class);
});

describe('tabs', function () use ($serviceRequestManagedBy): void {
    it('opens on the request tab', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertSet('tab', ServiceRequestTab::Request->value);
    });

    it('falls back to the request tab when given an unknown tab', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        Livewire::test(ViewServiceRequest::class, [
            'record' => $serviceRequest->getRouteKey(),
            'tab' => 'not-a-tab',
        ])
            ->assertSuccessful()
            ->assertSet('tab', ServiceRequestTab::Request->value);
    });

    it('renders the relation managers for the assignments tab', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Assignments->value)
            ->assertSuccessful()
            ->assertSeeLivewire(AssignedToRelationManager::class)
            ->assertSeeLivewire(AssignmentHistoryRelationManager::class)
            ->assertSeeLivewire(CreatedByRelationManager::class);
    });

    it('renders the updates feed for the updates tab', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Updates->value)
            ->assertSuccessful()
            ->assertSeeLivewire(ServiceRequestUpdates::class);
    });

    it('refreshes the header status when an update changes the status', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();
        $newStatus = ServiceRequestStatus::factory()->create(['name' => 'Awaiting Vendor Reply']);

        $component = Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()]);

        expect(\Tests\infolistState($component->instance(), ['status.name']))
            ->toEqual(['status.name' => $serviceRequest->status->name]);

        $serviceRequest->update(['status_id' => $newStatus->getKey()]);

        $component->dispatch('service-request-status-changed');

        expect(\Tests\infolistState($component->instance(), ['status.name']))
            ->toEqual(['status.name' => $newStatus->name]);
    });

    it('renders the relation manager for the chats tab', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Chats->value)
            ->assertSuccessful()
            ->assertSeeLivewire(ServiceRequestConversationsRelationManager::class);
    });

    it('renders the timeline for the timeline tab', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Timeline->value)
            ->assertSuccessful()
            ->assertSeeLivewire(TimelineList::class);
    });

    it('shows standardized timeline update text and non-linked timeline titles', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();
        $customer = Contact::factory()->create([
            'first_name' => 'Bob',
            'last_name' => 'Smith',
        ]);
        $agent = User::factory()->create([
            'name' => 'Richard Down',
        ]);

        $customerUpdateAt = CarbonImmutable::create(2026, 2, 3, 23, 35, 0, 'UTC');
        $agentUpdateAt = CarbonImmutable::create(2026, 3, 2, 12, 20, 0, 'UTC');
        $aiUpdateAt = CarbonImmutable::create(2026, 3, 4, 9, 15, 0, 'UTC');

        $customerUpdate = ServiceRequestUpdate::factory()
            ->for($serviceRequest, 'serviceRequest')
            ->create([
                'update' => 'This is my update.',
                'internal' => false,
                'created_by_id' => $customer->getKey(),
                'created_by_type' => $customer->getMorphClass(),
                'created_at' => $customerUpdateAt,
                'updated_at' => $customerUpdateAt,
            ]);

        $agentUpdate = ServiceRequestUpdate::factory()
            ->for($serviceRequest, 'serviceRequest')
            ->create([
                'update' => 'This is also an update.',
                'internal' => false,
                'created_by_id' => $agent->getKey(),
                'created_by_type' => $agent->getMorphClass(),
                'created_at' => $agentUpdateAt,
                'updated_at' => $agentUpdateAt,
            ]);

        $aiUpdate = ServiceRequestUpdate::factory()
            ->for($serviceRequest, 'serviceRequest')
            ->create([
                'update' => 'AI generated update.',
                'update_type' => ServiceRequestUpdateType::AiResolutionProposed,
                'internal' => false,
                'created_by_id' => $serviceRequest->getKey(),
                'created_by_type' => $serviceRequest->getMorphClass(),
                'created_at' => $aiUpdateAt,
                'updated_at' => $aiUpdateAt,
            ]);

        $customerPostedAt = $customerUpdate->created_at?->format('M j, Y \\a\\t g:i A');
        $agentPostedAt = $agentUpdate->created_at?->format('M j, Y \\a\\t g:i A');
        $aiPostedAt = $aiUpdate->created_at?->format('M j, Y \\a\\t g:i A');

        $customerName = $customer->full_name;
        $agentName = $agent->name;
        $renderedTimeline = Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])->set('tab', ServiceRequestTab::Timeline->value)->assertSuccessful()->assertSeeLivewire(TimelineList::class);
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain("{$customerName} (Customer) posted the following update on {$customerPostedAt}:");
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain("{$agentName} (Agent) posted the following update on {$agentPostedAt}:");
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain("Assistant (AI) posted the following update on {$aiPostedAt}:");
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('This is my update.');
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('This is also an update.');
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('AI generated update.');
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li//a'))->not()->toContain('Customer Update Sent');
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li//a'))->not()->toContain('Service Request Assigned');
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li//a'))->not()->toContain('Service Request Reassigned');
    });

    it('shows a new assignment on the timeline', function () use ($serviceRequestManagedBy): void {
        $manager = User::factory()->create([
            'name' => 'Jane Doe',
        ]);
        $serviceRequest = $serviceRequestManagedBy($manager);

        asSuperAdmin();

        ServiceRequestAssignment::factory()
            ->active()
            ->for($serviceRequest, 'serviceRequest')
            ->for($manager, 'user')
            ->create();
        $renderedTimeline = Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])->set('tab', ServiceRequestTab::Timeline->value)->assertSuccessful();
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('Service Request Assigned');
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('Jane Doe');
    });

    it('shows a soft-deleted assignment on the timeline', function () use ($serviceRequestManagedBy): void {
        $manager = User::factory()->create([
            'name' => 'Jane Doe',
        ]);
        $serviceRequest = $serviceRequestManagedBy($manager);

        asSuperAdmin();

        $assignment = ServiceRequestAssignment::factory()
            ->active()
            ->for($serviceRequest, 'serviceRequest')
            ->for($manager, 'user')
            ->create();

        // A query-builder delete skips the observer, leaving the timeline row in place.
        ServiceRequestAssignment::query()->whereKey($assignment->getKey())->delete();

        expect(Timeline::query()->where('timelineable_id', $assignment->getKey())->exists())->toBeTrue()
            ->and($assignment->refresh()->trashed())->toBeTrue();
        $renderedTimeline = Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])->set('tab', ServiceRequestTab::Timeline->value)->assertSuccessful();
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('Service Request Assigned');
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('Jane Doe');
    });

    it('shows a fallback entry for an assignment that was permanently deleted', function () use ($serviceRequestManagedBy): void {
        $manager = User::factory()->create([
            'name' => 'Jane Doe',
        ]);
        $serviceRequest = $serviceRequestManagedBy($manager);

        asSuperAdmin();

        $assignment = ServiceRequestAssignment::factory()
            ->active()
            ->for($serviceRequest, 'serviceRequest')
            ->for($manager, 'user')
            ->create();

        ServiceRequestAssignment::query()->whereKey($assignment->getKey())->forceDelete();

        expect(Timeline::query()->where('timelineable_id', $assignment->getKey())->exists())->toBeTrue()
            ->and(ServiceRequestAssignment::withTrashed()->whereKey($assignment->getKey())->exists())->toBeFalse();
        $renderedTimeline = Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])->set('tab', ServiceRequestTab::Timeline->value)->assertSuccessful();
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('Service Request Assigned');
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('Assigned to Unknown user');
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->not()->toContain('Jane Doe');
    });

    it('shows a removed assignment on the timeline', function () use ($serviceRequestManagedBy): void {
        $manager = User::factory()->create([
            'name' => 'Jane Doe',
        ]);
        $serviceRequest = $serviceRequestManagedBy($manager);

        $actor = User::factory()->create([
            'name' => 'John Smith',
        ]);
        asSuperAdmin($actor);

        $assignment = ServiceRequestAssignment::factory()
            ->active()
            ->for($serviceRequest, 'serviceRequest')
            ->for($manager, 'user')
            ->create();

        app(RecordServiceRequestAssignmentRemovalHistory::class)($serviceRequest, $assignment);
        $renderedTimeline = Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])->set('tab', ServiceRequestTab::Timeline->value)->assertSuccessful();
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toMatch('~' . implode('.*', array_map(fn (string $value): string => preg_quote($value, '~'), ['Assignment Removed', 'Unassigned', 'Jane Doe', 'by', 'John Smith'])) . '~s');
    });

    it('shows submitted feedback on the timeline', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();
        $contact = Contact::factory()->create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'full_name' => 'Alice Smith',
        ]);

        ServiceRequestFeedback::factory()
            ->for($serviceRequest, 'serviceRequest')
            ->for($contact, 'contact')
            ->create([
                'csat_answer' => 4,
                'nps_answer' => 8,
            ]);
        $renderedTimeline = Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])->set('tab', ServiceRequestTab::Timeline->value)->assertSuccessful();
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('Feedback Submitted');
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('Alice Smith');
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('4');
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->toContain('8');
    });

    it('hides submitted feedback from the timeline when the `FeedbackManagement` feature is disabled', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();
        $contact = Contact::factory()->create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'full_name' => 'Alice Smith',
        ]);

        ServiceRequestFeedback::factory()
            ->for($serviceRequest, 'serviceRequest')
            ->for($contact, 'contact')
            ->create([
                'csat_answer' => 4,
                'nps_answer' => 8,
            ]);

        $settings = app(LicenseSettings::class);
        $settings->data->addons->feedbackManagement = false;
        $settings->save();
        $renderedTimeline = Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])->set('tab', ServiceRequestTab::Timeline->value)->assertSuccessful();
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->not()->toContain('Feedback Submitted');
        expect(\Tests\htmlText($renderedTimeline->html(), '//ol/li'))->not()->toContain('Alice Smith');
    });

    it('renders the media table for the files tab', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Files->value)
            ->assertSuccessful()
            ->assertSeeLivewire(ServiceRequestMediaTable::class);
    });

    it('does not render the other tabs while the request tab is active', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertDontSeeLivewire(ServiceRequestMediaTable::class)
            ->assertDontSeeLivewire(ServiceRequestUpdates::class)
            ->assertDontSeeLivewire(ServiceRequestConversationsRelationManager::class)
            ->assertDontSeeLivewire(TimelineList::class);
    });

    it('shows the form submission inline on the request tab', function (): void {
        asSuperAdmin();

        $form = ServiceRequestForm::factory()->create([
            'content' => richContentText('What do you need help with?'),
        ]);

        $submission = ServiceRequestFormSubmission::create([
            'service_request_form_id' => $form->getKey(),
            'submitted_at' => now(),
        ]);

        $serviceRequest = ServiceRequest::factory()
            ->for($submission, 'serviceRequestFormSubmission')
            ->create();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentVisible('request.serviceRequestFormSubmission');
    });

    it('shows who submitted the form and links to them', function (): void {
        asSuperAdmin();

        $author = Contact::factory()->create();

        $form = ServiceRequestForm::factory()->create([
            'content' => richContentText('What do you need help with?'),
        ]);

        $submission = ServiceRequestFormSubmission::create([
            'service_request_form_id' => $form->getKey(),
            'submitted_at' => now(),
        ]);

        $submission->author()->associate($author)->save();

        $serviceRequest = ServiceRequest::factory()
            ->for($submission, 'serviceRequestFormSubmission')
            ->create();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('request.submission_author_name', $author->full_name)
            ->assertSchemaComponentStateSet('request.serviceRequestFormSubmission.author.email', $author->email)
            ->assertSeeHtml(ContactResource::getUrl('view', ['record' => $author]))
            ->assertSee('Contact');
    });

    it('resolves the submitter name for a user author, not just a contact', function (): void {
        asSuperAdmin();

        $author = User::factory()->create(['name' => 'Dana Whitfield']);

        $form = ServiceRequestForm::factory()->create([
            'content' => richContentText('What do you need help with?'),
        ]);

        $submission = ServiceRequestFormSubmission::create([
            'service_request_form_id' => $form->getKey(),
            'submitted_at' => now(),
        ]);

        $submission->author()->associate($author)->save();

        $serviceRequest = ServiceRequest::factory()
            ->for($submission, 'serviceRequestFormSubmission')
            ->create();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('request.submission_author_name', 'Dana Whitfield');
    });

    it('falls back to a placeholder when the submission has no author', function (): void {
        asSuperAdmin();

        $form = ServiceRequestForm::factory()->create([
            'content' => richContentText('What do you need help with?'),
        ]);

        $submission = ServiceRequestFormSubmission::create([
            'service_request_form_id' => $form->getKey(),
            'submitted_at' => now(),
        ]);

        $serviceRequest = ServiceRequest::factory()
            ->for($submission, 'serviceRequestFormSubmission')
            ->create();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('request.submission_author_name', null);
    });

    it('hides the form details when there is no submission', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentHidden('request.serviceRequestFormSubmission');
    });
});

describe('password fields', function () use ($serviceRequestManagedBy): void {
    it('shows the reveal control inline to the assigned manager', function () use ($serviceRequestManagedBy): void {
        $assignedManager = user(permissions: [
            'service_request.view-any',
            'service_request.*.view',
            'service_request.*.update',
        ]);
        $serviceRequest = $serviceRequestManagedBy($assignedManager);
        $serviceRequest->secret_key = Crypt::encryptString(
            'base64:' . base64_encode(Encrypter::generateKey(config('app.cipher')))
        );
        $serviceRequest->save();

        ServiceRequestAssignment::factory()
            ->active()
            ->for($serviceRequest, 'serviceRequest')
            ->for($assignedManager, 'user')
            ->create();

        $secret = Secret::factory()
            ->for($serviceRequest, 'related')
            ->create([
                'value' => app(ResolveServiceRequestSecretEncrypter::class)($serviceRequest)
                    ->encryptString('service-request-password'),
            ]);

        $form = ServiceRequestForm::factory()->create();
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

        $submission = ServiceRequestFormSubmission::create([
            'service_request_form_id' => $form->getKey(),
            'submitted_at' => now(),
        ]);
        $submission->fields()->attach($field, [
            'id' => Str::orderedUuid(),
            'response' => $secret->getKey(),
        ]);

        $serviceRequest->serviceRequestFormSubmission()->associate($submission);
        $serviceRequest->save();

        actingAs($assignedManager);

        $component = Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful();

        expect(\Tests\htmlText($component->html(), '//*[@data-secret-row]'))->toContain('••••••••')
            ->and(\Tests\htmlText($component->html(), '//button[@data-secret-reveal]'))->toBe('Reveal')
            ->and(\Tests\htmlText($component->html(), '//*[@data-secret-row]/@class'))->toContain('fi-not-prose')
            ->and((string) $component->html())->not()->toContain('service-request-password')
            ->and(\Tests\htmlText($component->html(), '//section'))->not()->toContain('see Secrets section to reveal', 'Secrets');
    });

    it('renders an unanswered optional password field', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        $form = ServiceRequestForm::factory()->create();
        $field = new ServiceRequestFormField([
            'label' => 'Private credential',
            'type' => PasswordFormFieldBlock::type(),
            'is_required' => false,
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
                        'isRequired' => false,
                    ],
                ],
            ]],
        ]);
        $form->save();

        $submission = ServiceRequestFormSubmission::create([
            'service_request_form_id' => $form->getKey(),
            'submitted_at' => now(),
        ]);

        $serviceRequest->serviceRequestFormSubmission()->associate($submission);
        $serviceRequest->save();

        $component = Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful();

        expect(\Tests\htmlText($component->html(), '//section[.//span[normalize-space()="No response"]]'))
            ->toContain('Private credential', 'No response')
            ->not()->toContain('••••••••');
    });
});

describe('feedback tab', function () use ($serviceRequestWithFeedbackCollection): void {
    it('shows the csat and nps answers when feedback has been submitted', function () use ($serviceRequestWithFeedbackCollection): void {
        asSuperAdmin();

        $serviceRequest = $serviceRequestWithFeedbackCollection();

        ServiceRequestFeedback::factory()
            ->for($serviceRequest, 'serviceRequest')
            ->for(Contact::factory(), 'contact')
            ->create([
                'csat_answer' => 4,
                'nps_answer' => 3,
            ]);

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Feedback->value)
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('feedback.feedback.csat_answer', 4)
            ->assertSchemaComponentStateSet('feedback.feedback.nps_answer', 3)
            ->assertSchemaComponentHidden('feedback.feedback_notice');
    });

    it('explains that feedback is disabled for the service request type', function () use ($serviceRequestWithFeedbackCollection): void {
        asSuperAdmin();

        $serviceRequest = $serviceRequestWithFeedbackCollection(enabled: false);

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Feedback->value)
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('feedback.feedback_notice', __('service-management::service_requests.feedback.type_feedback_disabled'));
    });

    it('explains that the service request is not closed yet', function () use ($serviceRequestWithFeedbackCollection): void {
        asSuperAdmin();

        $serviceRequest = $serviceRequestWithFeedbackCollection(
            classification: SystemServiceRequestClassification::Open,
        );

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Feedback->value)
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('feedback.feedback_notice', __('service-management::service_requests.feedback.not_closed'));
    });

    it('explains that no survey was ever sent', function () use ($serviceRequestWithFeedbackCollection): void {
        // Disable the feature before creating the Service Request so the observer does not
        // dispatch SendClosedServiceFeedbackNotification, which would set survey_sent_at.
        // This replicates a Service Request closed before feedback management was enabled.
        $settings = app(LicenseSettings::class);
        $settings->data->addons->feedbackManagement = false;
        $settings->save();

        $serviceRequest = $serviceRequestWithFeedbackCollection();

        $settings->data->addons->feedbackManagement = true;
        $settings->save();

        asSuperAdmin();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Feedback->value)
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('feedback.feedback_notice', __('service-management::service_requests.feedback.no_survey_sent'));
    });

    it('reports when the survey was sent', function () use ($serviceRequestWithFeedbackCollection): void {
        asSuperAdmin();

        $serviceRequest = $serviceRequestWithFeedbackCollection(attributes: [
            'survey_sent_at' => now(),
            'reminder_sent_at' => null,
        ]);

        $component = Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Feedback->value)
            ->assertSuccessful();

        $state = \Tests\infolistState($component->instance(), ['feedback_notice']);

        expect($state['feedback_notice'])->toContain('Feedback survey was sent at')
            ->not()->toContain('Feedback survey reminder sent at');
    });

    it('reports when the reminder was sent', function () use ($serviceRequestWithFeedbackCollection): void {
        asSuperAdmin();

        $serviceRequest = $serviceRequestWithFeedbackCollection(attributes: [
            'survey_sent_at' => now()->subDay(),
            'reminder_sent_at' => now(),
        ]);

        $component = Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Feedback->value)
            ->assertSuccessful();

        $state = \Tests\infolistState($component->instance(), ['feedback_notice']);

        expect($state['feedback_notice'])->toContain('Feedback survey was sent at', 'Feedback survey reminder sent at');
    });
});

describe('authorization', function () use ($serviceRequestManagedBy): void {
    it('hides the feedback tab when the `FeedbackManagement` feature is disabled', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentVisible(ServiceRequestTab::Feedback->value);

        $settings = app(LicenseSettings::class);
        $settings->data->addons->feedbackManagement = false;
        $settings->save();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentHidden(ServiceRequestTab::Feedback->value);
    });

    it('hides the chats tab when the `RealtimeChat` feature is disabled', function (): void {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentVisible(ServiceRequestTab::Chats->value);

        $settings = app(LicenseSettings::class);
        $settings->data->addons->realtimeChat = false;
        $settings->save();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentHidden(ServiceRequestTab::Chats->value);
    });

    it('hides the updates tab from a user who cannot view service request updates', function () use ($serviceRequestManagedBy): void {
        $serviceRequest = $serviceRequestManagedBy($user = User::factory()->create());

        actingAs($user);

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentHidden(ServiceRequestTab::Updates->value);

        $user->givePermissionTo('service_request_update.view-any');

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentVisible(ServiceRequestTab::Updates->value);
    });

    it('hides the assignments tab from a user who cannot view any of its relation managers', function () use ($serviceRequestManagedBy): void {
        $serviceRequest = $serviceRequestManagedBy($user = User::factory()->create());

        actingAs($user);

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentHidden(ServiceRequestTab::Assignments->value);

        $user->givePermissionTo('service_request_assignment.view-any');

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentVisible(ServiceRequestTab::Assignments->value);
    });

    it('hides the timeline tab from a user without engagement permissions', function (): void {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->serviceManagement = true;
        $settings->save();

        $user = User::factory()->create();
        $user->givePermissionTo('service_request.view-any');
        $user->givePermissionTo('service_request.*.view');

        actingAs($user);

        $serviceRequestType = ServiceRequestType::factory()->create();
        $serviceRequestType->managerUsers()->attach($user);

        $serviceRequest = ServiceRequest::factory()->state([
            'priority_id' => ServiceRequestPriority::factory()->create([
                'type_id' => $serviceRequestType->getKey(),
            ])->getKey(),
        ])->create();

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentHidden(ServiceRequestTab::Timeline->value);

        $user->givePermissionTo('engagement.view-any');
        $user->givePermissionTo('engagement.*.view');

        Livewire::test(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentVisible(ServiceRequestTab::Timeline->value);
    });
});
