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

use AidingApp\ServiceManagement\Filament\Resources\SLAs\Pages\EditSla;
use AidingApp\ServiceManagement\Models\Sla;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\SlaRequestFactory;
use App\Features\SlaNameUniquenessFeature;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Livewire;

use function Tests\asSuperAdmin;

it('can update an SLA', function (): void {
    asSuperAdmin();

    $sla = Sla::query()->create(['name' => 'Gold Support']);

    $request = SlaRequestFactory::new()->create();

    Livewire::test(EditSla::class, [
        'record' => $sla->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors();

    expect($sla->refresh()->name)->toBe($request['name']);
});

it('allows saving with the same name as the current record', function (): void {
    asSuperAdmin();

    $sla = Sla::query()->create(['name' => 'Gold Support']);

    Livewire::test(EditSla::class, [
        'record' => $sla->getRouteKey(),
    ])
        ->fillForm(['name' => 'GOLD SUPPORT'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($sla->refresh()->name)->toBe('GOLD SUPPORT');
});

it('validates the inputs', function (SlaRequestFactory $data, array $errors): void {
    asSuperAdmin();

    Sla::query()->create(['name' => 'Gold Support']);

    $sla = Sla::query()->create(['name' => 'Silver Support']);

    $request = SlaRequestFactory::new($data)->create();

    Livewire::test(EditSla::class, [
        'record' => $sla->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [
        SlaRequestFactory::new()->state(['name' => null]),
        ['name' => 'required'],
    ],
    'name max' => [
        SlaRequestFactory::new()->state(['name' => str()->random(256)]),
        ['name' => 'max'],
    ],
    'name unique' => [
        SlaRequestFactory::new()->state(['name' => 'Gold Support']),
        ['name' => 'unique'],
    ],
    'name unique case-insensitively' => [
        SlaRequestFactory::new()->state(['name' => 'gold support']),
        ['name' => 'unique'],
    ],
]);

it('allows renaming to the name of a soft-deleted SLA', function (): void {
    asSuperAdmin();

    Sla::query()->create(['name' => 'Gold Support'])->delete();

    $sla = Sla::query()->create(['name' => 'Silver Support']);

    Livewire::test(EditSla::class, [
        'record' => $sla->getRouteKey(),
    ])
        ->fillForm(['name' => 'Gold Support'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($sla->refresh()->name)->toBe('Gold Support');
});

it('does not apply the unique form rule when the feature is disabled', function (): void {
    SlaNameUniquenessFeature::deactivate();

    asSuperAdmin();

    Sla::query()->create(['name' => 'Gold Support']);

    $sla = Sla::query()->create(['name' => 'Silver Support']);

    expect(fn () => Livewire::test(EditSla::class, [
        'record' => $sla->getRouteKey(),
    ])
        ->fillForm(['name' => 'Gold Support'])
        ->call('save'))
        ->toThrow(UniqueConstraintViolationException::class);
});
