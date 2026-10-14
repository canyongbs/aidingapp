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
use AidingApp\Department\Models\Department;
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use AidingApp\ServiceManagement\Models\AdvisoryUpdate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $settings = app(PortalSettings::class);
    $settings->knowledge_management_portal_enabled = true;
    $settings->save();

    actingAs(Contact::factory()->create());
});

it('displays the advisory data', function () {
    $date = Carbon::parse('2026-10-08 08:30:00', 'UTC');

    $severity = AdvisorySeverity::factory()->create(['name' => 'Major', 'color' => 'orange']);
    $status = AdvisoryStatus::factory()->open()->create(['name' => 'Mitigating']);

    $advisory = Advisory::factory()
        ->for($severity, 'severity')
        ->for($status, 'status')
        ->for(Department::factory()->state(['name' => 'Facilities']), 'assignedDepartment')
        ->create([
            'title' => 'Science Hall flooding',
            'description' => 'A burst pipe has flooded the ground floor.',
        ]);

    $update = AdvisoryUpdate::factory()->public()->for($advisory, 'advisory')->create([
        'title' => 'Water removal underway',
        'update' => 'Crews are removing water.',
        'date' => $date,
    ]);

    getJson(route('api.portal.advisories.show', ['advisory' => $advisory]))
        ->assertOk()
        ->assertJsonPath('data.id', $advisory->getKey())
        ->assertJsonPath('data.title', 'Science Hall flooding')
        ->assertJsonPath('data.description', 'A burst pipe has flooded the ground floor.')
        ->assertJsonPath('data.severity', ['id' => $severity->getKey(), 'name' => 'Major', 'color' => 'orange'])
        ->assertJsonPath('data.status', ['id' => $status->getKey(), 'name' => 'Mitigating', 'classification' => 'open'])
        ->assertJsonPath('data.department_name', 'Facilities')
        ->assertJsonPath('data.updates', [[
            'id' => $update->getKey(),
            'title' => 'Water removal underway',
            'update' => 'Crews are removing water.',
            'date' => '2026-10-08T08:30:00+00:00',
        ]]);
});

it('lists the updates newest first', function () {
    $advisory = Advisory::factory()->create();

    $olderUpdate = AdvisoryUpdate::factory()->public()->for($advisory, 'advisory')->create(['date' => now()->subDay(), 'created_at' => now()]);
    $newerUpdate = AdvisoryUpdate::factory()->public()->for($advisory, 'advisory')->create(['date' => now(), 'created_at' => now()->subDay()]);

    $updateIds = collect(getJson(route('api.portal.advisories.show', ['advisory' => $advisory]))->assertOk()->json('data.updates'))->pluck('id');

    expect($updateIds->all())->toBe([$newerUpdate->getKey(), $olderUpdate->getKey()]);
});

it('does not show internal updates', function () {
    $advisory = Advisory::factory()->create();

    $publicUpdate = AdvisoryUpdate::factory()->public()->for($advisory, 'advisory')->create();
    AdvisoryUpdate::factory()->internal()->for($advisory, 'advisory')->create();

    $updateIds = collect(getJson(route('api.portal.advisories.show', ['advisory' => $advisory]))->assertOk()->json('data.updates'))->pluck('id');

    expect($updateIds->all())->toBe([$publicUpdate->getKey()]);
});

it('returns not found for a missing advisory', function () {
    getJson(route('api.portal.advisories.show', ['advisory' => (string) Str::uuid()]))->assertNotFound();
});

it('returns not found for a deleted advisory', function () {
    $advisory = Advisory::factory()->create();
    $advisory->delete();

    getJson(route('api.portal.advisories.show', ['advisory' => $advisory]))->assertNotFound();
});

describe('authorization', function () {
    it('denies access to guests', function () {
        auth()->logout();

        getJson(route('api.portal.advisories.show', ['advisory' => Advisory::factory()->create()]))->assertUnauthorized();
    });
});
