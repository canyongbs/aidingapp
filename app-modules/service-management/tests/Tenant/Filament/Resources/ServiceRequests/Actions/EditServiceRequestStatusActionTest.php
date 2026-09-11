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

use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages\ViewServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailPreference;
use AidingApp\ServiceManagement\Notifications\SendClosedServiceFeedbackNotification;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

// Authorization

test('editStatus action is visible for manager department member with update permission', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create();
    $user->department()->associate($department)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managerDepartments()->attach($department);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');
    $user->givePermissionTo('service_request.*.update');

    actingAs($user->refresh());

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible(TestAction::make('editStatus')->schemaComponent('status.name'));
});

test('editStatus action is hidden for user without update permission', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create();
    $user->department()->associate($department)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managerDepartments()->attach($department);
    $serviceRequestType->auditorDepartments()->attach($department);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');

    actingAs($user->refresh());

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionHidden(TestAction::make('editStatus')->schemaComponent('status.name'));
});

test('editStatus action is hidden for a department auditor who is not a manager of the service request type', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create();
    $user->department()->associate($department)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->auditorDepartments()->attach($department);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');
    $user->givePermissionTo('service_request.*.update');

    actingAs($user->refresh());

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionHidden(TestAction::make('editStatus')->schemaComponent('status.name'));
});

test('editStatus action is hidden for a direct auditor user who is not a manager of the service request type', function () {
    $user = User::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->auditorUsers()->attach($user);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');
    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionHidden(TestAction::make('editStatus')->schemaComponent('status.name'));
});

test('editStatus action is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = false;
    $settings->save();

    $user = User::factory()->create();

    $department = Department::factory()->create();
    $user->department()->associate($department)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managerDepartments()->attach($department);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');
    $user->givePermissionTo('service_request.*.update');

    actingAs($user->refresh());

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])->assertForbidden();

    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $newStatus = ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::InProgress,
    ]);

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSuccessful()
        ->callAction(TestAction::make('editStatus')->schemaComponent('status.name'), data: [
            'status_id' => $newStatus->getKey(),
        ])
        ->assertHasNoFormErrors();

    expect($serviceRequest->fresh()->status_id)->toBe($newStatus->getKey());
});

test('editStatus action is gated with proper feature access control for direct user manager', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = false;
    $settings->save();

    $user = User::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managerUsers()->attach($user);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');
    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])->assertForbidden();

    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $newStatus = ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::InProgress,
    ]);

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSuccessful()
        ->callAction(TestAction::make('editStatus')->schemaComponent('status.name'), data: [
            'status_id' => $newStatus->getKey(),
        ])
        ->assertHasNoFormErrors();

    expect($serviceRequest->fresh()->status_id)->toBe($newStatus->getKey());
});

// Validation

test('editStatus requires status_id', function () {
    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
    ])->create();

    asSuperAdmin();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->callAction(TestAction::make('editStatus')->schemaComponent('status.name'), data: [
            'status_id' => null,
        ])
        ->assertHasFormErrors(['status_id' => 'required']);
});

// Success

test('can update the service request status', function () {
    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
    ])->create();

    $newStatus = ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::InProgress,
    ]);

    asSuperAdmin();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->callAction(TestAction::make('editStatus')->schemaComponent('status.name'), data: [
            'status_id' => $newStatus->getKey(),
        ])
        ->assertHasNoFormErrors()
        ->assertNotified();

    expect($serviceRequest->fresh()->status_id)->toBe($newStatus->getKey());
});

test('send feedback email if service request is closed', function () {
    Notification::fake();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->feedbackManagement = true;
    $settings->save();

    $user = User::factory()->create();

    $department = Department::factory()->create();
    $user->department()->associate($department)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create([
        'has_enabled_feedback_collection' => true,
        'has_enabled_csat' => true,
        'has_enabled_nps' => true,
    ]);

    ServiceRequestTypeEmailPreference::create([
        'service_request_type_id' => $serviceRequestType->getKey(),
        'service_request_email_template_type' => ServiceRequestEmailTemplateType::SurveyResponse,
        'service_request_email_template_role' => ServiceRequestTypeEmailTemplateRole::Customer,
        'notification_channel' => ServiceRequestNotificationChannel::Email,
        'is_enabled' => true,
    ]);

    $serviceRequestType->managerDepartments()->attach($department);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');
    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    $closedStatus = ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::Closed,
    ]);

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->callAction(TestAction::make('editStatus')->schemaComponent('status.name'), data: [
            'status_id' => $closedStatus->getKey(),
        ])
        ->assertHasNoFormErrors();

    $serviceRequest->refresh();

    Notification::assertSentTo(
        $serviceRequest->respondent,
        SendClosedServiceFeedbackNotification::class
    );

    Notification::assertNotSentTo(
        [$user],
        SendClosedServiceFeedbackNotification::class
    );
});

test('send feedback email if service request is closed for direct user manager', function () {
    Notification::fake();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->feedbackManagement = true;
    $settings->save();

    $user = User::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create([
        'has_enabled_feedback_collection' => true,
        'has_enabled_csat' => true,
        'has_enabled_nps' => true,
    ]);

    ServiceRequestTypeEmailPreference::create([
        'service_request_type_id' => $serviceRequestType->getKey(),
        'service_request_email_template_type' => ServiceRequestEmailTemplateType::SurveyResponse,
        'service_request_email_template_role' => ServiceRequestTypeEmailTemplateRole::Customer,
        'notification_channel' => ServiceRequestNotificationChannel::Email,
        'is_enabled' => true,
    ]);

    $serviceRequestType->managerUsers()->attach($user);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');
    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    $closedStatus = ServiceRequestStatus::factory()->create([
        'classification' => SystemServiceRequestClassification::Closed,
    ]);

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->callAction(TestAction::make('editStatus')->schemaComponent('status.name'), data: [
            'status_id' => $closedStatus->getKey(),
        ])
        ->assertHasNoFormErrors();

    $serviceRequest->refresh();

    Notification::assertSentTo(
        $serviceRequest->respondent,
        SendClosedServiceFeedbackNotification::class
    );

    Notification::assertNotSentTo(
        [$user],
        SendClosedServiceFeedbackNotification::class
    );
});
