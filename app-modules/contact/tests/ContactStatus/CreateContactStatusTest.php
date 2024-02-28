<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/aidingapp/blob/main/LICENSE.

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
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Illuminate\Validation\Rules\Enum;
use AdvisingApp\Contact\Models\Contact;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Pest\Laravel\assertDatabaseHas;

use AdvisingApp\Contact\Models\ContactStatus;
use AdvisingApp\Contact\Filament\Resources\ContactStatusResource;
use AdvisingApp\Contact\Tests\ContactStatus\RequestFactories\CreateContactStatusRequestFactory;

test('A successful action on the CreateContactStatus page', function () {
    asSuperAdmin()
        ->get(
            ContactStatusResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = CreateContactStatusRequestFactory::new()->create();

    livewire(ContactStatusResource\Pages\CreateContactStatus::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ContactStatus::all());

    assertDatabaseHas(ContactStatus::class, $request);
});

test('CreateContactStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(ContactStatusResource\Pages\CreateContactStatus::class)
        ->fillForm(CreateContactStatusRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(ContactStatus::all());
})->with(
    [
        'name missing' => [CreateContactStatusRequestFactory::new()->without('name'), ['name' => 'required']],
        'name not a string' => [CreateContactStatusRequestFactory::new()->state(['name' => 1]), ['name' => 'string']],
        'color missing' => [CreateContactStatusRequestFactory::new()->state(['color' => null]), ['color' => 'required']],
        'color not within enum' => [CreateContactStatusRequestFactory::new()->state(['color' => 'not-a-color']), ['color' => Enum::class]],
    ]
);

// Permission Tests

test('CreateContactStatus is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    actingAs($user)
        ->get(
            ContactStatusResource::getUrl('create')
        )->assertForbidden();

    livewire(ContactStatusResource\Pages\CreateContactStatus::class)
        ->assertForbidden();

    $user->givePermissionTo('contact_status.view-any');
    $user->givePermissionTo('contact_status.create');

    actingAs($user)
        ->get(
            ContactStatusResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateContactStatusRequestFactory::new()->create());

    livewire(ContactStatusResource\Pages\CreateContactStatus::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ContactStatus::all());

    assertDatabaseHas(ContactStatus::class, $request->toArray());
});
