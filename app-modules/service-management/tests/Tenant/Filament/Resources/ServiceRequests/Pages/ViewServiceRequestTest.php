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
use AidingApp\Department\Models\Department;
use AidingApp\Division\Models\Division;
use AidingApp\ServiceManagement\Enums\ServiceRequestTab;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages\ViewServiceRequest;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\AssignedToRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\AssignmentHistoryRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\CreatedByRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\ServiceRequestConversationsRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\ServiceRequestUpdatesRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\ServiceRequestResource;
use AidingApp\ServiceManagement\Filament\Widgets\ServiceRequestMediaTable;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Timeline\Livewire\TimelineList;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Infolists\Components\TextEntry;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;
use function Tests\richContentText;

/**
 * A Service Request the given user may view, because they manage its type.
 */
function serviceRequestManagedBy(User $user): ServiceRequest
{
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
}

/**
 * A Service Request whose type controls whether feedback collection is enabled.
 *
 * @param  array<string, mixed>  $attributes
 */
function serviceRequestWithFeedbackCollection(
    bool $enabled = true,
    SystemServiceRequestClassification $classification = SystemServiceRequestClassification::Closed,
    array $attributes = [],
): ServiceRequest {
    $type = ServiceRequestType::factory()->create([
        'has_enabled_feedback_collection' => $enabled,
    ]);

    return ServiceRequest::factory()
        ->for(ServiceRequestStatus::factory()->state(['classification' => $classification]), 'status')
        ->for(ServiceRequestPriority::factory()->for($type, 'type'), 'priority')
        ->create($attributes);
}

test('The correct details are displayed on the ViewServiceRequest page', function () {
    Division::factory()->count(2)->create();
    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestResource::getUrl('view', [
                'record' => $serviceRequest,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Type',
                $serviceRequest->priority->type->name,
                'Division',
                $serviceRequest->division->name,
                'Status',
                $serviceRequest->status->name,
                'Priority',
                $serviceRequest->priority->name,
                'Description',
                $serviceRequest->close_details,
            ]
        );
});

test('The Description entry has Markdown rendering enabled on the underlying component', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin();

    $descriptionEntry = livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSuccessful()
        ->instance()
        ->getSchema('infolist')
        ->getComponent(fn ($component): bool => $component instanceof TextEntry && $component->getName() === 'close_details');

    expect($descriptionEntry)
        ->not->toBeNull()
        ->isMarkdown()->toBeTrue();
});

// Permission Tests

test('ViewServiceRequest is gated with proper access control', function () {
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

test('ViewServiceRequest is gated with proper feature access control', function () {
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

test('service request lock icon is shown when status classification closed', function () {
    $user = User::factory()->create();

    asSuperAdmin($user);

    $serviceRequest = ServiceRequest::factory([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
        ])->getKey(),
    ])->create();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSeeHtml('data-identifier="service_request_closed"');
});

test('service requests not authorized if user is not an auditor or manager of the service request type', function () {
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

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertForbidden();
});

test('view service request page visible if the user is an auditor of the service request type', function () {
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

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequestsWithAuditor->getRouteKey(),
    ])
        ->assertSuccessful();
});

test('view service request page visible if the user is a direct auditorUser of the service request type', function () {
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

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequestsWithAuditor->getRouteKey(),
    ])
        ->assertSuccessful();
});

test('view service request page visible if the user is a manager of the service request type', function () {
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

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequestsWithManager->getRouteKey(),
    ])
        ->assertSuccessful();
});

test('view service request page visible if the user is a direct managerUser of the service request type', function () {
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

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequestsWithManager->getRouteKey(),
    ])
        ->assertSuccessful();
});

test('ViewServiceRequest page displays the uploaded files on the files tab', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin()
        ->get(ServiceRequestResource::getUrl('view', ['record' => $serviceRequest, 'tab' => ServiceRequestTab::Files->value]))
        ->assertSuccessful()
        ->assertSeeLivewire(ServiceRequestMediaTable::class);
});

describe('tabs', function () {
    it('opens on the request tab', function () {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertSet('tab', ServiceRequestTab::Request->value);
    });

    it('falls back to the request tab when given an unknown tab', function () {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        livewire(ViewServiceRequest::class, [
            'record' => $serviceRequest->getRouteKey(),
            'tab' => 'not-a-tab',
        ])
            ->assertSuccessful()
            ->assertSet('tab', ServiceRequestTab::Request->value);
    });

    it('renders the relation managers for the assignments tab', function () {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Assignments->value)
            ->assertSuccessful()
            ->assertSeeLivewire(AssignedToRelationManager::class)
            ->assertSeeLivewire(AssignmentHistoryRelationManager::class)
            ->assertSeeLivewire(CreatedByRelationManager::class);
    });

    it('renders the relation manager for the updates tab', function () {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Updates->value)
            ->assertSuccessful()
            ->assertSeeLivewire(ServiceRequestUpdatesRelationManager::class);
    });

    it('renders the relation manager for the chats tab', function () {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Chats->value)
            ->assertSuccessful()
            ->assertSeeLivewire(ServiceRequestConversationsRelationManager::class);
    });

    it('renders the timeline for the timeline tab', function () {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Timeline->value)
            ->assertSuccessful()
            ->assertSeeLivewire(TimelineList::class);
    });

    it('renders the media table for the files tab', function () {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Files->value)
            ->assertSuccessful()
            ->assertSeeLivewire(ServiceRequestMediaTable::class);
    });

    it('does not render the other tabs while the request tab is active', function () {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertDontSeeLivewire(ServiceRequestMediaTable::class)
            ->assertDontSeeLivewire(ServiceRequestUpdatesRelationManager::class)
            ->assertDontSeeLivewire(ServiceRequestConversationsRelationManager::class)
            ->assertDontSeeLivewire(TimelineList::class);
    });

    it('shows the form submission inline on the request tab', function () {
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

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentVisible('request.serviceRequestFormSubmission');
    });

    it('shows who submitted the form and links to them', function () {
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

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('request.submission_author_name', $author->full_name)
            ->assertSchemaComponentStateSet('request.serviceRequestFormSubmission.author.email', $author->email)
            ->assertSeeHtml(ContactResource::getUrl('view', ['record' => $author]))
            ->assertSee('Contact');
    });

    it('resolves the submitter name for a user author, not just a contact', function () {
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

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('request.submission_author_name', 'Dana Whitfield');
    });

    it('falls back to a placeholder when the submission has no author', function () {
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

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('request.submission_author_name', null);
    });

    it('hides the form details when there is no submission', function () {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentHidden('request.serviceRequestFormSubmission');
    });
});

describe('division', function () {
    it('shows the division when the tenant has more than one', function () {
        asSuperAdmin();

        Division::factory()->count(2)->create();

        $serviceRequest = ServiceRequest::factory()->create();

        expect($serviceRequest->division)->not->toBeNull();

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentVisible('division.name')
            ->assertSchemaComponentStateSet('division.name', $serviceRequest->division->name);
    });

    it('hides the division when the tenant only has one', function () {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        Division::query()->whereKeyNot($serviceRequest->division_id)->delete();

        expect(Division::count())->toBe(1);

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentHidden('division.name');
    });

    it('hides the division when the service request has none', function () {
        asSuperAdmin();

        Division::factory()->count(2)->create();

        $serviceRequest = ServiceRequest::factory()->create();
        $serviceRequest->division()->disassociate()->saveQuietly();

        expect(Division::count())->toBeGreaterThan(1)
            ->and($serviceRequest->fresh()->division)->toBeNull();

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentHidden('division.name');
    });
});

describe('feedback tab', function () {
    it('shows the csat and nps answers when feedback has been submitted', function () {
        asSuperAdmin();

        $serviceRequest = serviceRequestWithFeedbackCollection();

        ServiceRequestFeedback::factory()
            ->for($serviceRequest, 'serviceRequest')
            ->for(Contact::factory(), 'contact')
            ->create([
                'csat_answer' => 4,
                'nps_answer' => 3,
            ]);

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Feedback->value)
            ->assertSuccessful()
            ->assertSchemaComponentStateSet('feedback.feedback.csat_answer', 4)
            ->assertSchemaComponentStateSet('feedback.feedback.nps_answer', 3)
            ->assertDontSee(__('service-management::service_requests.feedback.no_survey_sent'))
            ->assertDontSee(__('service-management::service_requests.feedback.not_closed'));
    });

    it('explains that feedback is disabled for the service request type', function () {
        asSuperAdmin();

        $serviceRequest = serviceRequestWithFeedbackCollection(enabled: false);

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Feedback->value)
            ->assertSuccessful()
            ->assertSee(__('service-management::service_requests.feedback.type_feedback_disabled'));
    });

    it('explains that the service request is not closed yet', function () {
        asSuperAdmin();

        $serviceRequest = serviceRequestWithFeedbackCollection(
            classification: SystemServiceRequestClassification::Open,
        );

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Feedback->value)
            ->assertSuccessful()
            ->assertSee(__('service-management::service_requests.feedback.not_closed'));
    });

    it('explains that no survey was ever sent', function () {
        // Disable the feature before creating the Service Request so the observer does not
        // dispatch SendClosedServiceFeedbackNotification, which would set survey_sent_at.
        // This replicates a Service Request closed before feedback management was enabled.
        $settings = app(LicenseSettings::class);
        $settings->data->addons->feedbackManagement = false;
        $settings->save();

        $serviceRequest = serviceRequestWithFeedbackCollection();

        $settings->data->addons->feedbackManagement = true;
        $settings->save();

        asSuperAdmin();

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Feedback->value)
            ->assertSuccessful()
            ->assertSee(__('service-management::service_requests.feedback.no_survey_sent'));
    });

    it('reports when the survey was sent', function () {
        asSuperAdmin();

        $serviceRequest = serviceRequestWithFeedbackCollection(attributes: [
            'survey_sent_at' => now(),
            'reminder_sent_at' => null,
        ]);

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Feedback->value)
            ->assertSuccessful()
            ->assertSee('Feedback survey was sent at')
            ->assertDontSee('Feedback survey reminder sent at');
    });

    it('reports when the reminder was sent', function () {
        asSuperAdmin();

        $serviceRequest = serviceRequestWithFeedbackCollection(attributes: [
            'survey_sent_at' => now()->subDay(),
            'reminder_sent_at' => now(),
        ]);

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->set('tab', ServiceRequestTab::Feedback->value)
            ->assertSuccessful()
            ->assertSee('Feedback survey was sent at')
            ->assertSee('Feedback survey reminder sent at');
    });
});

describe('authorization', function () {
    it('hides the feedback tab when the `FeedbackManagement` feature is disabled', function () {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentVisible(ServiceRequestTab::Feedback->value);

        $settings = app(LicenseSettings::class);
        $settings->data->addons->feedbackManagement = false;
        $settings->save();

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentHidden(ServiceRequestTab::Feedback->value);
    });

    it('hides the chats tab when the `RealtimeChat` feature is disabled', function () {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentVisible(ServiceRequestTab::Chats->value);

        $settings = app(LicenseSettings::class);
        $settings->data->addons->realtimeChat = false;
        $settings->save();

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentHidden(ServiceRequestTab::Chats->value);
    });

    it('hides the updates tab from a user who cannot view service request updates', function () {
        $serviceRequest = serviceRequestManagedBy($user = User::factory()->create());

        actingAs($user);

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentHidden(ServiceRequestTab::Updates->value);

        $user->givePermissionTo('service_request_update.view-any');

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentVisible(ServiceRequestTab::Updates->value);
    });

    it('hides the assignments tab from a user who cannot view any of its relation managers', function () {
        $serviceRequest = serviceRequestManagedBy($user = User::factory()->create());

        actingAs($user);

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentHidden(ServiceRequestTab::Assignments->value);

        $user->givePermissionTo('service_request_assignment.view-any');

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentVisible(ServiceRequestTab::Assignments->value);
    });

    it('hides the timeline tab from a user without engagement permissions', function () {
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

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentHidden(ServiceRequestTab::Timeline->value);

        $user->givePermissionTo('engagement.view-any');
        $user->givePermissionTo('engagement.*.view');

        livewire(ViewServiceRequest::class, ['record' => $serviceRequest->getRouteKey()])
            ->assertSchemaComponentVisible(ServiceRequestTab::Timeline->value);
    });
});
