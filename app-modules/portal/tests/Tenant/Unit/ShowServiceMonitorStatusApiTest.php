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
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('returns the full detail payload for a service monitoring target', function () {
    $settings = app(PortalSettings::class);

    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $contact = Contact::factory()->create();

    actingAs($contact);

    $target = ServiceMonitoringTarget::factory()->create(['name' => 'Google']);

    $target->histories()->create(['response' => 200, 'response_time' => 0.1, 'succeeded' => true]);

    $url = URL::route(name: 'api.portal.status.show', parameters: ['serviceMonitoringTarget' => $target], absolute: false);

    $response = get($url);

    $response->assertOk();
    $response->assertJsonPath('data.id', $target->getKey());
    $response->assertJsonPath('data.name', 'Google');
    $response->assertJsonPath('data.status', 'operational');
    $response->assertJsonCount(30, 'data.history');
    $response->assertJsonStructure([
        'data' => [
            'uptime' => [
                'twenty_four_hour' => ['value', 'label'],
                'seven_day' => ['value', 'label'],
                'thirty_day' => ['value', 'label'],
                'ninety_day' => ['value', 'label'],
                'twelve_month' => ['value', 'label'],
            ],
        ],
    ]);
});

test('returns a 404 for a confidential service monitoring target the contact cannot access', function () {
    $settings = app(PortalSettings::class);

    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $contact = Contact::factory()->create();

    actingAs($contact);

    $target = ServiceMonitoringTarget::factory()->confidential()->create(['name' => 'Confidential Monitor']);

    $url = URL::route(name: 'api.portal.status.show', parameters: ['serviceMonitoringTarget' => $target], absolute: false);

    get($url)->assertNotFound();

    $target->confidentialContacts()->attach($contact->getKey());

    get($url)->assertOk();
});
