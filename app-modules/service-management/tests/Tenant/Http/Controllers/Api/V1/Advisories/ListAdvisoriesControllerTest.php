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
use App\Features\AdvisorySeveritySortFeature;
use App\Features\AdvisoryVisibilityFeature;
use App\Models\SystemUser;
use App\Settings\LicenseSettings;
use CanyonGBS\Common\Enums\Color;

use function Pest\Laravel\getJson;
use function Tests\actingAsApiUser;

$actingAsAdvisoryViewer = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('advisory.view-any');

    return actingAsApiUser($user);
};

it('is gated with proper access control', function (): void {
    actingAsApiUser(SystemUser::factory()->create());
    getJson(route('api.v1.advisories.index', [], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('advisory.view-any');
    actingAsApiUser($user);
    getJson(route('api.v1.advisories.index', [], false))
        ->assertOk();
});

it('requires the advisory management addon', function () use ($actingAsAdvisoryViewer): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->advisoryManagement = false;
    $settings->save();

    $actingAsAdvisoryViewer();

    getJson(route('api.v1.advisories.index', [], false))
        ->assertForbidden();
});

it('returns a paginated list of advisories', function () use ($actingAsAdvisoryViewer): void {
    $actingAsAdvisoryViewer();

    $severity = AdvisorySeverity::factory()->color(Color::Red)->create(['name' => 'Critical']);
    $status = AdvisoryStatus::factory()->open()->create(['name' => 'Investigating']);
    $department = Department::factory()->create(['name' => 'IT']);
    $contactType = ContactType::factory()->create(['name' => 'Student']);

    $advisory = Advisory::factory()
        ->for($severity, 'severity')
        ->for($status, 'status')
        ->for($department, 'assignedDepartment')
        ->restrictedToContactTypes($contactType)
        ->create(['title' => 'Email outage', 'description' => 'Email is down.']);

    getJson(route('api.v1.advisories.index', [], false))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'severity' => ['id', 'name', 'color'],
                    'status' => ['id', 'name', 'classification'],
                    'department' => ['id', 'name'],
                    'visibility',
                    'is_visibility_restricted',
                    'restricted_to_contact_types' => ['*' => ['id', 'name']],
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
        ])
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.id', $advisory->getKey())
        ->assertJsonPath('data.0.title', 'Email outage')
        ->assertJsonPath('data.0.description', 'Email is down.')
        ->assertJsonPath('data.0.severity', ['id' => $severity->getKey(), 'name' => 'Critical', 'color' => 'red'])
        ->assertJsonPath('data.0.status', ['id' => $status->getKey(), 'name' => 'Investigating', 'classification' => 'open'])
        ->assertJsonPath('data.0.department', ['id' => $department->getKey(), 'name' => 'IT'])
        ->assertJsonPath('data.0.visibility', 'authenticated')
        ->assertJsonPath('data.0.is_visibility_restricted', true)
        ->assertJsonPath('data.0.restricted_to_contact_types', [['id' => $contactType->getKey(), 'name' => 'Student']]);
});

it('returns a null department for an unassigned advisory', function () use ($actingAsAdvisoryViewer): void {
    $actingAsAdvisoryViewer();

    Advisory::factory()->create(['assigned_department_id' => null]);

    getJson(route('api.v1.advisories.index', [], false))
        ->assertJsonPath('data.0.department', null);
});

it('omits the visibility fields when `AdvisoryVisibilityFeature` is inactive', function () use ($actingAsAdvisoryViewer): void {
    AdvisoryVisibilityFeature::deactivate();

    $actingAsAdvisoryViewer();

    Advisory::factory()->create();

    $advisory = getJson(route('api.v1.advisories.index', [], false))
        ->assertOk()
        ->json('data.0');

    expect($advisory)->not->toHaveKeys(['visibility', 'is_visibility_restricted', 'restricted_to_contact_types']);
});

it('does not return soft deleted advisories', function () use ($actingAsAdvisoryViewer): void {
    $actingAsAdvisoryViewer();

    Advisory::factory()->create();
    Advisory::factory()->create()->delete();

    getJson(route('api.v1.advisories.index', [], false))
        ->assertJsonPath('meta.total', 1);
});

it('can filter advisories by title', function () use ($actingAsAdvisoryViewer): void {
    $actingAsAdvisoryViewer();

    Advisory::factory()->create(['title' => 'Email outage']);
    Advisory::factory()->create(['title' => 'Scheduled maintenance']);

    getJson(route('api.v1.advisories.index', ['filter' => ['title' => 'OUTAGE']], false))
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.title', 'Email outage');
});

it('can filter advisories by related names, ignoring case', function (string $filter, string $relationship, Closure $makeRelated) use ($actingAsAdvisoryViewer): void {
    $actingAsAdvisoryViewer();

    $first = Advisory::factory()->for($makeRelated('First, with a comma'), $relationship)->create();
    $second = Advisory::factory()->for($makeRelated('Second'), $relationship)->create();
    Advisory::factory()->for($makeRelated('Third'), $relationship)->create();

    expect(getJson(route('api.v1.advisories.index', ['filter' => [$filter => 'first, WITH a comma']], false))->json('data.*.id'))
        ->toBe([$first->getKey()]);

    expect(getJson(route('api.v1.advisories.index', ['filter' => [$filter => 'First, with a comma|second']], false))->json('data.*.id'))
        ->toEqualCanonicalizing([$first->getKey(), $second->getKey()]);
})->with([
    'status' => ['status', 'status', fn (string $name): AdvisoryStatus => AdvisoryStatus::factory()->create(['name' => $name])],
    'severity' => ['severity', 'severity', fn (string $name): AdvisorySeverity => AdvisorySeverity::factory()->create(['name' => $name])],
    'department' => ['department', 'assignedDepartment', fn (string $name): Department => Department::factory()->create(['name' => $name])],
]);

it('can filter advisories by status classification', function () use ($actingAsAdvisoryViewer): void {
    $actingAsAdvisoryViewer();

    $open = Advisory::factory()->for(AdvisoryStatus::factory()->open(), 'status')->create();
    Advisory::factory()->for(AdvisoryStatus::factory()->resolved(), 'status')->count(2)->create();

    getJson(route('api.v1.advisories.index', ['filter' => ['classification' => 'open']], false))
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.id', $open->getKey());
});

it('sorts advisories by title by default', function () use ($actingAsAdvisoryViewer): void {
    $actingAsAdvisoryViewer();

    Advisory::factory()->create(['title' => 'Bravo']);
    Advisory::factory()->create(['title' => 'Charlie']);
    Advisory::factory()->create(['title' => 'Alpha']);

    expect(getJson(route('api.v1.advisories.index', [], false))->json('data.*.title'))
        ->toBe(['Alpha', 'Bravo', 'Charlie']);
});

it('can sort advisories by severity', function () use ($actingAsAdvisoryViewer): void {
    $actingAsAdvisoryViewer();

    // Sorted in an order that differs from the severity names' alphabetical order and its reverse.
    Advisory::factory()->for(AdvisorySeverity::factory()->state(['name' => 'Maintenance', 'sort' => 3]), 'severity')->create(['title' => 'Maintenance advisory']);
    Advisory::factory()->for(AdvisorySeverity::factory()->state(['name' => 'Outage', 'sort' => 1]), 'severity')->create(['title' => 'Outage advisory']);
    Advisory::factory()->for(AdvisorySeverity::factory()->state(['name' => 'Degraded', 'sort' => 2]), 'severity')->create(['title' => 'Degraded advisory']);

    expect(getJson(route('api.v1.advisories.index', ['sort' => 'severity'], false))->json('data.*.title'))
        ->toBe(['Outage advisory', 'Degraded advisory', 'Maintenance advisory']);

    expect(getJson(route('api.v1.advisories.index', ['sort' => '-severity'], false))->json('data.*.title'))
        ->toBe(['Maintenance advisory', 'Degraded advisory', 'Outage advisory']);
});

it('sorts advisories by severity name when `AdvisorySeveritySortFeature` is inactive', function () use ($actingAsAdvisoryViewer): void {
    AdvisorySeveritySortFeature::deactivate();

    $actingAsAdvisoryViewer();

    Advisory::factory()->for(AdvisorySeverity::factory()->state(['name' => 'Minor', 'sort' => 1]), 'severity')->create(['title' => 'Minor advisory']);
    Advisory::factory()->for(AdvisorySeverity::factory()->state(['name' => 'Critical', 'sort' => 3]), 'severity')->create(['title' => 'Critical advisory']);
    Advisory::factory()->for(AdvisorySeverity::factory()->state(['name' => 'Major', 'sort' => 2]), 'severity')->create(['title' => 'Major advisory']);

    expect(getJson(route('api.v1.advisories.index', ['sort' => 'severity'], false))->json('data.*.title'))
        ->toBe(['Critical advisory', 'Major advisory', 'Minor advisory']);
});

it('rejects unknown filters and sorts', function (array $query) use ($actingAsAdvisoryViewer): void {
    $actingAsAdvisoryViewer();

    getJson(route('api.v1.advisories.index', $query, false))
        ->assertBadRequest();
})->with([
    'filter' => [['filter' => ['description' => 'outage']]],
    'sort' => [['sort' => 'description']],
]);
