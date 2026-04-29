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
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Settings\LicenseSettings;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

function enableFeedbackFeatures(): void
{
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->data->addons->feedbackManagement = true;
    $settings->save();
}

function createServiceRequestWithFeedbackEnabled(Contact $respondent): ServiceRequest
{
    $type = ServiceRequestType::factory()->create([
        'has_enabled_feedback_collection' => true,
        'has_enabled_csat' => true,
        'has_enabled_nps' => true,
    ]);

    return ServiceRequest::factory()->create([
        'respondent_id' => $respondent->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $type->getKey(),
        ])->getKey(),
    ]);
}

test('respondent contact can view feedback entry for their own service request', function () {
    enableFeedbackFeatures();

    $respondent = Contact::factory()->create();
    $serviceRequest = createServiceRequestWithFeedbackEnabled($respondent);

    Sanctum::actingAs($respondent, [], 'contact');

    getJson(
        route('widgets.service-requests.feedback.api.entry', [
            'serviceRequest' => $serviceRequest,
        ])
    )->assertOk();
});

test('non-respondent contact is forbidden from viewing feedback entry', function () {
    enableFeedbackFeatures();

    $respondent = Contact::factory()->create();
    $otherContact = Contact::factory()->create();
    $serviceRequest = createServiceRequestWithFeedbackEnabled($respondent);

    Sanctum::actingAs($otherContact, [], 'contact');

    getJson(
        route('widgets.service-requests.feedback.api.entry', [
            'serviceRequest' => $serviceRequest,
        ])
    )->assertForbidden();
});

test('respondent contact can submit feedback for their own service request', function () {
    enableFeedbackFeatures();

    $respondent = Contact::factory()->create();
    $serviceRequest = createServiceRequestWithFeedbackEnabled($respondent);

    Sanctum::actingAs($respondent, [], 'contact');

    postJson(
        route('widgets.service-requests.feedback.api.submit', [
            'serviceRequest' => $serviceRequest,
        ]),
        ['csat' => 4, 'nps' => 5]
    )->assertOk()
        ->assertJsonPath('message', 'Service Request feedback submitted successfully.');
});

test('non-respondent contact is forbidden from submitting feedback', function () {
    enableFeedbackFeatures();

    $respondent = Contact::factory()->create();
    $otherContact = Contact::factory()->create();
    $serviceRequest = createServiceRequestWithFeedbackEnabled($respondent);

    Sanctum::actingAs($otherContact, [], 'contact');

    postJson(
        route('widgets.service-requests.feedback.api.submit', [
            'serviceRequest' => $serviceRequest,
        ]),
        ['csat' => 4, 'nps' => 5]
    )->assertForbidden();
});
