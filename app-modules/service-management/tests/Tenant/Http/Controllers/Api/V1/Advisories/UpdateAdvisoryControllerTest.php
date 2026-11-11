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

use function Pest\Laravel\patchJson;
use function Tests\actingAsApiUser;

$actingAsAdvisoryUpdater = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['advisory.view-any', 'advisory.*.update']);

    return actingAsApiUser($user);
};

it('is gated with proper access control', function (): void {
    $advisory = Advisory::factory()->create(['title' => 'Email outage']);

    actingAsApiUser(SystemUser::factory()->create());
    patchJson(route('api.v1.advisories.update', ['advisory' => $advisory], false), ['title' => 'Changed'])
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('advisory.view-any');
    actingAsApiUser($user);
    patchJson(route('api.v1.advisories.update', ['advisory' => $advisory], false), ['title' => 'Changed'])
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('advisory.*.update');
    actingAsApiUser($user);
    patchJson(route('api.v1.advisories.update', ['advisory' => $advisory], false), ['title' => 'Changed'])
        ->assertForbidden();

    expect($advisory->refresh()->title)->toBe('Email outage');
});

it('checks permissions before validating the request', function (): void {
    actingAsApiUser(SystemUser::factory()->create());

    patchJson(route('api.v1.advisories.update', ['advisory' => Advisory::factory()->create()], false), ['title' => null])
        ->assertForbidden();
});

it('requires the advisory management addon', function () use ($actingAsAdvisoryUpdater): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->advisoryManagement = false;
    $settings->save();

    $actingAsAdvisoryUpdater();

    patchJson(route('api.v1.advisories.update', ['advisory' => Advisory::factory()->create()], false), ['title' => 'Changed'])
        ->assertForbidden();
});

it('only changes the attributes included in the request', function () use ($actingAsAdvisoryUpdater): void {
    $actingAsAdvisoryUpdater();

    $contactType = ContactType::factory()->create();
    $advisory = Advisory::factory()->restrictedToContactTypes($contactType)->create(['title' => 'Email outage']);
    $original = $advisory->only(['description', 'severity_id', 'status_id', 'assigned_department_id', 'visibility', 'is_visibility_restricted']);

    patchJson(route('api.v1.advisories.update', ['advisory' => $advisory], false), ['title' => 'Email outage resolved'])
        ->assertOk()
        ->assertJsonPath('data.title', 'Email outage resolved');

    $advisory->refresh();

    expect($advisory->title)->toBe('Email outage resolved')
        ->and($advisory->only(array_keys($original)))->toBe($original)
        ->and($advisory->restrictedToContactTypes->modelKeys())->toBe([$contactType->getKey()]);
});

it('can change the related records, referenced by name regardless of case', function () use ($actingAsAdvisoryUpdater): void {
    $actingAsAdvisoryUpdater();

    $advisory = Advisory::factory()->create();
    $severity = AdvisorySeverity::factory()->create(['name' => 'Minor']);
    $status = AdvisoryStatus::factory()->resolved()->create(['name' => 'Resolved']);
    $department = Department::factory()->create(['name' => 'Facilities']);

    patchJson(route('api.v1.advisories.update', ['advisory' => $advisory], false), [
        'severity' => 'MINOR',
        'status' => 'resolved',
        'department' => 'facilities',
    ])
        ->assertOk()
        ->assertJsonPath('data.severity.name', 'Minor')
        ->assertJsonPath('data.status.name', 'Resolved')
        ->assertJsonPath('data.department.name', 'Facilities');

    $advisory->refresh();

    expect($advisory->severity_id)->toBe($severity->getKey())
        ->and($advisory->status_id)->toBe($status->getKey())
        ->and($advisory->assigned_department_id)->toBe($department->getKey());
});

it('can unassign the department', function () use ($actingAsAdvisoryUpdater): void {
    $actingAsAdvisoryUpdater();

    $advisory = Advisory::factory()->create();

    patchJson(route('api.v1.advisories.update', ['advisory' => $advisory], false), ['department' => null])
        ->assertOk()
        ->assertJsonPath('data.department', null);

    expect($advisory->refresh()->assigned_department_id)->toBeNull();
});

it('replaces the contact types an advisory is restricted to', function () use ($actingAsAdvisoryUpdater): void {
    $actingAsAdvisoryUpdater();

    $student = ContactType::factory()->create(['name' => 'Student']);
    $staff = ContactType::factory()->create(['name' => 'Staff']);
    $advisory = Advisory::factory()->restrictedToContactTypes($student)->create();

    patchJson(route('api.v1.advisories.update', ['advisory' => $advisory], false), ['restricted_to_contact_types' => ['staff']])
        ->assertOk()
        ->assertJsonPath('data.restricted_to_contact_types', [['id' => $staff->getKey(), 'name' => 'Staff']]);

    expect($advisory->refresh()->restrictedToContactTypes->modelKeys())->toBe([$staff->getKey()]);
});

it('can restrict a public advisory to contact types', function () use ($actingAsAdvisoryUpdater): void {
    $actingAsAdvisoryUpdater();

    $student = ContactType::factory()->create(['name' => 'Student']);
    $advisory = Advisory::factory()->create();

    patchJson(route('api.v1.advisories.update', ['advisory' => $advisory], false), [
        'visibility' => 'authenticated',
        'is_visibility_restricted' => true,
        'restricted_to_contact_types' => ['Student'],
    ])
        ->assertOk()
        ->assertJsonPath('data.visibility', 'authenticated')
        ->assertJsonPath('data.is_visibility_restricted', true);

    $advisory->refresh();

    expect($advisory->visibility)->toBe(AdvisoryVisibility::Authenticated)
        ->and($advisory->is_visibility_restricted)->toBeTrue()
        ->and($advisory->restrictedToContactTypes->modelKeys())->toBe([$student->getKey()]);
});

it('requires contact types when the advisory would be restricted without any', function (array $data) use ($actingAsAdvisoryUpdater): void {
    $actingAsAdvisoryUpdater();

    $advisory = Advisory::factory()->authenticated()->create();

    patchJson(route('api.v1.advisories.update', ['advisory' => $advisory], false), $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['restricted_to_contact_types']);

    expect($advisory->refresh()->is_visibility_restricted)->toBeFalse();
})->with([
    'restricting without contact types' => [['is_visibility_restricted' => true]],
    'restricting with an empty list' => [['is_visibility_restricted' => true, 'restricted_to_contact_types' => []]],
]);

it('requires contact types when removing them from a restricted advisory', function () use ($actingAsAdvisoryUpdater): void {
    $actingAsAdvisoryUpdater();

    $contactType = ContactType::factory()->create();
    $advisory = Advisory::factory()->restrictedToContactTypes($contactType)->create();

    patchJson(route('api.v1.advisories.update', ['advisory' => $advisory], false), ['restricted_to_contact_types' => null])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['restricted_to_contact_types']);

    expect($advisory->refresh()->restrictedToContactTypes->modelKeys())->toBe([$contactType->getKey()]);
});

it('validates the request', function (array $data, array $errors) use ($actingAsAdvisoryUpdater): void {
    $actingAsAdvisoryUpdater();

    $advisory = Advisory::factory()->create(['title' => 'Email outage']);
    AdvisorySeverity::factory()->create(['name' => 'Retired severity'])->delete();
    AdvisoryStatus::factory()->create(['name' => 'Retired status'])->delete();

    patchJson(route('api.v1.advisories.update', ['advisory' => $advisory], false), $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($errors);

    expect($advisory->refresh()->title)->toBe('Email outage');
})->with([
    'title not null' => [['title' => null], ['title']],
    'title max' => [['title' => str_repeat('a', 256)], ['title']],
    'description not null' => [['description' => null], ['description']],
    'severity not null' => [['severity' => null], ['severity']],
    'severity exists' => [['severity' => 'Unknown'], ['severity']],
    'severity not soft deleted' => [['severity' => 'Retired severity'], ['severity']],
    'status not null' => [['status' => null], ['status']],
    'status exists' => [['status' => 'Unknown'], ['status']],
    'status not soft deleted' => [['status' => 'Retired status'], ['status']],
    'department exists' => [['department' => 'Unknown'], ['department']],
    'visibility enum' => [['visibility' => 'private'], ['visibility']],
    'visibility not null' => [['visibility' => null], ['visibility']],
    'is_visibility_restricted boolean' => [['is_visibility_restricted' => 'yes'], ['is_visibility_restricted']],
    'restricted_to_contact_types array' => [['restricted_to_contact_types' => 'Student'], ['restricted_to_contact_types']],
    'restricted_to_contact_types exists' => [['restricted_to_contact_types' => ['Unknown']], ['restricted_to_contact_types.0']],
]);

it('ignores the visibility fields when `AdvisoryVisibilityFeature` is inactive', function () use ($actingAsAdvisoryUpdater): void {
    AdvisoryVisibilityFeature::deactivate();

    $actingAsAdvisoryUpdater();

    $advisory = Advisory::factory()->create(['title' => 'Email outage']);

    $response = patchJson(route('api.v1.advisories.update', ['advisory' => $advisory], false), [
        'title' => 'Email outage resolved',
        'visibility' => 'authenticated',
        'is_visibility_restricted' => true,
        'restricted_to_contact_types' => ['Unknown'],
    ]);

    $response->assertOk();

    expect($response->json('data'))->not->toHaveKeys(['visibility', 'is_visibility_restricted', 'restricted_to_contact_types']);

    $advisory->refresh();

    expect($advisory->title)->toBe('Email outage resolved')
        ->and($advisory->visibility)->toBe(AdvisoryVisibility::Public)
        ->and($advisory->is_visibility_restricted)->toBeFalse();
});

it('returns not found for a soft deleted advisory', function () use ($actingAsAdvisoryUpdater): void {
    $actingAsAdvisoryUpdater();

    $advisory = Advisory::factory()->create();
    $advisory->delete();

    patchJson(route('api.v1.advisories.update', ['advisory' => $advisory], false), ['title' => 'Changed'])
        ->assertNotFound();
});
