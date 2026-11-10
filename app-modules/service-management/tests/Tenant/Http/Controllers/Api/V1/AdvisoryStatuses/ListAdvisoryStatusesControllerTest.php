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

use AidingApp\ServiceManagement\Enums\SystemAdvisoryStatusClassification;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use App\Models\SystemUser;
use App\Settings\LicenseSettings;

use function Pest\Laravel\getJson;
use function Tests\actingAsApiUser;

$actingAsAdvisoryStatusViewer = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.view-any');

    return actingAsApiUser($user);
};

it('is gated with proper access control', function (): void {
    actingAsApiUser(SystemUser::factory()->create());
    getJson(route('api.v1.advisory-statuses.index', [], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.view-any');
    actingAsApiUser($user);
    getJson(route('api.v1.advisory-statuses.index', [], false))
        ->assertOk();
});

it('requires the advisory management addon', function () use ($actingAsAdvisoryStatusViewer): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->advisoryManagement = false;
    $settings->save();

    $actingAsAdvisoryStatusViewer();

    getJson(route('api.v1.advisory-statuses.index', [], false))
        ->assertForbidden();
});

it('returns a paginated list of advisory statuses', function () use ($actingAsAdvisoryStatusViewer): void {
    $actingAsAdvisoryStatusViewer();

    $advisoryStatus = AdvisoryStatus::factory()->open()->create(['name' => 'Investigating']);

    getJson(route('api.v1.advisory-statuses.index', [], false))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'classification', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ])
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.id', $advisoryStatus->getKey())
        ->assertJsonPath('data.0.name', 'Investigating')
        ->assertJsonPath('data.0.classification', 'open');
});

it('does not return soft deleted advisory statuses', function () use ($actingAsAdvisoryStatusViewer): void {
    $actingAsAdvisoryStatusViewer();

    AdvisoryStatus::factory()->create();
    AdvisoryStatus::factory()->create()->delete();

    getJson(route('api.v1.advisory-statuses.index', [], false))
        ->assertJsonPath('meta.total', 1);
});

it('can filter advisory statuses by name', function () use ($actingAsAdvisoryStatusViewer): void {
    $actingAsAdvisoryStatusViewer();

    AdvisoryStatus::factory()->create(['name' => 'Under Investigation']);
    AdvisoryStatus::factory()->create(['name' => 'Monitoring']);

    getJson(route('api.v1.advisory-statuses.index', ['filter' => ['name' => 'investigation']], false))
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.name', 'Under Investigation');
});

it('can filter advisory statuses by classification', function () use ($actingAsAdvisoryStatusViewer): void {
    $actingAsAdvisoryStatusViewer();

    $resolved = AdvisoryStatus::factory()->resolved()->create();
    AdvisoryStatus::factory()->open()->count(2)->create();

    getJson(route('api.v1.advisory-statuses.index', ['filter' => ['classification' => SystemAdvisoryStatusClassification::Resolved->value]], false))
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.id', $resolved->getKey());
});

it('sorts advisory statuses by name by default and supports descending sorts', function () use ($actingAsAdvisoryStatusViewer): void {
    $actingAsAdvisoryStatusViewer();

    AdvisoryStatus::factory()->create(['name' => 'Bravo']);
    AdvisoryStatus::factory()->create(['name' => 'Alpha']);
    AdvisoryStatus::factory()->create(['name' => 'Charlie']);

    expect(getJson(route('api.v1.advisory-statuses.index', [], false))->json('data.*.name'))
        ->toBe(['Alpha', 'Bravo', 'Charlie']);

    expect(getJson(route('api.v1.advisory-statuses.index', ['sort' => '-name'], false))->json('data.*.name'))
        ->toBe(['Charlie', 'Bravo', 'Alpha']);
});

it('rejects filters and sorts that are not allowed', function () use ($actingAsAdvisoryStatusViewer): void {
    $actingAsAdvisoryStatusViewer();

    getJson(route('api.v1.advisory-statuses.index', ['filter' => ['id' => 'x']], false))
        ->assertBadRequest();

    getJson(route('api.v1.advisory-statuses.index', ['sort' => 'classification'], false))
        ->assertBadRequest();
});
