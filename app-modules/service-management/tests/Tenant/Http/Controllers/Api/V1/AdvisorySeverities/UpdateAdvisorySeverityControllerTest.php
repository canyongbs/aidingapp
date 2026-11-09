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
use App\Features\AdvisorySeverityNameUniquenessFeature;
use App\Models\SystemUser;
use CanyonGBS\Common\Enums\Color;
use Illuminate\Database\UniqueConstraintViolationException;

use function Pest\Laravel\patchJson;
use function Pest\Laravel\withoutExceptionHandling;
use function Tests\actingAsApiUser;

$actingAsAdvisorySeverityUpdater = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    return actingAsApiUser($user);
};

it('is gated with proper access control', function (): void {
    $advisorySeverity = AdvisorySeverity::factory()->create(['name' => 'Critical']);

    actingAsApiUser(SystemUser::factory()->create());
    patchJson(route('api.v1.advisory-severities.update', ['advisorySeverity' => $advisorySeverity], false), ['name' => 'Major'])
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.view-any');
    actingAsApiUser($user);
    patchJson(route('api.v1.advisory-severities.update', ['advisorySeverity' => $advisorySeverity], false), ['name' => 'Major'])
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.*.update');
    actingAsApiUser($user);
    patchJson(route('api.v1.advisory-severities.update', ['advisorySeverity' => $advisorySeverity], false), ['name' => 'Major'])
        ->assertForbidden();

    expect($advisorySeverity->refresh()->name)->toBe('Critical');
});

it('checks permissions before validating the request', function (): void {
    $advisorySeverity = AdvisorySeverity::factory()->create();

    actingAsApiUser(SystemUser::factory()->create());

    patchJson(route('api.v1.advisory-severities.update', ['advisorySeverity' => $advisorySeverity], false), ['color' => 'chartreuse'])
        ->assertForbidden();
});

it('can update an advisory severity', function () use ($actingAsAdvisorySeverityUpdater): void {
    $actingAsAdvisorySeverityUpdater();

    $advisorySeverity = AdvisorySeverity::factory()->color(Color::Red)->create(['name' => 'Critical']);

    patchJson(route('api.v1.advisory-severities.update', ['advisorySeverity' => $advisorySeverity], false), [
        'name' => 'Urgent',
        'color' => 'orange',
    ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Urgent')
        ->assertJsonPath('data.color', 'orange');

    $advisorySeverity->refresh();

    expect($advisorySeverity->name)->toBe('Urgent')
        ->and($advisorySeverity->color)->toBe(Color::Orange);
});

it('only changes the attributes included in the request', function () use ($actingAsAdvisorySeverityUpdater): void {
    $actingAsAdvisorySeverityUpdater();

    $advisorySeverity = AdvisorySeverity::factory()->color(Color::Red)->create(['name' => 'Critical']);

    patchJson(route('api.v1.advisory-severities.update', ['advisorySeverity' => $advisorySeverity], false), ['name' => 'Urgent'])
        ->assertOk();

    $advisorySeverity->refresh();

    expect($advisorySeverity->name)->toBe('Urgent')
        ->and($advisorySeverity->color)->toBe(Color::Red);
});

it('does not change the rank', function () use ($actingAsAdvisorySeverityUpdater): void {
    $actingAsAdvisorySeverityUpdater();

    $advisorySeverity = AdvisorySeverity::factory()->create(['sort' => 2]);

    patchJson(route('api.v1.advisory-severities.update', ['advisorySeverity' => $advisorySeverity], false), ['sort' => 1])
        ->assertOk()
        ->assertJsonPath('data.sort', 2);

    expect($advisorySeverity->refresh()->sort)->toBe(2);
});

it('validates the request', function (array $data, array $errors) use ($actingAsAdvisorySeverityUpdater): void {
    $actingAsAdvisorySeverityUpdater();

    $advisorySeverity = AdvisorySeverity::factory()->create(['name' => 'Critical']);

    patchJson(route('api.v1.advisory-severities.update', ['advisorySeverity' => $advisorySeverity], false), $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($errors);

    expect($advisorySeverity->refresh()->name)->toBe('Critical');
})->with([
    'name required' => [['name' => null], ['name']],
    'name string' => [['name' => 123], ['name']],
    'name max' => [['name' => str_repeat('a', 256)], ['name']],
    'color required' => [['color' => null], ['color']],
    'color enum' => [['color' => 'chartreuse'], ['color']],
    'color not a shade' => [['color' => 'black'], ['color']],
]);

it('requires a unique name regardless of case', function () use ($actingAsAdvisorySeverityUpdater): void {
    $actingAsAdvisorySeverityUpdater();

    AdvisorySeverity::factory()->create(['name' => 'Major']);
    $advisorySeverity = AdvisorySeverity::factory()->create(['name' => 'Critical']);

    patchJson(route('api.v1.advisory-severities.update', ['advisorySeverity' => $advisorySeverity], false), ['name' => 'MAJOR'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('allows saving with the same name as the current advisory severity', function () use ($actingAsAdvisorySeverityUpdater): void {
    $actingAsAdvisorySeverityUpdater();

    $advisorySeverity = AdvisorySeverity::factory()->create(['name' => 'Critical']);

    patchJson(route('api.v1.advisory-severities.update', ['advisorySeverity' => $advisorySeverity], false), ['name' => 'critical'])
        ->assertOk()
        ->assertJsonPath('data.name', 'critical');
});

it('returns not found for a soft deleted advisory severity', function () use ($actingAsAdvisorySeverityUpdater): void {
    $actingAsAdvisorySeverityUpdater();

    $advisorySeverity = AdvisorySeverity::factory()->create();
    $advisorySeverity->delete();

    patchJson(route('api.v1.advisory-severities.update', ['advisorySeverity' => $advisorySeverity], false), ['name' => 'Major'])
        ->assertNotFound();
});

// TODO: Cleanup Task (advisory-status-and-severity-name-uniqueness): delete this test
it('does not validate name uniqueness when the name uniqueness feature is inactive', function () use ($actingAsAdvisorySeverityUpdater): void {
    AdvisorySeverityNameUniquenessFeature::deactivate();

    $actingAsAdvisorySeverityUpdater();

    AdvisorySeverity::factory()->create(['name' => 'Major']);
    $advisorySeverity = AdvisorySeverity::factory()->create(['name' => 'Critical']);

    withoutExceptionHandling();

    // The unique index exists in tests, so the database rejects the duplicate instead of validation.
    expect(fn () => patchJson(route('api.v1.advisory-severities.update', ['advisorySeverity' => $advisorySeverity], false), ['name' => 'Major']))
        ->toThrow(UniqueConstraintViolationException::class);
});
