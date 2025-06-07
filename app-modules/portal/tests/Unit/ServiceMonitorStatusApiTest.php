<?php

use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\get;

test('categories and items are returned without filtering', function () {
    $settings = app(PortalSettings::class);

    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    ServiceMonitoringTarget::factory()->count(5)->create();

    $url = URL::signedRoute(name: 'api.portal.status', absolute: false);
    $response = get($url);

    expect($response)->toMatchSnapshot();
});
