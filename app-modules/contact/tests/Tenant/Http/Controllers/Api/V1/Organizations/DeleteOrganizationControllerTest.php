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

use AidingApp\Contact\Models\Organization;
use App\Features\OrganizationArchivingFeature;
use App\Models\SystemUser;

use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;
use function Tests\actingAsApiUser;

it('is gated with proper access control', function (): void {
    $organization = Organization::factory()->create();

    $user = SystemUser::factory()->create();
    actingAsApiUser($user);
    deleteJson(route('api.v1.organizations.delete', ['organization' => $organization], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    actingAsApiUser($user);
    deleteJson(route('api.v1.organizations.delete', ['organization' => $organization], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.*.delete');
    actingAsApiUser($user);
    deleteJson(route('api.v1.organizations.delete', ['organization' => $organization], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.delete');
    actingAsApiUser($user);
    deleteJson(route('api.v1.organizations.delete', ['organization' => $organization], false))
        ->assertNoContent();
});

it('archives the organization', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.delete');
    actingAsApiUser($user);

    $organization = Organization::factory()->create();

    expect($organization->isArchived())->toBeFalse();

    deleteJson(route('api.v1.organizations.delete', ['organization' => $organization], false))
        ->assertNoContent();

    $organization->refresh();

    expect($organization->isArchived())->toBeTrue()
        ->and($organization->trashed())->toBeFalse();
});

it('returns not found for an archived organization', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.delete');
    actingAsApiUser($user);

    $organization = Organization::factory()->archived()->create();

    deleteJson(route('api.v1.organizations.delete', ['organization' => $organization], false))
        ->assertNotFound();

    assertModelExists($organization);
});

it('checks permissions before returning not found for an archived organization', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    actingAsApiUser($user);

    $organization = Organization::factory()->archived()->create();

    deleteJson(route('api.v1.organizations.delete', ['organization' => $organization], false))
        ->assertForbidden();
});

it('returns not found for a soft deleted organization', function (): void {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.delete');
    actingAsApiUser($user);

    $organization = Organization::factory()->create();
    $organization->delete();

    deleteJson(route('api.v1.organizations.delete', ['organization' => $organization], false))
        ->assertNotFound();
});

it('soft deletes the organization when organization archiving is inactive', function (): void {
    OrganizationArchivingFeature::deactivate();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('organization.view-any');
    $user->givePermissionTo('organization.*.delete');
    actingAsApiUser($user);

    $organization = Organization::factory()->create();

    deleteJson(route('api.v1.organizations.delete', ['organization' => $organization], false))
        ->assertNoContent();

    assertSoftDeleted($organization);

    expect($organization->refresh()->isArchived())->toBeFalse();
});
