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

use AidingApp\Contact\Models\ContactType;
use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use App\Models\SystemUser;
use App\Settings\LicenseSettings;
use CanyonGBS\Common\Enums\Color;

use function Pest\Laravel\getJson;
use function Tests\actingAsApiUser;

$actingAsAdvisoryViewer = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['advisories.view-any', 'advisories.*.view']);

    return actingAsApiUser($user);
};

it('is gated with proper access control', function (): void {
    $advisory = Advisory::factory()->create();

    actingAsApiUser(SystemUser::factory()->create());
    getJson(route('api.v1.advisories.show', ['advisory' => $advisory], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('advisories.view-any');
    actingAsApiUser($user);
    getJson(route('api.v1.advisories.show', ['advisory' => $advisory], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('advisories.*.view');
    actingAsApiUser($user);
    getJson(route('api.v1.advisories.show', ['advisory' => $advisory], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['advisories.view-any', 'advisories.*.view']);
    actingAsApiUser($user);
    getJson(route('api.v1.advisories.show', ['advisory' => $advisory], false))
        ->assertOk();
});

it('requires the advisory management addon', function () use ($actingAsAdvisoryViewer): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->advisoryManagement = false;
    $settings->save();

    $actingAsAdvisoryViewer();

    getJson(route('api.v1.advisories.show', ['advisory' => Advisory::factory()->create()], false))
        ->assertForbidden();
});

it('returns the advisory', function () use ($actingAsAdvisoryViewer): void {
    $actingAsAdvisoryViewer();

    $severity = AdvisorySeverity::factory()->color(Color::Orange)->create(['name' => 'Major']);
    $status = AdvisoryStatus::factory()->resolved()->create(['name' => 'Resolved']);
    $department = Department::factory()->create(['name' => 'Facilities']);
    $staff = ContactType::factory()->create(['name' => 'Staff']);
    $student = ContactType::factory()->create(['name' => 'Student']);

    $advisory = Advisory::factory()
        ->for($severity, 'severity')
        ->for($status, 'status')
        ->for($department, 'assignedDepartment')
        ->restrictedToContactTypes($student, $staff)
        ->create(['title' => 'Power outage', 'description' => 'The power is out.']);

    getJson(route('api.v1.advisories.show', ['advisory' => $advisory], false))
        ->assertOk()
        ->assertJsonPath('data.id', $advisory->getKey())
        ->assertJsonPath('data.title', 'Power outage')
        ->assertJsonPath('data.description', 'The power is out.')
        ->assertJsonPath('data.severity', ['id' => $severity->getKey(), 'name' => 'Major', 'color' => 'orange'])
        ->assertJsonPath('data.status', ['id' => $status->getKey(), 'name' => 'Resolved', 'classification' => 'resolved'])
        ->assertJsonPath('data.department', ['id' => $department->getKey(), 'name' => 'Facilities'])
        ->assertJsonPath('data.visibility', 'authenticated')
        ->assertJsonPath('data.is_visibility_restricted', true)
        ->assertJsonPath('data.restricted_to_contact_types', [
            ['id' => $staff->getKey(), 'name' => 'Staff'],
            ['id' => $student->getKey(), 'name' => 'Student'],
        ]);
});

it('returns not found for a soft deleted advisory', function () use ($actingAsAdvisoryViewer): void {
    $actingAsAdvisoryViewer();

    $advisory = Advisory::factory()->create();
    $advisory->delete();

    getJson(route('api.v1.advisories.show', ['advisory' => $advisory], false))
        ->assertNotFound();
});
