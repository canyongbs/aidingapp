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
use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\EditContact;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Tests\Tenant\Contact\RequestFactories\EditContactRequestFactory;
use App\Models\User;
use Filament\Forms\Components\TextInput;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

// TODO: Write EditContact page tests
//test('A successful action on the EditContact page', function () {});
//
//test('EditContact requires valid data', function ($data, $errors) {})->with([]);

describe('email health', function () {
    it('shows the bounced hint icon for a bounced contact and does not render the email health callout', function () {
        asSuperAdmin();

        $contact = Contact::factory()->bounced()->create();

        $component = livewire(EditContact::class, ['record' => $contact->getRouteKey()])
            ->assertSuccessful()
            ->assertSchemaComponentDoesNotExist('email-health-callout');

        $emailField = $component->instance()
            ->getSchema('form')
            ->getComponent(fn ($component): bool => $component instanceof TextInput && $component->getName() === 'email');

        expect($emailField)->not->toBeNull()
            ->and($emailField->getHintIcon())->toBe('heroicon-m-exclamation-triangle')
            ->and($emailField->getHintColor())->toBe('warning')
            ->and($emailField->getHintIconTooltip())->toBe('Bounced. Email delivery failed and a bounce was received from our email provider.');
    });

    it('shows the healthy hint icon for a healthy contact', function () {
        asSuperAdmin();

        $contact = Contact::factory()->create(['email_bounce' => false]);

        $component = livewire(EditContact::class, ['record' => $contact->getRouteKey()])
            ->assertSuccessful();

        $emailField = $component->instance()
            ->getSchema('form')
            ->getComponent(fn ($component): bool => $component instanceof TextInput && $component->getName() === 'email');

        expect($emailField)->not->toBeNull()
            ->and($emailField->getHintIcon())->toBe('heroicon-m-check-circle')
            ->and($emailField->getHintColor())->toBe('success')
            ->and($emailField->getHintIconTooltip())->toBe('Healthy. No delivery issues detected.');
    });
});

// Permission Tests

test('EditContact is gated with proper access control', function () {
    $user = User::factory()->create();

    $contact = Contact::factory()->create();

    actingAs($user)
        ->get(
            ContactResource::getUrl('edit', [
                'record' => $contact,
            ])
        )->assertForbidden();

    livewire(EditContact::class, [
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

    livewire(EditContact::class, [
        'record' => $contact->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($contact->fresh()->type_id)->toEqual($request->get('type_id'))
        ->and($contact->fresh()->first_name)->toEqual($request->get('first_name'))
        ->and($contact->fresh()->last_name)->toEqual($request->get('last_name'))
        ->and($contact->fresh()->full_name)->toEqual($request->get('full_name'))
        ->and($contact->fresh()->preferred)->toEqual($request->get('preferred'))
        ->and($contact->fresh()->description)->toEqual($request->get('description'))
        ->and($contact->fresh()->email)->toEqual($request->get('email'))
        ->and($contact->fresh()->mobile)->toEqual($request->get('mobile'))
        ->and($contact->fresh()->email_bounce)->toEqual($request->get('email_bounce'))
        ->and($contact->fresh()->phone)->toEqual($request->get('phone'))
        ->and($contact->fresh()->job_title)->toEqual($request->get('job_title'))
        ->and($contact->fresh()->employee_id)->toEqual($request->get('employee_id'))
        ->and($contact->fresh()->work_number)->toEqual($request->get('work_number'))
        ->and($contact->fresh()->work_extension)->toEqual($request->get('work_extension'))
        ->and($contact->fresh()->student_id)->toEqual($request->get('student_id'))
        ->and($contact->fresh()->school)->toEqual($request->get('school'))
        ->and($contact->fresh()->academic_department)->toEqual($request->get('academic_department'))
        ->and($contact->fresh()->program)->toEqual($request->get('program'))
        ->and($contact->fresh()->address)->toEqual($request->get('address'))
        ->and($contact->fresh()->address_2)->toEqual($request->get('address_2'))
        ->and($contact->fresh()->city)->toEqual($request->get('city'))
        ->and($contact->fresh()->state)->toEqual($request->get('state'))
        ->and($contact->fresh()->postal)->toEqual($request->get('postal'))
        ->and($contact->fresh()->country)->toEqual($request->get('country'));
});

test('EditContact keeps its own email but rejects another contact\'s email case-insensitively', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('contact.view-any');
    $user->givePermissionTo('contact.*.update');

    Contact::factory()->create(['email' => 'other@example.com']);
    $contact = Contact::factory()->create(['email' => 'mine@example.com']);

    actingAs($user);

    livewire(EditContact::class, [
        'record' => $contact->getRouteKey(),
    ])
        ->fillForm(['email' => 'Mine@Example.com'])
        ->call('save')
        ->assertHasNoFormErrors();

    livewire(EditContact::class, [
        'record' => $contact->getRouteKey(),
    ])
        ->fillForm(['email' => 'Other@Example.com'])
        ->call('save')
        ->assertHasFormErrors(['email']);
});
