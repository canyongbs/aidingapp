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
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use AidingApp\ServiceManagement\Models\AdvisoryUpdate;
use App\Models\SystemUser;
use App\Settings\LicenseSettings;

use function Pest\Laravel\patchJson;
use function Tests\actingAsApiUser;

$updaterPermissions = ['advisories.view-any', 'advisories.*.view', 'advisories.*.update'];

$actingAsAdvisoryUpdateUpdater = function () use ($updaterPermissions): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo($updaterPermissions);

    return actingAsApiUser($user);
};

$openAdvisoryUpdate = fn (array $attributes = []): AdvisoryUpdate => AdvisoryUpdate::factory()
    ->for(Advisory::factory()->for(AdvisoryStatus::factory()->open(), 'status'))
    ->create($attributes);

$updateUrl = fn (AdvisoryUpdate $advisoryUpdate): string => route('api.v1.advisories.updates.update', ['advisory' => $advisoryUpdate->advisory, 'advisoryUpdate' => $advisoryUpdate], false);

it('is gated with proper access control', function (array $permissions) use ($openAdvisoryUpdate, $updateUrl): void {
    $advisoryUpdate = $openAdvisoryUpdate(['title' => 'Investigating']);

    $user = SystemUser::factory()->create();
    $user->givePermissionTo($permissions);
    actingAsApiUser($user);

    patchJson($updateUrl($advisoryUpdate), ['title' => 'Changed'])
        ->assertForbidden();

    expect($advisoryUpdate->refresh()->title)->toBe('Investigating');
})->with([
    'no permissions' => [[]],
    'missing view any' => [['advisories.*.view', 'advisories.*.update']],
    'missing view' => [['advisories.view-any', 'advisories.*.update']],
    'missing update' => [['advisories.view-any', 'advisories.*.view']],
]);

it('checks permissions before validating the request', function () use ($openAdvisoryUpdate, $updateUrl): void {
    actingAsApiUser(SystemUser::factory()->create());

    patchJson($updateUrl($openAdvisoryUpdate()), ['title' => null])
        ->assertForbidden();
});

it('requires the advisory management and service management addons', function (string $addon) use ($actingAsAdvisoryUpdateUpdater, $openAdvisoryUpdate, $updateUrl): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->{$addon} = false;
    $settings->save();

    $actingAsAdvisoryUpdateUpdater();

    patchJson($updateUrl($openAdvisoryUpdate()), ['title' => 'Changed'])
        ->assertForbidden();
})->with(['advisoryManagement', 'serviceManagement']);

it('only changes the attributes included in the request', function () use ($actingAsAdvisoryUpdateUpdater, $openAdvisoryUpdate, $updateUrl): void {
    $actingAsAdvisoryUpdateUpdater();

    $advisoryUpdate = $openAdvisoryUpdate(['title' => 'Investigating', 'internal' => false]);
    $original = $advisoryUpdate->only(['update', 'date']);
    $statusId = $advisoryUpdate->advisory->status_id;

    patchJson($updateUrl($advisoryUpdate), ['title' => 'Identified', 'internal' => true])
        ->assertOk()
        ->assertJsonPath('data.title', 'Identified')
        ->assertJsonPath('data.internal', true);

    $advisoryUpdate->refresh();

    expect($advisoryUpdate->title)->toBe('Identified')
        ->and($advisoryUpdate->internal)->toBeTrue()
        ->and($advisoryUpdate->update)->toBe($original['update'])
        ->and($advisoryUpdate->date->equalTo($original['date']))->toBeTrue()
        ->and($advisoryUpdate->advisory->refresh()->status_id)->toBe($statusId);
});

it('can change the date, converting a timezone offset to the same moment', function () use ($actingAsAdvisoryUpdateUpdater, $openAdvisoryUpdate, $updateUrl): void {
    $actingAsAdvisoryUpdateUpdater();

    $advisoryUpdate = $openAdvisoryUpdate();

    patchJson($updateUrl($advisoryUpdate), ['date' => '2026-11-01T10:30:00+01:00'])
        ->assertOk();

    expect($advisoryUpdate->refresh()->date->toIso8601ZuluString())->toBe('2026-11-01T09:30:00Z');
});

it('can move the advisory to another status, referenced by name regardless of case', function () use ($actingAsAdvisoryUpdateUpdater, $openAdvisoryUpdate, $updateUrl): void {
    $actingAsAdvisoryUpdateUpdater();

    $advisoryUpdate = $openAdvisoryUpdate();
    $resolved = AdvisoryStatus::factory()->resolved()->create(['name' => 'Resolved']);

    patchJson($updateUrl($advisoryUpdate), ['status' => 'resolved'])
        ->assertOk();

    expect($advisoryUpdate->advisory->refresh()->status_id)->toBe($resolved->getKey());
});

it('cannot edit an update on a resolved advisory', function () use ($actingAsAdvisoryUpdateUpdater, $updateUrl): void {
    $actingAsAdvisoryUpdateUpdater();

    $advisoryUpdate = AdvisoryUpdate::factory()
        ->for(Advisory::factory()->for(AdvisoryStatus::factory()->resolved(), 'status'))
        ->create(['title' => 'Resolved']);

    patchJson($updateUrl($advisoryUpdate), ['title' => 'Changed'])
        ->assertForbidden();

    expect($advisoryUpdate->refresh()->title)->toBe('Resolved');
});

it('validates the request', function (array $data, array $errors) use ($actingAsAdvisoryUpdateUpdater, $openAdvisoryUpdate, $updateUrl): void {
    $actingAsAdvisoryUpdateUpdater();

    $advisoryUpdate = $openAdvisoryUpdate(['title' => 'Investigating']);
    AdvisoryStatus::factory()->create(['name' => 'Retired status'])->delete();

    patchJson($updateUrl($advisoryUpdate), $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($errors);

    expect($advisoryUpdate->refresh()->title)->toBe('Investigating');
})->with([
    'title not null' => [['title' => null], ['title']],
    'title max' => [['title' => str_repeat('a', 256)], ['title']],
    'update not null' => [['update' => null], ['update']],
    'internal boolean' => [['internal' => 'yes'], ['internal']],
    'date date' => [['date' => 'soon'], ['date']],
    'date string' => [['date' => 20261101], ['date']],
    'status exists' => [['status' => 'Unknown'], ['status']],
    'status not soft deleted' => [['status' => 'Retired status'], ['status']],
]);

it('returns not found for an update requested through a different advisory', function () use ($actingAsAdvisoryUpdateUpdater, $openAdvisoryUpdate): void {
    $actingAsAdvisoryUpdateUpdater();

    $advisoryUpdate = $openAdvisoryUpdate(['title' => 'Investigating']);

    patchJson(route('api.v1.advisories.updates.update', ['advisory' => Advisory::factory()->create(), 'advisoryUpdate' => $advisoryUpdate], false), ['title' => 'Changed'])
        ->assertNotFound();

    expect($advisoryUpdate->refresh()->title)->toBe('Investigating');
});
