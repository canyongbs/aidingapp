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
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use App\Models\SystemUser;

use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;
use function Tests\actingAsApiUser;

$actingAsAdvisorySeverityDeleter = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.delete');

    return actingAsApiUser($user);
};

it('is gated with proper access control', function (): void {
    $advisorySeverity = AdvisorySeverity::factory()->create();

    actingAsApiUser(SystemUser::factory()->create());
    deleteJson(route('api.v1.advisory-severities.delete', ['advisorySeverity' => $advisorySeverity], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.view-any');
    actingAsApiUser($user);
    deleteJson(route('api.v1.advisory-severities.delete', ['advisorySeverity' => $advisorySeverity], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.*.delete');
    actingAsApiUser($user);
    deleteJson(route('api.v1.advisory-severities.delete', ['advisorySeverity' => $advisorySeverity], false))
        ->assertForbidden();

    assertNotSoftDeleted($advisorySeverity);
});

it('deletes the advisory severity', function () use ($actingAsAdvisorySeverityDeleter): void {
    $actingAsAdvisorySeverityDeleter();

    $advisorySeverity = AdvisorySeverity::factory()->create();

    assertNotSoftDeleted($advisorySeverity);

    deleteJson(route('api.v1.advisory-severities.delete', ['advisorySeverity' => $advisorySeverity], false))
        ->assertNoContent();

    assertSoftDeleted($advisorySeverity);
});

it('does not delete an advisory severity that is used by an advisory', function () use ($actingAsAdvisorySeverityDeleter): void {
    $actingAsAdvisorySeverityDeleter();

    $advisorySeverity = AdvisorySeverity::factory()->create();
    Advisory::factory()->create(['severity_id' => $advisorySeverity->getKey()]);

    deleteJson(route('api.v1.advisory-severities.delete', ['advisorySeverity' => $advisorySeverity], false))
        ->assertForbidden()
        ->assertJsonPath('message', 'The advisory severity cannot be deleted because it is associated with an advisory.');

    assertNotSoftDeleted($advisorySeverity);
});

it('returns not found for a soft deleted advisory severity', function () use ($actingAsAdvisorySeverityDeleter): void {
    $actingAsAdvisorySeverityDeleter();

    $advisorySeverity = AdvisorySeverity::factory()->create();
    $advisorySeverity->delete();

    deleteJson(route('api.v1.advisory-severities.delete', ['advisorySeverity' => $advisorySeverity], false))
        ->assertNotFound();
});
