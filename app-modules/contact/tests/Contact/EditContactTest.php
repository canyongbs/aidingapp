<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Tests\Contact\RequestFactories\EditContactRequestFactory;

// TODO: Write EditContact page tests
//test('A successful action on the EditContact page', function () {});
//
//test('EditContact requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('EditContact is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    $contact = Contact::factory()->create();

    actingAs($user)
        ->get(
            ContactResource::getUrl('edit', [
                'record' => $contact,
            ])
        )->assertForbidden();

    livewire(ContactResource\Pages\EditContact::class, [
        'record' => $contact->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('contact.view-any');
    $user->givePermissionTo('contact.*.update');

    actingAs($user)
        ->get(
            ContactResource::getUrl('edit', [
                'record' => $contact,
            ])
        )->assertSuccessful();

    // TODO: Finish these tests to ensure changes are allowed
    $request = collect(EditContactRequestFactory::new()->create());

    livewire(ContactResource\Pages\EditContact::class, [
        'record' => $contact->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($contact->fresh()->status_id)->toEqual($request->get('status_id'))
        ->and($contact->fresh()->source_id)->toEqual($request->get('source_id'))
        ->and($contact->fresh()->first_name)->toEqual($request->get('first_name'))
        ->and($contact->fresh()->last_name)->toEqual($request->get('last_name'))
        ->and($contact->fresh()->full_name)->toEqual($request->get('full_name'))
        ->and($contact->fresh()->preferred)->toEqual($request->get('preferred'))
        ->and($contact->fresh()->description)->toEqual($request->get('description'))
        ->and($contact->fresh()->email)->toEqual($request->get('email'))
        ->and($contact->fresh()->mobile)->toEqual($request->get('mobile'))
        ->and($contact->fresh()->sms_opt_out)->toEqual($request->get('sms_opt_out'))
        ->and($contact->fresh()->email_bounce)->toEqual($request->get('email_bounce'))
        ->and($contact->fresh()->phone)->toEqual($request->get('phone'))
        ->and($contact->fresh()->address)->toEqual($request->get('address'))
        ->and($contact->fresh()->address_2)->toEqual($request->get('address_2'))
        ->and($contact->fresh()->assigned_to_id)->toEqual($request->get('assigned_to_id'))
        ->and($contact->fresh()->created_by_id)->toEqual($request->get('created_by_id'));
});
