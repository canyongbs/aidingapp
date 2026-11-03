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

use AidingApp\ServiceManagement\Filament\Resources\ChangeRequestStatuses\Pages\CreateChangeRequestStatus;
use AidingApp\ServiceManagement\Models\ChangeRequestStatus;
use AidingApp\ServiceManagement\Tests\Tenant\Filament\Resources\ChangeRequestStatuses\RequestFactories\CreateChangeRequestStatusRequestFactory;
use App\Features\ChangeRequestTypeAndStatusNameUniquenessFeature;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;
use function Tests\asSuperAdmin;

it('can create a change request status', function (): void {
    asSuperAdmin();

    $request = CreateChangeRequestStatusRequestFactory::new()->create();

    Livewire::test(CreateChangeRequestStatus::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(ChangeRequestStatus::class, ['name' => $request['name']]);
});

it('can create a change request status with the name of a soft deleted change request status', function (): void {
    asSuperAdmin();

    ChangeRequestStatus::factory()->create(['name' => 'Under Review'])->delete();

    $request = CreateChangeRequestStatusRequestFactory::new()->state(['name' => 'Under Review'])->create();

    Livewire::test(CreateChangeRequestStatus::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    expect(ChangeRequestStatus::query()->where('name', 'Under Review')->count())->toBe(1);
});

it('validates the inputs', function (CreateChangeRequestStatusRequestFactory $data, array $errors): void {
    asSuperAdmin();

    ChangeRequestStatus::factory()->create(['name' => 'Under Review']);

    Livewire::test(CreateChangeRequestStatus::class)
        ->fillForm($data->create())
        ->call('create')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [CreateChangeRequestStatusRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
    'name max' => [CreateChangeRequestStatusRequestFactory::new()->state(['name' => str()->random(256)]), ['name' => 'max']],
    'name unique' => [CreateChangeRequestStatusRequestFactory::new()->state(['name' => 'Under Review']), ['name' => 'unique']],
    'name unique case insensitive' => [CreateChangeRequestStatusRequestFactory::new()->state(['name' => 'UNDER REVIEW']), ['name' => 'unique']],
]);

// TODO: Cleanup Task (change-request-type-and-status-name-uniqueness): delete this describe and the tests within
describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        ChangeRequestTypeAndStatusNameUniquenessFeature::deactivate();

        asSuperAdmin();

        ChangeRequestStatus::factory()->create(['name' => 'Under Review']);

        $request = CreateChangeRequestStatusRequestFactory::new()->state(['name' => 'Under Review'])->create();

        expect(fn () => Livewire::test(CreateChangeRequestStatus::class)
            ->fillForm($request)
            ->call('create'))
            ->toThrow(UniqueConstraintViolationException::class);
    });
});
