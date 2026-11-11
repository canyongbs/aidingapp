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

use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisoryUpdate;
use App\Models\SystemUser;
use App\Settings\LicenseSettings;

use function Pest\Laravel\getJson;
use function Tests\actingAsApiUser;

$actingAsAdvisoryUpdateViewer = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['advisory.view-any', 'advisory.*.view', 'advisory_update.view-any']);

    return actingAsApiUser($user);
};

it('is gated with proper access control', function (array $permissions): void {
    $advisory = Advisory::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo($permissions);
    actingAsApiUser($user);

    getJson(route('api.v1.advisories.updates.index', ['advisory' => $advisory], false))
        ->assertForbidden();
})->with([
    'no permissions' => [[]],
    'missing advisory view any' => [['advisory.*.view', 'advisory_update.view-any']],
    'missing advisory view' => [['advisory.view-any', 'advisory_update.view-any']],
    'missing advisory update view any' => [['advisory.view-any', 'advisory.*.view']],
]);

it('allows access with the advisory and advisory update permissions', function () use ($actingAsAdvisoryUpdateViewer): void {
    $actingAsAdvisoryUpdateViewer();

    getJson(route('api.v1.advisories.updates.index', ['advisory' => Advisory::factory()->create()], false))
        ->assertOk();
});

it('requires the advisory management and service management addons', function (string $addon) use ($actingAsAdvisoryUpdateViewer): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->{$addon} = false;
    $settings->save();

    $actingAsAdvisoryUpdateViewer();

    getJson(route('api.v1.advisories.updates.index', ['advisory' => Advisory::factory()->create()], false))
        ->assertForbidden();
})->with(['advisoryManagement', 'serviceManagement']);

it('returns a paginated list of the advisory\'s updates', function () use ($actingAsAdvisoryUpdateViewer): void {
    $actingAsAdvisoryUpdateViewer();

    $advisory = Advisory::factory()->create();
    $advisoryUpdate = AdvisoryUpdate::factory()->for($advisory)->internal()->create([
        'title' => 'Investigating',
        'update' => 'We are looking into it.',
        'date' => '2026-11-01 09:30:00',
    ]);
    AdvisoryUpdate::factory()->create();

    getJson(route('api.v1.advisories.updates.index', ['advisory' => $advisory], false))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'update', 'internal', 'date', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ])
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.id', $advisoryUpdate->getKey())
        ->assertJsonPath('data.0.title', 'Investigating')
        ->assertJsonPath('data.0.update', 'We are looking into it.')
        ->assertJsonPath('data.0.internal', true)
        ->assertJsonPath('data.0.date', $advisoryUpdate->date->toJSON());
});

it('can filter advisory updates by title', function () use ($actingAsAdvisoryUpdateViewer): void {
    $actingAsAdvisoryUpdateViewer();

    $advisory = Advisory::factory()->create();
    AdvisoryUpdate::factory()->for($advisory)->create(['title' => 'Investigating the outage']);
    AdvisoryUpdate::factory()->for($advisory)->create(['title' => 'Resolved']);

    getJson(route('api.v1.advisories.updates.index', ['advisory' => $advisory, 'filter' => ['title' => 'OUTAGE']], false))
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.title', 'Investigating the outage');
});

it('can filter advisory updates by whether they are internal', function () use ($actingAsAdvisoryUpdateViewer): void {
    $actingAsAdvisoryUpdateViewer();

    $advisory = Advisory::factory()->create();
    $internal = AdvisoryUpdate::factory()->for($advisory)->internal()->create();
    $public = AdvisoryUpdate::factory()->for($advisory)->public()->create();

    getJson(route('api.v1.advisories.updates.index', ['advisory' => $advisory, 'filter' => ['internal' => 'true']], false))
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.id', $internal->getKey());

    getJson(route('api.v1.advisories.updates.index', ['advisory' => $advisory, 'filter' => ['internal' => 'false']], false))
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.id', $public->getKey());
});

it('sorts advisory updates by the newest date by default and supports other sorts', function () use ($actingAsAdvisoryUpdateViewer): void {
    $actingAsAdvisoryUpdateViewer();

    $advisory = Advisory::factory()->create();
    AdvisoryUpdate::factory()->for($advisory)->create(['title' => 'Bravo', 'date' => '2026-11-02 00:00:00']);
    AdvisoryUpdate::factory()->for($advisory)->create(['title' => 'Charlie', 'date' => '2026-11-03 00:00:00']);
    AdvisoryUpdate::factory()->for($advisory)->create(['title' => 'Alpha', 'date' => '2026-11-01 00:00:00']);

    expect(getJson(route('api.v1.advisories.updates.index', ['advisory' => $advisory], false))->json('data.*.title'))
        ->toBe(['Charlie', 'Bravo', 'Alpha']);

    expect(getJson(route('api.v1.advisories.updates.index', ['advisory' => $advisory, 'sort' => 'date'], false))->json('data.*.title'))
        ->toBe(['Alpha', 'Bravo', 'Charlie']);

    expect(getJson(route('api.v1.advisories.updates.index', ['advisory' => $advisory, 'sort' => '-title'], false))->json('data.*.title'))
        ->toBe(['Charlie', 'Bravo', 'Alpha']);
});

it('rejects unknown filters and sorts', function () use ($actingAsAdvisoryUpdateViewer): void {
    $actingAsAdvisoryUpdateViewer();

    $advisory = Advisory::factory()->create();

    getJson(route('api.v1.advisories.updates.index', ['advisory' => $advisory, 'filter' => ['update' => 'outage']], false))
        ->assertBadRequest();

    getJson(route('api.v1.advisories.updates.index', ['advisory' => $advisory, 'sort' => 'update'], false))
        ->assertBadRequest();
});

it('returns not found for a soft deleted advisory', function () use ($actingAsAdvisoryUpdateViewer): void {
    $actingAsAdvisoryUpdateViewer();

    $advisory = Advisory::factory()->create();
    AdvisoryUpdate::factory()->for($advisory)->create();
    $advisory->delete();

    getJson(route('api.v1.advisories.updates.index', ['advisory' => $advisory], false))
        ->assertNotFound();
});
