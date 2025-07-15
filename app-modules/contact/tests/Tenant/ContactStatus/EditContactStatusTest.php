<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\Contact\Filament\Resources\ContactStatusResource;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactStatus;
use AidingApp\Contact\Tests\Tenant\ContactStatus\RequestFactories\EditContactStatusRequestFactory;
use App\Models\User;
use Illuminate\Validation\Rules\Enum;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;
use function Tests\asSuperAdmin;

test('A successful action on the EditContactStatus page', function () {
    $contactStatus = ContactStatus::factory()->create();

    asSuperAdmin()
        ->get(
            ContactStatusResource::getUrl('edit', [
                'record' => $contactStatus->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditContactStatusRequestFactory::new()->create();

    livewire(ContactStatusResource\Pages\EditContactStatus::class, [
        'record' => $contactStatus->getRouteKey(),
    ])
        ->assertFormSet([
            'classification' => $contactStatus->classification->value,
            'name' => $contactStatus->name,
            'color' => $contactStatus->color->value,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $contactStatus->fresh()->name);
    assertEquals($editRequest['classification'], $contactStatus->fresh()->classification);
    assertEquals($editRequest['color'], $contactStatus->fresh()->color);
});

test('EditContactStatus requires valid data', function (EditContactStatusRequestFactory $data, array $errors) {
    asSuperAdmin();

    $contactStatus = ContactStatus::factory()->create();

    livewire(ContactStatusResource\Pages\EditContactStatus::class, [
        'record' => $contactStatus->getRouteKey(),
    ])
        ->assertFormSet([
            'classification' => $contactStatus->classification->value,
            'name' => $contactStatus->name,
            'color' => $contactStatus->color->value,
        ])
        ->fillForm(EditContactStatusRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ContactStatus::class, $contactStatus->toArray());
})->with(
    [
        'name missing' => [fn () => EditContactStatusRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [fn () => EditContactStatusRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [fn () => EditContactStatusRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [fn () => EditContactStatusRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

// Permission Tests

test('EditContactStatus is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    $contactStatus = ContactStatus::factory()->create();

    actingAs($user)
        ->get(
            ContactStatusResource::getUrl('edit', [
                'record' => $contactStatus,
            ])
        )->assertForbidden();

    livewire(ContactStatusResource\Pages\EditContactStatus::class, [
        'record' => $contactStatus->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    actingAs($user)
        ->get(
            ContactStatusResource::getUrl('edit', [
                'record' => $contactStatus,
            ])
        )->assertSuccessful();

    $request = collect(EditContactStatusRequestFactory::new()->create());

    livewire(ContactStatusResource\Pages\EditContactStatus::class, [
        'record' => $contactStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $contactStatus->fresh()->name);
    assertEquals($request['color'], $contactStatus->fresh()->color);
});
