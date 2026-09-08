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
use AidingApp\Contact\Models\Organization;
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\Project\Models\Project;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    $licenseSettings = app(LicenseSettings::class);
    $licenseSettings->data->addons->projectManagement = true;
    $licenseSettings->save();
});

it('returns `has_projects` as false when the contact has no assigned projects', function () {
    $contact = Contact::factory()->create();

    actingAs($contact, 'contact');

    $url = URL::signedRoute(name: 'api.portal.define', absolute: false);
    $response = get($url);

    $response->assertSuccessful();
    $response->assertJsonPath('has_projects', false);
});

it('returns `has_projects` as true when the contact is a direct project guest', function () {
    $contact = Contact::factory()->create();

    $project = Project::factory()->create();
    $project->guestContacts()->attach($contact);

    actingAs($contact, 'contact');

    $url = URL::signedRoute(name: 'api.portal.define', absolute: false);
    $response = get($url);

    $response->assertSuccessful();
    $response->assertJsonPath('has_projects', true);
});

it('returns `has_projects` as true when the contact organization is a project guest', function () {
    $organization = Organization::factory()->create();
    $contact = Contact::factory()->for($organization)->create();
    $project = Project::factory()->create();
    $project->guestOrganizations()->attach($organization);

    actingAs($contact, 'contact');

    $response = get(URL::signedRoute(name: 'api.portal.define', absolute: false));

    $response->assertSuccessful();
    $response->assertJsonPath('has_projects', true);
});

it('returns `has_projects` as false when Project Management is not licensed', function () {
    $licenseSettings = app(LicenseSettings::class);
    $licenseSettings->data->addons->projectManagement = false;
    $licenseSettings->save();

    $contact = Contact::factory()->create();
    $project = Project::factory()->create();
    $project->guestContacts()->attach($contact);

    actingAs($contact, 'contact');

    $response = get(URL::signedRoute(name: 'api.portal.define', absolute: false));

    $response->assertSuccessful();
    $response->assertJsonPath('has_projects', false);
});

it('renders the portal projects route', function () {
    $response = get(route('portal.projects'));

    $response->assertSuccessful();
});
