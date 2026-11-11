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
use App\Features\AdvisoryStatusNameUniquenessFeature;
use App\Models\SystemUser;
use Illuminate\Database\UniqueConstraintViolationException;

use function Pest\Laravel\patchJson;
use function Pest\Laravel\withoutExceptionHandling;
use function Tests\actingAsApiUser;

$actingAsAdvisoryStatusUpdater = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    return actingAsApiUser($user);
};

it('is gated with proper access control', function (): void {
    $advisoryStatus = AdvisoryStatus::factory()->create(['name' => 'Investigating']);

    actingAsApiUser(SystemUser::factory()->create());
    patchJson(route('api.v1.advisory-statuses.update', ['advisoryStatus' => $advisoryStatus], false), ['name' => 'Monitoring'])
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.view-any');
    actingAsApiUser($user);
    patchJson(route('api.v1.advisory-statuses.update', ['advisoryStatus' => $advisoryStatus], false), ['name' => 'Monitoring'])
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.*.update');
    actingAsApiUser($user);
    patchJson(route('api.v1.advisory-statuses.update', ['advisoryStatus' => $advisoryStatus], false), ['name' => 'Monitoring'])
        ->assertForbidden();

    expect($advisoryStatus->refresh()->name)->toBe('Investigating');
});

it('checks permissions before validating the request', function (): void {
    $advisoryStatus = AdvisoryStatus::factory()->create();

    actingAsApiUser(SystemUser::factory()->create());

    patchJson(route('api.v1.advisory-statuses.update', ['advisoryStatus' => $advisoryStatus], false), ['classification' => 'closed'])
        ->assertForbidden();
});

it('can update an advisory status', function () use ($actingAsAdvisoryStatusUpdater): void {
    $actingAsAdvisoryStatusUpdater();

    $advisoryStatus = AdvisoryStatus::factory()->open()->create(['name' => 'Investigating']);

    patchJson(route('api.v1.advisory-statuses.update', ['advisoryStatus' => $advisoryStatus], false), [
        'name' => 'Fixed',
        'classification' => 'resolved',
    ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Fixed')
        ->assertJsonPath('data.classification', 'resolved');

    $advisoryStatus->refresh();

    expect($advisoryStatus->name)->toBe('Fixed')
        ->and($advisoryStatus->classification)->toBe(SystemAdvisoryStatusClassification::Resolved);
});

it('only changes the attributes included in the request', function () use ($actingAsAdvisoryStatusUpdater): void {
    $actingAsAdvisoryStatusUpdater();

    $advisoryStatus = AdvisoryStatus::factory()->open()->create(['name' => 'Investigating']);

    patchJson(route('api.v1.advisory-statuses.update', ['advisoryStatus' => $advisoryStatus], false), ['name' => 'Monitoring'])
        ->assertOk();

    $advisoryStatus->refresh();

    expect($advisoryStatus->name)->toBe('Monitoring')
        ->and($advisoryStatus->classification)->toBe(SystemAdvisoryStatusClassification::Open);
});

it('validates the request', function (array $data, array $errors) use ($actingAsAdvisoryStatusUpdater): void {
    $actingAsAdvisoryStatusUpdater();

    $advisoryStatus = AdvisoryStatus::factory()->create(['name' => 'Investigating']);

    patchJson(route('api.v1.advisory-statuses.update', ['advisoryStatus' => $advisoryStatus], false), $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($errors);

    expect($advisoryStatus->refresh()->name)->toBe('Investigating');
})->with([
    'name required' => [['name' => null], ['name']],
    'name string' => [['name' => 123], ['name']],
    'name max' => [['name' => str_repeat('a', 256)], ['name']],
    'classification required' => [['classification' => null], ['classification']],
    'classification enum' => [['classification' => 'closed'], ['classification']],
]);

it('requires a unique name regardless of case', function () use ($actingAsAdvisoryStatusUpdater): void {
    $actingAsAdvisoryStatusUpdater();

    AdvisoryStatus::factory()->create(['name' => 'Monitoring']);
    $advisoryStatus = AdvisoryStatus::factory()->create(['name' => 'Investigating']);

    patchJson(route('api.v1.advisory-statuses.update', ['advisoryStatus' => $advisoryStatus], false), ['name' => 'MONITORING'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('allows saving with the same name as the current advisory status', function () use ($actingAsAdvisoryStatusUpdater): void {
    $actingAsAdvisoryStatusUpdater();

    $advisoryStatus = AdvisoryStatus::factory()->create(['name' => 'Investigating']);

    patchJson(route('api.v1.advisory-statuses.update', ['advisoryStatus' => $advisoryStatus], false), ['name' => 'investigating'])
        ->assertOk()
        ->assertJsonPath('data.name', 'investigating');
});

it('returns not found for a soft deleted advisory status', function () use ($actingAsAdvisoryStatusUpdater): void {
    $actingAsAdvisoryStatusUpdater();

    $advisoryStatus = AdvisoryStatus::factory()->create();
    $advisoryStatus->delete();

    patchJson(route('api.v1.advisory-statuses.update', ['advisoryStatus' => $advisoryStatus], false), ['name' => 'Monitoring'])
        ->assertNotFound();
});

// TODO: Cleanup Task (advisory-status-and-severity-name-uniqueness): delete this test
it('does not validate name uniqueness when the name uniqueness feature is inactive', function () use ($actingAsAdvisoryStatusUpdater): void {
    AdvisoryStatusNameUniquenessFeature::deactivate();

    $actingAsAdvisoryStatusUpdater();

    AdvisoryStatus::factory()->create(['name' => 'Monitoring']);
    $advisoryStatus = AdvisoryStatus::factory()->create(['name' => 'Investigating']);

    withoutExceptionHandling();

    // The unique index exists in tests, so the database rejects the duplicate instead of validation.
    expect(fn () => patchJson(route('api.v1.advisory-statuses.update', ['advisoryStatus' => $advisoryStatus], false), ['name' => 'Monitoring']))
        ->toThrow(UniqueConstraintViolationException::class);
});
