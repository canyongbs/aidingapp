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

use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use App\Models\SystemUser;
use App\Settings\LicenseSettings;
use CanyonGBS\Common\Enums\Color;

use function Pest\Laravel\getJson;
use function Tests\actingAsApiUser;

$actingAsAdvisorySeverityViewer = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.view-any');

    return actingAsApiUser($user);
};

it('is gated with proper access control', function (): void {
    actingAsApiUser(SystemUser::factory()->create());
    getJson(route('api.v1.advisory-severities.index', [], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.view-any');
    actingAsApiUser($user);
    getJson(route('api.v1.advisory-severities.index', [], false))
        ->assertOk();
});

it('requires the advisory management addon', function () use ($actingAsAdvisorySeverityViewer): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->advisoryManagement = false;
    $settings->save();

    $actingAsAdvisorySeverityViewer();

    getJson(route('api.v1.advisory-severities.index', [], false))
        ->assertForbidden();
});

it('returns a paginated list of advisory severities', function () use ($actingAsAdvisorySeverityViewer): void {
    $actingAsAdvisorySeverityViewer();

    $advisorySeverity = AdvisorySeverity::factory()->color(Color::Red)->create(['name' => 'Critical', 'sort' => 1]);

    getJson(route('api.v1.advisory-severities.index', [], false))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'color', 'sort', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ])
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.id', $advisorySeverity->getKey())
        ->assertJsonPath('data.0.name', 'Critical')
        ->assertJsonPath('data.0.color', 'red')
        ->assertJsonPath('data.0.sort', 1);
});

it('does not return soft deleted advisory severities', function () use ($actingAsAdvisorySeverityViewer): void {
    $actingAsAdvisorySeverityViewer();

    AdvisorySeverity::factory()->create();
    AdvisorySeverity::factory()->create()->delete();

    getJson(route('api.v1.advisory-severities.index', [], false))
        ->assertJsonPath('meta.total', 1);
});

it('can filter advisory severities by name', function () use ($actingAsAdvisorySeverityViewer): void {
    $actingAsAdvisorySeverityViewer();

    AdvisorySeverity::factory()->create(['name' => 'Major Outage']);
    AdvisorySeverity::factory()->create(['name' => 'Minor']);

    getJson(route('api.v1.advisory-severities.index', ['filter' => ['name' => 'outage']], false))
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.name', 'Major Outage');
});

it('can filter advisory severities by color', function () use ($actingAsAdvisorySeverityViewer): void {
    $actingAsAdvisorySeverityViewer();

    $red = AdvisorySeverity::factory()->color(Color::Red)->create();
    AdvisorySeverity::factory()->color(Color::Blue)->count(2)->create();

    getJson(route('api.v1.advisory-severities.index', ['filter' => ['color' => 'red']], false))
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.id', $red->getKey());
});

it('sorts advisory severities by `sort` by default and supports descending sorts', function () use ($actingAsAdvisorySeverityViewer): void {
    $actingAsAdvisorySeverityViewer();

    // Sorted in an order that differs from the names' alphabetical order and its reverse.
    AdvisorySeverity::factory()->create(['name' => 'Maintenance', 'sort' => 3]);
    AdvisorySeverity::factory()->create(['name' => 'Outage', 'sort' => 1]);
    AdvisorySeverity::factory()->create(['name' => 'Degraded', 'sort' => 2]);

    expect(getJson(route('api.v1.advisory-severities.index', [], false))->json('data.*.name'))
        ->toBe(['Outage', 'Degraded', 'Maintenance']);

    expect(getJson(route('api.v1.advisory-severities.index', ['sort' => '-sort'], false))->json('data.*.name'))
        ->toBe(['Maintenance', 'Degraded', 'Outage']);
});

it('can sort advisory severities by name', function () use ($actingAsAdvisorySeverityViewer): void {
    $actingAsAdvisorySeverityViewer();

    AdvisorySeverity::factory()->create(['name' => 'Bravo', 'sort' => 1]);
    AdvisorySeverity::factory()->create(['name' => 'Charlie', 'sort' => 2]);
    AdvisorySeverity::factory()->create(['name' => 'Alpha', 'sort' => 3]);

    expect(getJson(route('api.v1.advisory-severities.index', ['sort' => 'name'], false))->json('data.*.name'))
        ->toBe(['Alpha', 'Bravo', 'Charlie']);
});

it('rejects filters and sorts that are not allowed', function () use ($actingAsAdvisorySeverityViewer): void {
    $actingAsAdvisorySeverityViewer();

    getJson(route('api.v1.advisory-severities.index', ['filter' => ['id' => 'x']], false))
        ->assertBadRequest();

    getJson(route('api.v1.advisory-severities.index', ['sort' => 'color'], false))
        ->assertBadRequest();
});
