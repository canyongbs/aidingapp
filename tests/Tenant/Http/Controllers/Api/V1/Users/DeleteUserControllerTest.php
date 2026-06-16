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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use App\Models\Authenticatable;
use App\Models\SystemUser;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;

beforeEach(function () {
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $targetUser = User::factory()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);

    deleteJson(route('api.v1.users.destroy', ['user' => $targetUser], false))
        ->assertForbidden();
});

it('requires user.*.delete permission', function () {
    $targetUser = User::factory()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);

    deleteJson(route('api.v1.users.destroy', ['user' => $targetUser], false))
        ->assertForbidden();

    $user->givePermissionTo('user.*.delete');
    Sanctum::actingAs($user, ['api']);

    deleteJson(route('api.v1.users.destroy', ['user' => $targetUser], false))
        ->assertNoContent();
});

it('can soft-delete a user with valid permissions', function () {
    $targetUser = User::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.*.delete');
    Sanctum::actingAs($user, ['api']);

    $response = deleteJson(route('api.v1.users.destroy', ['user' => $targetUser], false));

    $response->assertNoContent();

    expect($targetUser->fresh()->trashed())->toBeTrue();
});

it('returns 404 when the requested user has an admin role', function (string $adminRole) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.*.delete');
    Sanctum::actingAs($user, ['api']);

    $adminUser = User::factory()->create();
    $adminUser->assignRole($adminRole);

    deleteJson(route('api.v1.users.destroy', ['user' => $adminUser], false))
        ->assertNotFound();

    expect($adminUser->fresh()->trashed())->toBeFalse();
})->with([
    'SaaS Global Admin' => [Authenticatable::SUPER_ADMIN_ROLE],
    'Partner Admin' => [Authenticatable::PARTNER_ADMIN_ROLE],
    'AI Admin' => [Authenticatable::AI_ADMIN_ROLE],
]);

it('returns 404 for a non-existent user', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.*.delete');
    Sanctum::actingAs($user, ['api']);

    deleteJson(route('api.v1.users.destroy', ['user' => 'non-existent-id'], false))
        ->assertNotFound();
});

it('rejects unauthenticated requests', function () {
    $targetUser = User::factory()->create();

    deleteJson(route('api.v1.users.destroy', ['user' => $targetUser], false))
        ->assertUnauthorized();
});

it('does not permanently delete the user', function () {
    $targetUser = User::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('user.*.delete');
    Sanctum::actingAs($user, ['api']);

    deleteJson(route('api.v1.users.destroy', ['user' => $targetUser], false))
        ->assertNoContent();

    expect(User::withTrashed()->find($targetUser->id))->not->toBeNull();
    expect(User::find($targetUser->id))->toBeNull();
});
