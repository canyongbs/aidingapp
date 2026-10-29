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

use AidingApp\Contact\Filament\Resources\ContactTypeResource;
use AidingApp\Contact\Filament\Resources\ContactTypeResource\Pages\EditContactType;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Tests\Tenant\ContactType\RequestFactories\EditContactTypeRequestFactory;
use App\Features\ContactTypeNameUniquenessFeature;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\Rules\Enum;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;
use function Tests\asSuperAdmin;

test('A successful action on the EditContactType page', function (): void {
    $contactType = ContactType::factory()->create();

    asSuperAdmin()
        ->get(
            ContactTypeResource::getUrl('edit', [
                'record' => $contactType->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditContactTypeRequestFactory::new()->create();

    Livewire::test(EditContactType::class, [
        'record' => $contactType->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $contactType->name,
            'color' => $contactType->color,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $contactType->fresh()->name);
    assertEquals($editRequest['color'], $contactType->fresh()->color);
});

test('EditContactType requires valid data', function (EditContactTypeRequestFactory $data, array $errors): void {
    asSuperAdmin();

    $contactType = ContactType::factory()->create();

    Livewire::test(EditContactType::class, [
        'record' => $contactType->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $contactType->name,
            'color' => $contactType->color,
        ])
        ->fillForm(EditContactTypeRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ContactType::class, $contactType->toArray());
})->with(
    [
        'name missing' => [fn (): EditContactTypeRequestFactory => EditContactTypeRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [fn (): EditContactTypeRequestFactory => EditContactTypeRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [fn (): EditContactTypeRequestFactory => EditContactTypeRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [fn (): EditContactTypeRequestFactory => EditContactTypeRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

test('EditContactType rejects a name that matches another contact type case-insensitively', function (string $name): void {
    asSuperAdmin();

    ContactType::factory()->create(['name' => 'Student']);
    $contactType = ContactType::factory()->create(['name' => 'Employee']);

    Livewire::test(EditContactType::class, [
        'record' => $contactType->getRouteKey(),
    ])
        ->fillForm(EditContactTypeRequestFactory::new()->state(['name' => $name])->create())
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);

    expect($contactType->fresh()->name)->toBe('Employee');
})->with([
    'same case' => 'Student',
    'different case' => 'STUDENT',
]);

test('EditContactType allows saving a contact type with its own name in a different case', function (): void {
    asSuperAdmin();

    $contactType = ContactType::factory()->create(['name' => 'Student']);

    Livewire::test(EditContactType::class, [
        'record' => $contactType->getRouteKey(),
    ])
        ->fillForm(EditContactTypeRequestFactory::new()->state(['name' => 'STUDENT'])->create())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($contactType->fresh()->name)->toBe('STUDENT');
});

test('EditContactType allows using the name of a soft-deleted contact type', function (): void {
    asSuperAdmin();

    ContactType::factory()->create(['name' => 'Student'])->delete();
    $contactType = ContactType::factory()->create(['name' => 'Employee']);

    Livewire::test(EditContactType::class, [
        'record' => $contactType->getRouteKey(),
    ])
        ->fillForm(EditContactTypeRequestFactory::new()->state(['name' => 'student'])->create())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($contactType->fresh()->name)->toBe('student');
});

test('EditContactType does not apply the unique form rule when the feature is disabled', function (): void {
    ContactTypeNameUniquenessFeature::deactivate();

    asSuperAdmin();

    ContactType::factory()->create(['name' => 'Student']);
    $contactType = ContactType::factory()->create(['name' => 'Employee']);

    expect(fn () => Livewire::test(EditContactType::class, [
        'record' => $contactType->getRouteKey(),
    ])
        ->fillForm(EditContactTypeRequestFactory::new()->state(['name' => 'Student'])->create())
        ->call('save'))
        ->toThrow(UniqueConstraintViolationException::class);
});

// Permission Tests

test('EditContactType is gated with proper access control', function (): void {
    $user = User::factory()->create();

    $contactType = ContactType::factory()->create();

    actingAs($user)
        ->get(
            ContactTypeResource::getUrl('edit', [
                'record' => $contactType,
            ])
        )->assertForbidden();

    Livewire::test(EditContactType::class, [
        'record' => $contactType->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    actingAs($user)
        ->get(
            ContactTypeResource::getUrl('edit', [
                'record' => $contactType,
            ])
        )->assertSuccessful();

    $request = collect(EditContactTypeRequestFactory::new()->create());

    Livewire::test(EditContactType::class, [
        'record' => $contactType->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $contactType->fresh()->name);
    assertEquals($request['color'], $contactType->fresh()->color);
});
