<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Models\Incident;
use AidingApp\ServiceManagement\Models\IncidentSeverity;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use AidingApp\ServiceManagement\Models\IncidentUpdate;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\Get;

test('Can fetch all incidents with updates', function () {
    $settings = app(PortalSettings::class);

    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $contact = Contact::factory()->create();

    actingAs($contact);

    $incidentStatus = IncidentStatus::factory()->create();

    $incidentSeverity = IncidentSeverity::factory()->create();

    Incident::factory()
        ->count(5)
        ->for($incidentStatus, 'status')
        ->for($incidentSeverity, 'severity')
        ->has(IncidentUpdate::factory()->count(2), 'incidentUpdates')
        ->create();

    $url = URL::signedRoute(name: 'api.portal.incidents', absolute: false);
    $response = get($url);

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data.data');
});
