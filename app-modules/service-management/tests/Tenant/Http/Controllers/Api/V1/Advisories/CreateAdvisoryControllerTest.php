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

use AidingApp\Contact\Models\ContactType;
use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Enums\AdvisoryVisibility;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use App\Features\AdvisoryVisibilityFeature;
use App\Models\SystemUser;
use App\Settings\LicenseSettings;

use function Pest\Laravel\postJson;
use function Tests\actingAsApiUser;

$actingAsAdvisoryCreator = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['advisories.view-any', 'advisories.create']);

    return actingAsApiUser($user);
};

beforeEach(function (): void {
    AdvisorySeverity::factory()->create(['name' => 'Critical']);
    AdvisoryStatus::factory()->open()->create(['name' => 'Investigating']);
});

it('is gated with proper access control', function (): void {
    $data = ['title' => 'Email outage', 'description' => 'Email is down.', 'severity' => 'Critical', 'status' => 'Investigating'];

    actingAsApiUser(SystemUser::factory()->create());
    postJson(route('api.v1.advisories.store', [], false), $data)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('advisories.create');
    actingAsApiUser($user);
    postJson(route('api.v1.advisories.store', [], false), $data)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('advisories.view-any');
    actingAsApiUser($user);
    postJson(route('api.v1.advisories.store', [], false), $data)
        ->assertForbidden();

    expect(Advisory::query()->count())->toBe(0);
});

it('checks permissions before validating the request', function (): void {
    actingAsApiUser(SystemUser::factory()->create());

    postJson(route('api.v1.advisories.store', [], false), [])
        ->assertForbidden();
});

it('requires the advisory management addon', function () use ($actingAsAdvisoryCreator): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->advisoryManagement = false;
    $settings->save();

    $actingAsAdvisoryCreator();

    postJson(route('api.v1.advisories.store', [], false), ['title' => 'Email outage', 'description' => 'Email is down.', 'severity' => 'Critical', 'status' => 'Investigating'])
        ->assertForbidden();
});

it('can create an advisory, referencing related records by name regardless of case', function () use ($actingAsAdvisoryCreator): void {
    $actingAsAdvisoryCreator();

    $department = Department::factory()->create(['name' => 'IT']);

    $response = postJson(route('api.v1.advisories.store', [], false), [
        'title' => 'Email outage',
        'description' => 'Email is down.',
        'severity' => 'CRITICAL',
        'status' => 'investigating',
        'department' => 'it',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Email outage')
        ->assertJsonPath('data.description', 'Email is down.')
        ->assertJsonPath('data.severity.name', 'Critical')
        ->assertJsonPath('data.status.name', 'Investigating')
        ->assertJsonPath('data.department.name', 'IT')
        ->assertJsonPath('data.visibility', 'public')
        ->assertJsonPath('data.is_visibility_restricted', false)
        ->assertJsonPath('data.restricted_to_contact_types', []);

    $advisory = Advisory::query()->findOrFail($response->json('data.id'));

    expect($advisory->title)->toBe('Email outage')
        ->and($advisory->description)->toBe('Email is down.')
        ->and($advisory->severity->name)->toBe('Critical')
        ->and($advisory->status->name)->toBe('Investigating')
        ->and($advisory->assigned_department_id)->toBe($department->getKey())
        ->and($advisory->visibility)->toBe(AdvisoryVisibility::Public)
        ->and($advisory->is_visibility_restricted)->toBeFalse();
});

it('can create an advisory without a department', function () use ($actingAsAdvisoryCreator): void {
    $actingAsAdvisoryCreator();

    postJson(route('api.v1.advisories.store', [], false), [
        'title' => 'Email outage',
        'description' => 'Email is down.',
        'severity' => 'Critical',
        'status' => 'Investigating',
        'department' => null,
    ])
        ->assertCreated()
        ->assertJsonPath('data.department', null);
});

it('can restrict an advisory to contact types, referenced by name regardless of case', function () use ($actingAsAdvisoryCreator): void {
    $actingAsAdvisoryCreator();

    $student = ContactType::factory()->create(['name' => 'Student']);
    $staff = ContactType::factory()->create(['name' => 'Staff']);
    ContactType::factory()->create(['name' => 'Alumni']);

    $response = postJson(route('api.v1.advisories.store', [], false), [
        'title' => 'Email outage',
        'description' => 'Email is down.',
        'severity' => 'Critical',
        'status' => 'Investigating',
        'visibility' => 'authenticated',
        'is_visibility_restricted' => true,
        'restricted_to_contact_types' => ['student', 'STAFF'],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.visibility', 'authenticated')
        ->assertJsonPath('data.is_visibility_restricted', true);

    $advisory = Advisory::query()->findOrFail($response->json('data.id'));

    expect($advisory->restrictedToContactTypes->modelKeys())->toEqualCanonicalizing([$student->getKey(), $staff->getKey()]);
});

it('requires contact types when the advisory is restricted to them', function () use ($actingAsAdvisoryCreator): void {
    $actingAsAdvisoryCreator();

    postJson(route('api.v1.advisories.store', [], false), [
        'title' => 'Email outage',
        'description' => 'Email is down.',
        'severity' => 'Critical',
        'status' => 'Investigating',
        'visibility' => 'authenticated',
        'is_visibility_restricted' => true,
        'restricted_to_contact_types' => [],
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['restricted_to_contact_types']);

    expect(Advisory::query()->count())->toBe(0);
});

it('validates the request', function (array $data, array $errors) use ($actingAsAdvisoryCreator): void {
    $actingAsAdvisoryCreator();

    AdvisorySeverity::factory()->create(['name' => 'Retired severity'])->delete();
    AdvisoryStatus::factory()->create(['name' => 'Retired status'])->delete();
    ContactType::factory()->count(2)->create(['name' => 'Duplicate']);

    postJson(route('api.v1.advisories.store', [], false), [
        'title' => 'Email outage',
        'description' => 'Email is down.',
        'severity' => 'Critical',
        'status' => 'Investigating',
        ...$data,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors($errors);

    expect(Advisory::query()->count())->toBe(0);
})->with([
    'title required' => [['title' => null], ['title']],
    'title max' => [['title' => str_repeat('a', 256)], ['title']],
    'description required' => [['description' => null], ['description']],
    'severity required' => [['severity' => null], ['severity']],
    'severity exists' => [['severity' => 'Unknown'], ['severity']],
    'severity not soft deleted' => [['severity' => 'Retired severity'], ['severity']],
    'status required' => [['status' => null], ['status']],
    'status exists' => [['status' => 'Unknown'], ['status']],
    'status not soft deleted' => [['status' => 'Retired status'], ['status']],
    'department exists' => [['department' => 'Unknown'], ['department']],
    'visibility enum' => [['visibility' => 'private'], ['visibility']],
    'visibility not null' => [['visibility' => null], ['visibility']],
    'is_visibility_restricted boolean' => [['is_visibility_restricted' => 'yes'], ['is_visibility_restricted']],
    'restricted_to_contact_types array' => [['restricted_to_contact_types' => 'Student'], ['restricted_to_contact_types']],
    'restricted_to_contact_types exists' => [['restricted_to_contact_types' => ['Unknown']], ['restricted_to_contact_types.0']],
    'restricted_to_contact_types not ambiguous' => [['restricted_to_contact_types' => ['Duplicate']], ['restricted_to_contact_types.0']],
]);

it('rejects duplicate contact types regardless of case', function () use ($actingAsAdvisoryCreator): void {
    $actingAsAdvisoryCreator();

    ContactType::factory()->create(['name' => 'Student']);

    postJson(route('api.v1.advisories.store', [], false), [
        'title' => 'Email outage',
        'description' => 'Email is down.',
        'severity' => 'Critical',
        'status' => 'Investigating',
        'visibility' => 'authenticated',
        'is_visibility_restricted' => true,
        'restricted_to_contact_types' => ['Student', 'student'],
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['restricted_to_contact_types.0', 'restricted_to_contact_types.1']);
});

it('ignores the visibility fields when `AdvisoryVisibilityFeature` is inactive', function () use ($actingAsAdvisoryCreator): void {
    AdvisoryVisibilityFeature::deactivate();

    $actingAsAdvisoryCreator();

    $response = postJson(route('api.v1.advisories.store', [], false), [
        'title' => 'Email outage',
        'description' => 'Email is down.',
        'severity' => 'Critical',
        'status' => 'Investigating',
        'visibility' => 'authenticated',
        'is_visibility_restricted' => true,
        'restricted_to_contact_types' => ['Unknown'],
    ]);

    $response->assertCreated();

    expect($response->json('data'))->not->toHaveKeys(['visibility', 'is_visibility_restricted', 'restricted_to_contact_types']);

    $advisory = Advisory::query()->findOrFail($response->json('data.id'));

    expect($advisory->visibility)->toBe(AdvisoryVisibility::Public)
        ->and($advisory->is_visibility_restricted)->toBeFalse()
        ->and($advisory->restrictedToContactTypes)->toBeEmpty();
});
