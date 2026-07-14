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
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('portal returns has_projects as false when contact has no pipeline entries', function () {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $contact = Contact::factory()->create();

    actingAs($contact, 'contact');

    $url = URL::signedRoute(name: 'api.portal.define', absolute: false);
    $response = get($url);

    $response->assertSuccessful();
    $response->assertJsonPath('has_projects', false);
});

test('portal returns has_projects as true when contact has pipeline entries', function () {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $contact = Contact::factory()->create();

    PipelineEntry::factory()
        ->for(PipelineStage::factory(), 'pipelineStage')
        ->create([
            'organizable_type' => $contact->getMorphClass(),
            'organizable_id' => $contact->getKey(),
        ]);

    actingAs($contact, 'contact');

    $url = URL::signedRoute(name: 'api.portal.define', absolute: false);
    $response = get($url);

    $response->assertSuccessful();
    $response->assertJsonPath('has_projects', true);
});

test('portal projects route renders successfully', function () {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $response = get(route('portal.projects'));

    $response->assertSuccessful();
});
