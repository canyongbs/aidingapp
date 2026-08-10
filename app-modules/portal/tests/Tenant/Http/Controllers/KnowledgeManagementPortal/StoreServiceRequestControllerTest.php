<?php

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
