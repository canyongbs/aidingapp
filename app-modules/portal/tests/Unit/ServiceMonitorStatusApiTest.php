<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('returns all service monitoring targets with latest history when portal is enabled', function () {
    $settings = app(PortalSettings::class);

    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $contact = Contact::factory()->create();

    actingAs($contact);

    $targets = ServiceMonitoringTarget::factory()
        ->count(1)
        ->sequence(
            ['id' => '9f18838a-051d-441a-afc8-ded84bb070be','name' => 'Google', 'domain' => 'https://google.com'],
        )
        ->create();

    foreach ($targets as $target) {
        $target->histories()->make([
            'response_time' => 0.123,
            'succeeded' => true,
            'response' => 200,
        ])->forceFill(['id' => '9f18ac66-7369-4282-b834-9f672f49b2bc'])->save();
    }

    $url = URL::route(name: 'api.portal.status', absolute: false);
    $response = get($url);

    expect($response)->toMatchSnapshot();
});