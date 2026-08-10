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
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Features\DefaultPriorityFeature;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->save();

    ServiceRequestStatus::firstOrCreate(
        ['name' => 'New'],
        ServiceRequestStatus::factory()->open()->systemProtected()->make()->getAttributes(),
    );
});

it('assigns the configured default priority instead of the submitted priority', function () {
    $type = ServiceRequestType::factory()->create();
    $defaultPriority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $submittedPriority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $type->update(['default_priority_id' => $defaultPriority->getKey()]);

    $contact = Contact::factory()->create();

    $response = actingAs($contact, 'contact')->postJson(
        route('api.portal.service-request.store', ['type' => $type]),
        [
            'Main' => [
                'title' => 'Portal priority test',
                'description' => 'Configured priority must take precedence.',
                'priority' => $submittedPriority->getKey(),
            ],
        ],
    );

    $response->assertOk();

    assertDatabaseHas(ServiceRequest::class, [
        'priority_id' => $defaultPriority->getKey(),
        'respondent_id' => $contact->getKey(),
    ]);
});

it('requires a submitted priority before the default priority feature is activated', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $type->update(['default_priority_id' => $priority->getKey()]);
    $contact = Contact::factory()->create();
    DefaultPriorityFeature::deactivate();

    try {
        $response = actingAs($contact, 'contact')->postJson(
            route('api.portal.service-request.store', ['type' => $type]),
            ['Main' => ['title' => 'Portal priority test', 'description' => 'A priority is required.']],
        );

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('Main.priority');
    } finally {
        DefaultPriorityFeature::activate();
    }
});
