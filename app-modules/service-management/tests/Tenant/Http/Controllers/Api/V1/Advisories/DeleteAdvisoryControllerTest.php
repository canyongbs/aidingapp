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
use App\Models\SystemUser;
use App\Settings\LicenseSettings;

use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;
use function Tests\actingAsApiUser;

$actingAsAdvisoryDeleter = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['advisories.view-any', 'advisories.*.delete']);

    return actingAsApiUser($user);
};

it('is gated with proper access control', function (): void {
    $advisory = Advisory::factory()->create();

    actingAsApiUser(SystemUser::factory()->create());
    deleteJson(route('api.v1.advisories.delete', ['advisory' => $advisory], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('advisories.view-any');
    actingAsApiUser($user);
    deleteJson(route('api.v1.advisories.delete', ['advisory' => $advisory], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('advisories.*.delete');
    actingAsApiUser($user);
    deleteJson(route('api.v1.advisories.delete', ['advisory' => $advisory], false))
        ->assertForbidden();

    assertNotSoftDeleted($advisory);
});

it('requires the advisory management addon', function () use ($actingAsAdvisoryDeleter): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->advisoryManagement = false;
    $settings->save();

    $actingAsAdvisoryDeleter();

    $advisory = Advisory::factory()->create();

    deleteJson(route('api.v1.advisories.delete', ['advisory' => $advisory], false))
        ->assertForbidden();

    assertNotSoftDeleted($advisory);
});

it('soft deletes the advisory', function () use ($actingAsAdvisoryDeleter): void {
    $actingAsAdvisoryDeleter();

    $advisory = Advisory::factory()->create();

    deleteJson(route('api.v1.advisories.delete', ['advisory' => $advisory], false))
        ->assertNoContent();

    assertSoftDeleted($advisory);
});

it('returns not found for a soft deleted advisory', function () use ($actingAsAdvisoryDeleter): void {
    $actingAsAdvisoryDeleter();

    $advisory = Advisory::factory()->create();
    $advisory->delete();

    deleteJson(route('api.v1.advisories.delete', ['advisory' => $advisory], false))
        ->assertNotFound();
});
