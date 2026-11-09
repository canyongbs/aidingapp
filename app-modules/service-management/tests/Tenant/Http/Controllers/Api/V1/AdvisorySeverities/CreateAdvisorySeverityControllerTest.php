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

use function Pest\Laravel\postJson;
use function Pest\Laravel\withoutExceptionHandling;
use function Tests\actingAsApiUser;

$actingAsAdvisorySeverityCreator = function (): SystemUser {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.create');

    return actingAsApiUser($user);
};

it('is gated with proper access control', function (): void {
    $data = ['name' => 'Critical', 'color' => 'red'];

    actingAsApiUser(SystemUser::factory()->create());
    postJson(route('api.v1.advisory-severities.store', [], false), $data)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.create');
    actingAsApiUser($user);
    postJson(route('api.v1.advisory-severities.store', [], false), $data)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('settings.view-any');
    actingAsApiUser($user);
    postJson(route('api.v1.advisory-severities.store', [], false), $data)
        ->assertForbidden();

    expect(AdvisorySeverity::query()->count())->toBe(0);
});

it('checks permissions before validating the request', function (): void {
    actingAsApiUser(SystemUser::factory()->create());

    postJson(route('api.v1.advisory-severities.store', [], false), [])
        ->assertForbidden();
});

it('can create an advisory severity', function () use ($actingAsAdvisorySeverityCreator): void {
    $actingAsAdvisorySeverityCreator();

    $response = postJson(route('api.v1.advisory-severities.store', [], false), [
        'name' => 'Critical',
        'color' => 'red',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Critical')
        ->assertJsonPath('data.color', 'red');

    $advisorySeverity = AdvisorySeverity::query()->findOrFail($response->json('data.id'));

    expect($advisorySeverity->name)->toBe('Critical')
        ->and($advisorySeverity->color)->toBe(Color::Red);
});

it('sorts a new advisory severity last', function () use ($actingAsAdvisorySeverityCreator): void {
    $actingAsAdvisorySeverityCreator();

    AdvisorySeverity::factory()->create(['sort' => 1]);
    AdvisorySeverity::factory()->create(['sort' => 2]);

    postJson(route('api.v1.advisory-severities.store', [], false), ['name' => 'Minor', 'color' => 'green'])
        ->assertCreated()
        ->assertJsonPath('data.sort', 3);
});

it('validates the request', function (array $data, array $errors) use ($actingAsAdvisorySeverityCreator): void {
    $actingAsAdvisorySeverityCreator();

    postJson(route('api.v1.advisory-severities.store', [], false), $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($errors);

    expect(AdvisorySeverity::query()->count())->toBe(0);
})->with([
    'name required' => [['color' => 'red'], ['name']],
    'name string' => [['name' => 123, 'color' => 'red'], ['name']],
    'name max' => [['name' => str_repeat('a', 256), 'color' => 'red'], ['name']],
    'color required' => [['name' => 'Critical'], ['color']],
    'color enum' => [['name' => 'Critical', 'color' => 'chartreuse'], ['color']],
    'color not a shade' => [['name' => 'Critical', 'color' => 'navy'], ['color']],
]);

it('requires a unique name regardless of case', function () use ($actingAsAdvisorySeverityCreator): void {
    $actingAsAdvisorySeverityCreator();

    AdvisorySeverity::factory()->create(['name' => 'Critical']);

    postJson(route('api.v1.advisory-severities.store', [], false), ['name' => 'CRITICAL', 'color' => 'red'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('allows the name of a soft deleted advisory severity to be reused', function () use ($actingAsAdvisorySeverityCreator): void {
    $actingAsAdvisorySeverityCreator();

    AdvisorySeverity::factory()->create(['name' => 'Critical'])->delete();

    postJson(route('api.v1.advisory-severities.store', [], false), ['name' => 'Critical', 'color' => 'red'])
        ->assertCreated();
});

// TODO: Cleanup Task (advisory-status-and-severity-name-uniqueness): delete this test
it('does not validate name uniqueness when the name uniqueness feature is inactive', function () use ($actingAsAdvisorySeverityCreator): void {
    AdvisorySeverityNameUniquenessFeature::deactivate();

    $actingAsAdvisorySeverityCreator();

    AdvisorySeverity::factory()->create(['name' => 'Critical']);

    withoutExceptionHandling();

    // The unique index exists in tests, so the database rejects the duplicate instead of validation.
    expect(fn () => postJson(route('api.v1.advisory-severities.store', [], false), ['name' => 'Critical', 'color' => 'red']))
        ->toThrow(UniqueConstraintViolationException::class);
});
