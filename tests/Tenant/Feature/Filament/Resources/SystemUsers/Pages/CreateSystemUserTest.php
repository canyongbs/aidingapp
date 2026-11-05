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

use App\Features\SystemUserNameUniquenessFeature;
use App\Filament\Resources\SystemUsers\Pages\CreateSystemUser;
use App\Models\SystemUser;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;
use function Tests\asSuperAdmin;

it('can create a system user', function (): void {
    asSuperAdmin();

    Livewire::test(CreateSystemUser::class)
        ->fillForm(['name' => 'Integration Service'])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(SystemUser::class, ['name' => 'Integration Service']);
});

it('does not allow creating a system user whose name matches another system user case-insensitively', function (): void {
    asSuperAdmin();

    SystemUser::factory()->create(['name' => 'Integration Service']);

    Livewire::test(CreateSystemUser::class)
        ->fillForm(['name' => 'integration service'])
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});

it('allows creating a system user whose name is freed up by a soft-deleted system user case-insensitively', function (): void {
    asSuperAdmin();

    SystemUser::factory()->create(['name' => 'Integration Service'])->delete();

    Livewire::test(CreateSystemUser::class)
        ->fillForm(['name' => 'integration service'])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(SystemUser::class, [
        'name' => 'integration service',
        'deleted_at' => null,
    ]);
});

describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        SystemUserNameUniquenessFeature::deactivate();

        asSuperAdmin();

        SystemUser::factory()->create(['name' => 'Integration Service']);

        expect(fn () => Livewire::test(CreateSystemUser::class)
            ->fillForm(['name' => 'Integration Service'])
            ->call('create'))
            ->toThrow(UniqueConstraintViolationException::class);
    });
});
