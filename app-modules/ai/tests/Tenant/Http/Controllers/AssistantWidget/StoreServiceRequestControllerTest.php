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
    $portalSettings->ai_support_assistant = true;
    $portalSettings->save();

    ServiceRequestStatus::firstOrCreate(
        ['name' => 'New'],
        ServiceRequestStatus::factory()->open()->systemProtected()->make()->getAttributes(),
    );
});

function postAssistantServiceRequest(Contact $contact, ServiceRequestType $type, array $data)
{
    $contact->createToken('assistant-widget-access-token');

    return actingAs($contact, 'contact')->postJson(
        route('widgets.assistant.api.service-request.store', ['type' => $type]),
        $data,
        ['Origin' => config('app.url')],
    );
}

it('assigns the configured default priority when no priority is submitted', function () {
    $type = ServiceRequestType::factory()->create();
    $defaultPriority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $type->update(['default_priority_id' => $defaultPriority->getKey()]);
    $contact = Contact::factory()->create();

    $response = postAssistantServiceRequest($contact, $type, [
        'title' => 'Assistant priority test',
        'description' => 'Configured priority should be assigned.',
    ]);

    $response->assertOk();

    assertDatabaseHas(ServiceRequest::class, [
        'priority_id' => $defaultPriority->getKey(),
        'respondent_id' => $contact->getKey(),
    ]);
});

it('requires a priority when the default priority feature is inactive', function () {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $type->update(['default_priority_id' => $priority->getKey()]);
    $contact = Contact::factory()->create();
    DefaultPriorityFeature::deactivate();

    try {
        $response = postAssistantServiceRequest($contact, $type, [
            'title' => 'Assistant priority test',
            'description' => 'A priority is required.',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('priority_id');
    } finally {
        DefaultPriorityFeature::activate();
    }
});
