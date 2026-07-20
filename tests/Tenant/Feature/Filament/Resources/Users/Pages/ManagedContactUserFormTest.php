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

use AidingApp\Contact\Models\ContactType;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Models\User;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('creates a linked managed contact when the toggle is enabled', function () {
    asSuperAdmin();

    $type = ContactType::factory()->create();

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Manny Ged',
            'email' => 'manny@example.com',
            'is_managed_contact' => true,
            'managed_contact_type_id' => $type->getKey(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $user = User::query()->where('email', 'manny@example.com')->first();

    expect($user->managedContact()->exists())->toBeTrue();

    $contact = $user->managedContact;

    expect($contact->full_name)->toBe('Manny Ged')
        ->and($contact->email)->toBe('manny@example.com')
        ->and($contact->type_id)->toBe($type->getKey());
});

it('does not create a managed contact when the toggle is disabled', function () {
    asSuperAdmin();

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Plain User',
            'email' => 'plain@example.com',
            'is_managed_contact' => false,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $user = User::query()->where('email', 'plain@example.com')->first();

    expect($user->managedContact()->exists())->toBeFalse();
});

it('requires a contact type when managed contact is enabled', function () {
    asSuperAdmin();

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'No Type',
            'email' => 'notype@example.com',
            'is_managed_contact' => true,
            'managed_contact_type_id' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['managed_contact_type_id' => 'required']);
});

it('enables and then disables a managed contact from the edit page', function () {
    asSuperAdmin();

    $type = ContactType::factory()->create();

    $user = User::factory()->create();

    livewire(EditUser::class, ['record' => $user->getKey()])
        ->fillForm([
            'is_managed_contact' => true,
            'managed_contact_type_id' => $type->getKey(),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->managedContact()->exists())->toBeTrue();

    livewire(EditUser::class, ['record' => $user->getKey()])
        ->fillForm([
            'is_managed_contact' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->managedContact()->exists())->toBeFalse();
});

it('hydrates the managed contact toggle on the edit page', function () {
    asSuperAdmin();

    $type = ContactType::factory()->create();

    $user = User::factory()->create();

    livewire(EditUser::class, ['record' => $user->getKey()])
        ->fillForm([
            'is_managed_contact' => true,
            'managed_contact_type_id' => $type->getKey(),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    livewire(EditUser::class, ['record' => $user->getKey()])
        ->assertFormSet([
            'is_managed_contact' => true,
            'managed_contact_type_id' => $type->getKey(),
        ]);
});
