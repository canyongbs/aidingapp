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
    $user->givePermissionTo(['advisory.view-any', 'advisory.*.view', 'advisory_update.view-any', 'advisory_update.*.view']);

    return actingAsApiUser($user);
};

it('is gated with proper access control', function (array $permissions): void {
    $advisoryUpdate = AdvisoryUpdate::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo($permissions);
    actingAsApiUser($user);

    getJson(route('api.v1.advisories.updates.show', ['advisory' => $advisoryUpdate->advisory, 'advisoryUpdate' => $advisoryUpdate], false))
        ->assertForbidden();
})->with([
    'no permissions' => [[]],
    'missing advisory view any' => [['advisory.*.view', 'advisory_update.view-any', 'advisory_update.*.view']],
    'missing advisory view' => [['advisory.view-any', 'advisory_update.view-any', 'advisory_update.*.view']],
    'missing advisory update view any' => [['advisory.view-any', 'advisory.*.view', 'advisory_update.*.view']],
    'missing advisory update view' => [['advisory.view-any', 'advisory.*.view', 'advisory_update.view-any']],
]);

it('requires the advisory management and service management addons', function (string $addon) use ($actingAsAdvisoryUpdateViewer): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->{$addon} = false;
    $settings->save();

    $actingAsAdvisoryUpdateViewer();

    $advisoryUpdate = AdvisoryUpdate::factory()->create();

    getJson(route('api.v1.advisories.updates.show', ['advisory' => $advisoryUpdate->advisory, 'advisoryUpdate' => $advisoryUpdate], false))
        ->assertForbidden();
})->with(['advisoryManagement', 'serviceManagement']);

it('returns the advisory update', function () use ($actingAsAdvisoryUpdateViewer): void {
    $actingAsAdvisoryUpdateViewer();

    $advisoryUpdate = AdvisoryUpdate::factory()->public()->create([
        'title' => 'Resolved',
        'update' => 'Email is working again.',
        'date' => '2026-11-02 14:00:00',
    ]);

    getJson(route('api.v1.advisories.updates.show', ['advisory' => $advisoryUpdate->advisory, 'advisoryUpdate' => $advisoryUpdate], false))
        ->assertOk()
        ->assertJsonPath('data.id', $advisoryUpdate->getKey())
        ->assertJsonPath('data.title', 'Resolved')
        ->assertJsonPath('data.update', 'Email is working again.')
        ->assertJsonPath('data.internal', false)
        ->assertJsonPath('data.date', $advisoryUpdate->date->toJSON());
});

it('returns not found for an update requested through a different advisory', function () use ($actingAsAdvisoryUpdateViewer): void {
    $actingAsAdvisoryUpdateViewer();

    $advisoryUpdate = AdvisoryUpdate::factory()->create();

    getJson(route('api.v1.advisories.updates.show', ['advisory' => Advisory::factory()->create(), 'advisoryUpdate' => $advisoryUpdate], false))
        ->assertNotFound();
});

it('returns not found when the advisory is soft deleted', function () use ($actingAsAdvisoryUpdateViewer): void {
    $actingAsAdvisoryUpdateViewer();

    $advisoryUpdate = AdvisoryUpdate::factory()->create();
    $advisory = $advisoryUpdate->advisory;
    $advisory->delete();

    getJson(route('api.v1.advisories.updates.show', ['advisory' => $advisory, 'advisoryUpdate' => $advisoryUpdate], false))
        ->assertNotFound();
});
