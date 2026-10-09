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
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ViewContact;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Tests\Tenant\Contact\RequestFactories\EditContactRequestFactory;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('the contact section edit actions are gated with proper access control', function () {
    $user = User::factory()->create();

    $contact = Contact::factory()->create();

    actingAs($user);

    $user->givePermissionTo('contact.view-any');
    $user->givePermissionTo('contact.*.view');

    livewire(ViewContact::class, [
        'record' => $contact->getRouteKey(),
    ])
        ->assertActionDoesNotExist(TestAction::make('editDemographicInformation')->schemaComponent('demographicInformation'));

    $user->givePermissionTo('contact.*.update');

    livewire(ViewContact::class, [
        'record' => $contact->getRouteKey(),
    ])
        ->assertActionVisible(TestAction::make('editDemographicInformation')->schemaComponent('demographicInformation'));
});

test('the contact sections can be updated', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('contact.view-any');
    $user->givePermissionTo('contact.*.view');
    $user->givePermissionTo('contact.*.update');

    actingAs($user);

    $contact = Contact::factory()->create();

    $request = collect(EditContactRequestFactory::new()->create());

    livewire(ViewContact::class, [
        'record' => $contact->getRouteKey(),
    ])
        ->callAction(TestAction::make('editDemographicInformation')->schemaComponent('demographicInformation'), data: [
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'preferred' => $request->get('preferred'),
            'type_id' => $request->get('type_id'),
        ])
        ->assertHasNoFormErrors()
        ->callAction(TestAction::make('editContactInformation')->schemaComponent('contactInformation'), data: [
            'email' => $request->get('email'),
            'mobile' => $request->get('mobile'),
            'phone' => $request->get('phone'),
        ])
        ->assertHasNoFormErrors();

    expect($contact->fresh())
        ->type_id->toEqual($request->get('type_id'))
        ->first_name->toEqual($request->get('first_name'))
        ->last_name->toEqual($request->get('last_name'))
        ->full_name->toEqual("{$request->get('first_name')} {$request->get('last_name')}")
        ->preferred->toEqual($request->get('preferred'))
        ->email->toEqual($request->get('email'));
});

test('EditContact keeps its own email but rejects another contact\'s email case-insensitively', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('contact.view-any');
    $user->givePermissionTo('contact.*.view');
    $user->givePermissionTo('contact.*.update');

    Contact::factory()->create(['email' => 'other@example.com']);
    $contact = Contact::factory()->create(['email' => 'mine@example.com']);

    actingAs($user);

    livewire(ViewContact::class, [
        'record' => $contact->getRouteKey(),
    ])
        ->callAction(TestAction::make('editContactInformation')->schemaComponent('contactInformation'), data: [
            'email' => 'Mine@Example.com',
        ])
        ->assertHasNoFormErrors();

    livewire(ViewContact::class, [
        'record' => $contact->getRouteKey(),
    ])
        ->callAction(TestAction::make('editContactInformation')->schemaComponent('contactInformation'), data: [
            'email' => 'Other@Example.com',
        ])
        ->assertHasFormErrors(['email']);
});
