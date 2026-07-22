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
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\File;
use Illuminate\Testing\TestResponse;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_enabled = true;
    $portalSettings->ai_support_assistant = true;
    $portalSettings->save();

    $manifestPath = public_path('storage/widgets/assistant/.vite/manifest.json');

    $this->manifestPath = $manifestPath;
    $this->manifestOriginalContents = File::exists($manifestPath) ? File::get($manifestPath) : null;

    File::ensureDirectoryExists(dirname($manifestPath));
    File::put($manifestPath, json_encode([
        'src/widget.js' => [
            'file' => 'assets/widget-test.js',
            'name' => 'widget',
            'src' => 'src/widget.js',
            'isEntry' => true,
        ],
    ]));
});

afterEach(function () {
    if ($this->manifestOriginalContents === null) {
        File::delete($this->manifestPath);

        return;
    }

    File::put($this->manifestPath, $this->manifestOriginalContents);
});

function getWidgetConfig(): TestResponse
{
    return getJson(route('widgets.assistant.api.config'), ['Origin' => config('app.url')]);
}

it('exposes service management when the license addon and portal setting are enabled', function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_service_management = true;
    $portalSettings->save();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $response = getWidgetConfig();

    $response->assertOk()
        ->assertJson(['portal_service_management' => true]);

    expect($response->json('service_request_types_url'))->not->toBeNull();
});

it('does not expose service management when the license addon is disabled', function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_service_management = true;
    $portalSettings->save();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = false;
    $settings->save();

    $response = getWidgetConfig();

    $response->assertOk()
        ->assertJson([
            'portal_service_management' => false,
            'service_request_types_url' => null,
        ]);
});

it('does not expose service management when the portal setting is disabled', function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_service_management = false;
    $portalSettings->save();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $response = getWidgetConfig();

    $response->assertOk()
        ->assertJson([
            'portal_service_management' => false,
            'service_request_types_url' => null,
        ]);
});

it('exposes service management for an authenticated contact when the license addon and portal setting are enabled', function () {
    $portalSettings = app(PortalSettings::class);
    $portalSettings->knowledge_management_portal_service_management = true;
    $portalSettings->save();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $contact = Contact::factory()->create();
    $contact->createToken('assistant-widget-access-token');

    actingAs($contact, 'contact');

    $response = getWidgetConfig();

    $response->assertOk()
        ->assertJson(['portal_service_management' => true]);

    expect($response->json('service_request_types_url'))->not->toBeNull();
});
