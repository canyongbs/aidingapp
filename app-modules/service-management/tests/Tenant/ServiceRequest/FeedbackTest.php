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
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\ServiceRequestResource;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages\Feedback;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages\ViewServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

// ---------------------------------------------------------------------------
// Access-control tests
// ---------------------------------------------------------------------------

test('Feedback is gated with proper access control', function () {
    $user = User::factory()->create();

    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin($user);

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('manage-feedback', [
                'record' => $serviceRequest,
            ])
        )->assertSuccessful();
});

test('Feedback is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->feedbackManagement = false;

    $settings->save();

    $user = User::factory()->create();

    $serviceRequest = ServiceRequest::factory()->create();

    asSuperAdmin($user)
        ->get(
            ServiceRequestResource::getUrl('manage-feedback', [
                'record' => $serviceRequest,
            ])
        )->assertForbidden();

    $settings->data->addons->feedbackManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('manage-feedback', [
                'record' => $serviceRequest,
            ])
        )->assertSuccessful();
});

// ---------------------------------------------------------------------------
// Navigation tab visibility tests
// ---------------------------------------------------------------------------

test('feedback navigation tab is not shown when feedback management feature is disabled', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->feedbackManagement = false;

    $settings->save();

    $user = User::factory()->create();

    asSuperAdmin($user);

    $serviceRequest = ServiceRequest::factory()->create();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])->assertDontSeeHtml('manage-feedback');
});

test('feedback navigation tab is shown when feedback management feature is enabled', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->feedbackManagement = true;

    $settings->save();

    $user = User::factory()->create();

    asSuperAdmin($user);

    $serviceRequest = ServiceRequest::factory()->create();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])->assertSeeHtml('manage-feedback');
});

// ---------------------------------------------------------------------------
// Message-display tests
// ---------------------------------------------------------------------------

test('feedback page shows type feedback disabled message when type has feedback collection disabled', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->feedbackManagement = true;

    $settings->save();

    $user = User::factory()->create();

    asSuperAdmin($user);

    $type = ServiceRequestType::factory()->create([
        'has_enabled_feedback_collection' => false,
    ]);

    $serviceRequest = ServiceRequest::factory()->create([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $type->getKey(),
        ])->getKey(),
    ]);

    livewire(Feedback::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])->assertSee(__('service-management::service_requests.feedback.type_feedback_disabled'));
});

test('feedback page shows not closed message when service request is not resolved', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->feedbackManagement = true;

    $settings->save();

    $user = User::factory()->create();

    asSuperAdmin($user);

    $type = ServiceRequestType::factory()->create([
        'has_enabled_feedback_collection' => true,
    ]);

    $serviceRequest = ServiceRequest::factory()->create([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $type->getKey(),
        ])->getKey(),
    ]);

    livewire(Feedback::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])->assertSee(__('service-management::service_requests.feedback.not_closed'));
});

test('feedback page shows no survey sent message when service request is closed with no survey sent', function () {
    $settings = app(LicenseSettings::class);

    // Disable the feature before creating the SR so the observer does not dispatch
    // SendClosedServiceFeedbackNotification (which would set survey_sent_at = now()).
    // This replicates a real scenario: the SR was closed before feedback management
    // was enabled, so no survey was ever sent.
    $settings->data->addons->feedbackManagement = false;

    $settings->save();

    $type = ServiceRequestType::factory()->create([
        'has_enabled_feedback_collection' => true,
    ]);

    $serviceRequest = ServiceRequest::factory()->create([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $type->getKey(),
        ])->getKey(),
    ]);

    $settings->data->addons->feedbackManagement = true;

    $settings->save();

    $user = User::factory()->create();

    asSuperAdmin($user);

    livewire(Feedback::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])->assertSee(__('service-management::service_requests.feedback.no_survey_sent'));
});

test('feedback page shows survey sent message when service request is closed and survey was sent', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->feedbackManagement = true;

    $settings->save();

    $user = User::factory()->create();

    asSuperAdmin($user);

    $type = ServiceRequestType::factory()->create([
        'has_enabled_feedback_collection' => true,
    ]);

    $serviceRequest = ServiceRequest::factory()->create([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $type->getKey(),
        ])->getKey(),
        'survey_sent_at' => now(),
        'reminder_sent_at' => null,
    ]);

    livewire(Feedback::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSee('Feedback survey was sent at')
        ->assertDontSee('Feedback survey reminder sent at');
});

test('feedback page shows reminder sent message when service request is closed and reminder was sent', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->feedbackManagement = true;

    $settings->save();

    $user = User::factory()->create();

    asSuperAdmin($user);

    $type = ServiceRequestType::factory()->create([
        'has_enabled_feedback_collection' => true,
    ]);

    $serviceRequest = ServiceRequest::factory()->create([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $type->getKey(),
        ])->getKey(),
        'survey_sent_at' => now()->subDay(),
        'reminder_sent_at' => now(),
    ]);

    livewire(Feedback::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSee('Feedback survey was sent at')
        ->assertSee('Feedback survey reminder sent at');
});

test('feedback page shows csat and nps answers when feedback has been submitted', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->feedbackManagement = true;

    $settings->save();

    $user = User::factory()->create();

    asSuperAdmin($user);

    $type = ServiceRequestType::factory()->create([
        'has_enabled_feedback_collection' => true,
    ]);

    $serviceRequest = ServiceRequest::factory()->create([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $type->getKey(),
        ])->getKey(),
    ]);

    ServiceRequestFeedback::factory()->create([
        'service_request_id' => $serviceRequest->getKey(),
        'contact_id' => Contact::factory()->create()->getKey(),
        'csat_answer' => 4,
        'nps_answer' => 3,
    ]);

    livewire(Feedback::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSee('Customer Satisfaction (CSAT)')
        ->assertSee('Net Promoter Score (NPS)')
        ->assertDontSee(__('service-management::service_requests.feedback.no_survey_sent'))
        ->assertDontSee(__('service-management::service_requests.feedback.not_closed'));
});
