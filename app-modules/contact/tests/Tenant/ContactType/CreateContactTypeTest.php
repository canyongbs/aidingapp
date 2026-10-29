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
use AidingApp\Contact\Filament\Resources\ContactTypeResource\Pages\CreateContactType;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Tests\Tenant\ContactType\RequestFactories\CreateContactTypeRequestFactory;
use App\Features\ContactTypeNameUniquenessFeature;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\Rules\Enum;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Tests\asSuperAdmin;

test('A successful action on the CreateContactType page', function (): void {
    asSuperAdmin()
        ->get(
            ContactTypeResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = CreateContactTypeRequestFactory::new()->create();

    Livewire::test(CreateContactType::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ContactType::all());

    assertDatabaseHas(ContactType::class, $request);
});

test('CreateContactType requires valid data', function (CreateContactTypeRequestFactory $data, array $errors): void {
    asSuperAdmin();

    Livewire::test(CreateContactType::class)
        ->fillForm(CreateContactTypeRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(ContactType::all());
})->with(
    [
        'name missing' => [fn (): CreateContactTypeRequestFactory => CreateContactTypeRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [fn (): CreateContactTypeRequestFactory => CreateContactTypeRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [fn (): CreateContactTypeRequestFactory => CreateContactTypeRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [fn (): CreateContactTypeRequestFactory => CreateContactTypeRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

test('CreateContactType rejects a name that matches an existing contact type case-insensitively', function (string $name): void {
    asSuperAdmin();

    ContactType::factory()->create(['name' => 'Student']);

    Livewire::test(CreateContactType::class)
        ->fillForm(CreateContactTypeRequestFactory::new()->state(['name' => $name])->create())
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);

    assertCount(1, ContactType::all());
})->with([
    'same case' => 'Student',
    'different case' => 'STUDENT',
]);

test('CreateContactType allows reusing the name of a soft-deleted contact type', function (): void {
    asSuperAdmin();

    ContactType::factory()->create(['name' => 'Student'])->delete();

    Livewire::test(CreateContactType::class)
        ->fillForm(CreateContactTypeRequestFactory::new()->state(['name' => 'student'])->create())
        ->call('create')
        ->assertHasNoFormErrors();

    expect(ContactType::query()->where('name', 'student')->count())->toBe(1);
});

test('CreateContactType does not apply the unique form rule when the feature is disabled', function (): void {
    ContactTypeNameUniquenessFeature::deactivate();

    asSuperAdmin();

    ContactType::factory()->create(['name' => 'Student']);

    expect(fn () => Livewire::test(CreateContactType::class)
        ->fillForm(CreateContactTypeRequestFactory::new()->state(['name' => 'Student'])->create())
        ->call('create'))
        ->toThrow(UniqueConstraintViolationException::class);
});

// Permission Tests

test('CreateContactType is gated with proper access control', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ContactTypeResource::getUrl('create')
        )->assertForbidden();

    Livewire::test(CreateContactType::class)
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.create');

    actingAs($user)
        ->get(
            ContactTypeResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateContactTypeRequestFactory::new()->create());

    Livewire::test(CreateContactType::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ContactType::all());

    assertDatabaseHas(ContactType::class, $request->toArray());
});
