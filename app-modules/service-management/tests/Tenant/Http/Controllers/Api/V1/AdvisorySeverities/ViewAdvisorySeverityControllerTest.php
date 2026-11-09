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
use CanyonGBS\Common\Enums\Color;

use function Pest\Laravel\getJson;
use function Tests\actingAsApiUser;

$actingAsAdvisorySeverityViewer = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.*.view']);

    return actingAsApiUser($user);
};

it('is gated with proper access control', function (): void {
    $advisorySeverity = AdvisorySeverity::factory()->create();

    actingAsApiUser(SystemUser::factory()->create());
    getJson(route('api.v1.advisory-severities.show', ['advisorySeverity' => $advisorySeverity], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.view-any');
    actingAsApiUser($user);
    getJson(route('api.v1.advisory-severities.show', ['advisorySeverity' => $advisorySeverity], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.*.view');
    actingAsApiUser($user);
    getJson(route('api.v1.advisory-severities.show', ['advisorySeverity' => $advisorySeverity], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.*.view']);
    actingAsApiUser($user);
    getJson(route('api.v1.advisory-severities.show', ['advisorySeverity' => $advisorySeverity], false))
        ->assertOk();
});

it('returns the advisory severity', function () use ($actingAsAdvisorySeverityViewer): void {
    $actingAsAdvisorySeverityViewer();

    $advisorySeverity = AdvisorySeverity::factory()->color(Color::Orange)->create(['name' => 'Major', 'sort' => 2]);

    getJson(route('api.v1.advisory-severities.show', ['advisorySeverity' => $advisorySeverity], false))
        ->assertOk()
        ->assertJsonPath('data.id', $advisorySeverity->getKey())
        ->assertJsonPath('data.name', 'Major')
        ->assertJsonPath('data.color', 'orange')
        ->assertJsonPath('data.sort', 2);
});

it('returns not found for a soft deleted advisory severity', function () use ($actingAsAdvisorySeverityViewer): void {
    $actingAsAdvisorySeverityViewer();

    $advisorySeverity = AdvisorySeverity::factory()->create();
    $advisorySeverity->delete();

    getJson(route('api.v1.advisory-severities.show', ['advisorySeverity' => $advisorySeverity], false))
        ->assertNotFound();
});
