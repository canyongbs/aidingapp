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
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use AidingApp\ServiceManagement\Models\AdvisoryUpdate;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\Get;

test('Can fetch all advisories with updates', function () {
    $settings = app(PortalSettings::class);

    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $contact = Contact::factory()->create();

    actingAs($contact);

    $advisoryStatus = AdvisoryStatus::factory()->create();

    $advisorySeverity = AdvisorySeverity::factory()->create();

    Advisory::factory()
        ->count(5)
        ->for($advisoryStatus, 'status')
        ->for($advisorySeverity, 'severity')
        ->has(AdvisoryUpdate::factory()->count(2), 'advisoryUpdates')
        ->create();

    $url = URL::signedRoute(name: 'api.portal.advisories', absolute: false);
    $response = get($url);

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data.data');
});
