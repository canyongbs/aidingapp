<?php

use AidingApp\Portal\Settings\PortalSettings;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\json;
use function Pest\Laravel\post;

it('search will not work if `Knowledge Management Portal` is not enabled.', function () {
    $url =  URL::signedRoute(name: 'api.portal.search',absolute: false);
    $response = json('POST',$url);
    $response->assertStatus(403);
    $response->assertSee('Knowledge Management Portal is not enabled.');
});

it('search will work if `Knowledge Management Portal` is enabled.', function () {

    $settings = app(PortalSettings::class);
    
    $settings->knowledge_management_portal_enabled = true;

    $settings->save();

    $url =  URL::signedRoute(name: 'api.portal.search',absolute: false);
    $response = json('POST',$url);

    $response->assertStatus(201);
});
