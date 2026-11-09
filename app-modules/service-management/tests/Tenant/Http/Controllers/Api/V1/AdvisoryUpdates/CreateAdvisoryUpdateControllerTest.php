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

use function Pest\Laravel\postJson;
use function Pest\Laravel\travelTo;
use function Tests\actingAsApiUser;

$creatorPermissions = ['advisory.view-any', 'advisory.*.view', 'advisory_update.view-any', 'advisory_update.create'];

$actingAsAdvisoryUpdateCreator = function (array $extraPermissions = []) use ($creatorPermissions): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo([...$creatorPermissions, ...$extraPermissions]);

    return actingAsApiUser($user);
};

$openAdvisory = fn (): Advisory => Advisory::factory()->for(AdvisoryStatus::factory()->open(), 'status')->create();

it('is gated with proper access control', function (array $permissions) use ($openAdvisory): void {
    $advisory = $openAdvisory();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo($permissions);
    actingAsApiUser($user);

    postJson(route('api.v1.advisories.updates.store', ['advisory' => $advisory], false), ['title' => 'Investigating', 'update' => 'We are looking into it.'])
        ->assertForbidden();

    expect(AdvisoryUpdate::query()->count())->toBe(0);
})->with([
    'no permissions' => [[]],
    'missing advisory view any' => [['advisory.*.view', 'advisory_update.view-any', 'advisory_update.create']],
    'missing advisory view' => [['advisory.view-any', 'advisory_update.view-any', 'advisory_update.create']],
    'missing advisory update view any' => [['advisory.view-any', 'advisory.*.view', 'advisory_update.create']],
    'missing advisory update create' => [['advisory.view-any', 'advisory.*.view', 'advisory_update.view-any']],
]);

it('requires permission to update the advisory to change its status', function () use ($actingAsAdvisoryUpdateCreator, $openAdvisory): void {
    $actingAsAdvisoryUpdateCreator();

    $advisory = $openAdvisory();
    AdvisoryStatus::factory()->resolved()->create(['name' => 'Resolved']);

    postJson(route('api.v1.advisories.updates.store', ['advisory' => $advisory], false), [
        'title' => 'Resolved',
        'update' => 'Email is working again.',
        'status' => 'Resolved',
    ])
        ->assertForbidden();

    expect(AdvisoryUpdate::query()->count())->toBe(0);
});

it('checks permissions before validating the request', function () use ($openAdvisory): void {
    actingAsApiUser(SystemUser::factory()->create());

    postJson(route('api.v1.advisories.updates.store', ['advisory' => $openAdvisory()], false), [])
        ->assertForbidden();
});

it('requires the advisory management and service management addons', function (string $addon) use ($actingAsAdvisoryUpdateCreator, $openAdvisory): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->{$addon} = false;
    $settings->save();

    $actingAsAdvisoryUpdateCreator();

    postJson(route('api.v1.advisories.updates.store', ['advisory' => $openAdvisory()], false), ['title' => 'Investigating', 'update' => 'We are looking into it.'])
        ->assertForbidden();
})->with(['advisoryManagement', 'serviceManagement']);

it('can add a public update dated now by default', function () use ($actingAsAdvisoryUpdateCreator, $openAdvisory): void {
    $actingAsAdvisoryUpdateCreator();

    travelTo(now()->startOfSecond());

    $advisory = $openAdvisory();

    $response = postJson(route('api.v1.advisories.updates.store', ['advisory' => $advisory], false), [
        'title' => 'Investigating',
        'update' => 'We are looking into it.',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Investigating')
        ->assertJsonPath('data.update', 'We are looking into it.')
        ->assertJsonPath('data.internal', false);

    $advisoryUpdate = AdvisoryUpdate::query()->findOrFail($response->json('data.id'));

    expect($advisoryUpdate->advisory_id)->toBe($advisory->getKey())
        ->and($advisoryUpdate->internal)->toBeFalse()
        ->and($advisoryUpdate->date->equalTo(now()))->toBeTrue();
});

it('can add an internal update with a date', function () use ($actingAsAdvisoryUpdateCreator, $openAdvisory): void {
    $actingAsAdvisoryUpdateCreator();

    $advisory = $openAdvisory();

    $response = postJson(route('api.v1.advisories.updates.store', ['advisory' => $advisory], false), [
        'title' => 'Investigating',
        'update' => 'We are looking into it.',
        'internal' => true,
        'date' => '2026-11-01T09:30:00Z',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.internal', true);

    $advisoryUpdate = AdvisoryUpdate::query()->findOrFail($response->json('data.id'));

    expect($advisoryUpdate->internal)->toBeTrue()
        ->and($advisoryUpdate->date->toIso8601ZuluString())->toBe('2026-11-01T09:30:00Z');
});

it('can move the advisory to another status, referenced by name regardless of case', function () use ($actingAsAdvisoryUpdateCreator, $openAdvisory): void {
    $actingAsAdvisoryUpdateCreator(['advisory.*.update']);

    $advisory = $openAdvisory();
    $resolved = AdvisoryStatus::factory()->resolved()->create(['name' => 'Resolved']);

    postJson(route('api.v1.advisories.updates.store', ['advisory' => $advisory], false), [
        'title' => 'Resolved',
        'update' => 'Email is working again.',
        'status' => 'RESOLVED',
    ])
        ->assertCreated();

    expect($advisory->refresh()->status_id)->toBe($resolved->getKey())
        ->and($advisory->advisoryUpdates()->count())->toBe(1);
});

it('cannot add an update to a resolved advisory', function () use ($actingAsAdvisoryUpdateCreator): void {
    $actingAsAdvisoryUpdateCreator(['advisory.*.update']);

    $advisory = Advisory::factory()->for(AdvisoryStatus::factory()->resolved(), 'status')->create();
    $statusId = $advisory->status_id;
    AdvisoryStatus::factory()->open()->create(['name' => 'Investigating']);

    postJson(route('api.v1.advisories.updates.store', ['advisory' => $advisory], false), [
        'title' => 'Reopened',
        'update' => 'The issue is back.',
        'status' => 'Investigating',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['advisory']);

    expect(AdvisoryUpdate::query()->count())->toBe(0)
        ->and($advisory->refresh()->status_id)->toBe($statusId);
});

it('validates the request', function (array $data, array $errors) use ($actingAsAdvisoryUpdateCreator, $openAdvisory): void {
    $actingAsAdvisoryUpdateCreator(['advisory.*.update']);

    AdvisoryStatus::factory()->create(['name' => 'Retired status'])->delete();

    postJson(route('api.v1.advisories.updates.store', ['advisory' => $openAdvisory()], false), [
        'title' => 'Investigating',
        'update' => 'We are looking into it.',
        ...$data,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors($errors);

    expect(AdvisoryUpdate::query()->count())->toBe(0);
})->with([
    'title required' => [['title' => null], ['title']],
    'title max' => [['title' => str_repeat('a', 256)], ['title']],
    'update required' => [['update' => null], ['update']],
    'internal boolean' => [['internal' => 'yes'], ['internal']],
    'internal not null' => [['internal' => null], ['internal']],
    'date date' => [['date' => 'soon'], ['date']],
    'date not null' => [['date' => null], ['date']],
    'status exists' => [['status' => 'Unknown'], ['status']],
    'status not soft deleted' => [['status' => 'Retired status'], ['status']],
    'status not null' => [['status' => null], ['status']],
]);

it('returns not found for a soft deleted advisory', function () use ($actingAsAdvisoryUpdateCreator, $openAdvisory): void {
    $actingAsAdvisoryUpdateCreator();

    $advisory = $openAdvisory();
    $advisory->delete();

    postJson(route('api.v1.advisories.updates.store', ['advisory' => $advisory], false), ['title' => 'Investigating', 'update' => 'We are looking into it.'])
        ->assertNotFound();
});
