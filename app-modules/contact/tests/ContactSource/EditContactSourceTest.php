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

use AidingApp\Contact\Filament\Resources\ContactSourceResource;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactSource;
use AidingApp\Contact\Tests\ContactSource\RequestFactories\EditContactSourceRequestFactory;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertEquals;
use function Tests\asSuperAdmin;

test('A successful action on the EditContactSource page', function () {
    $contactSource = ContactSource::factory()->create();

    asSuperAdmin()
        ->get(
            ContactSourceResource::getUrl('edit', [
                'record' => $contactSource->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $editRequest = EditContactSourceRequestFactory::new()->create();

    livewire(ContactSourceResource\Pages\EditContactSource::class, [
        'record' => $contactSource->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $contactSource->name,
        ])
        ->fillForm($editRequest)
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($editRequest['name'], $contactSource->fresh()->name);
});

test('EditContactSource requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $contactSource = ContactSource::factory()->create();

    livewire(ContactSourceResource\Pages\EditContactSource::class, [
        'record' => $contactSource->getRouteKey(),
    ])
        ->assertFormSet([
            'name' => $contactSource->name,
        ])
        ->fillForm(EditContactSourceRequestFactory::new($data)->create())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ContactSource::class, $contactSource->toArray());
})->with(
    [
        'name missing' => [EditContactSourceRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
        'name not a string' => [EditContactSourceRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
    ]
);

// Permission Tests

test('EditContactSource is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    $contactSource = ContactSource::factory()->create();

    actingAs($user)
        ->get(
            ContactSourceResource::getUrl('edit', [
                'record' => $contactSource,
            ])
        )->assertForbidden();

    livewire(ContactSourceResource\Pages\EditContactSource::class, [
        'record' => $contactSource->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    actingAs($user)
        ->get(
            ContactSourceResource::getUrl('edit', [
                'record' => $contactSource,
            ])
        )->assertSuccessful();

    $request = collect(EditContactSourceRequestFactory::new()->create());

    livewire(ContactSourceResource\Pages\EditContactSource::class, [
        'record' => $contactSource->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertEquals($request['name'], $contactSource->fresh()->name);
});
