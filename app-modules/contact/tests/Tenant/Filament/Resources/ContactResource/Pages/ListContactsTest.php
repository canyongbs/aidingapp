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
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ListContacts;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('displays the open and total service request counts', function () {
    actingAs(
        User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view')
    );

    $contact = Contact::factory()->create();

    $openStatus = ServiceRequestStatus::factory()->open()->create();
    $closedStatus = ServiceRequestStatus::factory()->closed()->create();

    ServiceRequest::factory()->count(2)->create([
        'respondent_id' => $contact->getKey(),
        'status_id' => $openStatus->getKey(),
    ]);

    ServiceRequest::factory()->create([
        'respondent_id' => $contact->getKey(),
        'status_id' => $closedStatus->getKey(),
    ]);

    livewire(ListContacts::class)
        ->assertSeeText('2 Open')
        ->assertSeeText('3 Total');
});

it('displays 0 open and 0 total when the contact has no service requests', function () {
    actingAs(
        User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view')
    );

    Contact::factory()->create();

    livewire(ListContacts::class)
        ->assertSeeText('0 Open')
        ->assertSeeText('0 Total');
});

it('only displays populated identifiers', function () {
    actingAs(
        User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view')
    );

    Contact::factory()->create([
        'employee_id' => 'EMP-777',
        'student_id' => null,
    ]);

    livewire(ListContacts::class)
        ->assertSeeText('Employee ID:')
        ->assertSeeText('EMP-777')
        ->assertDontSeeText('Student ID:');
});

it('only displays the organization when one is associated', function () {
    actingAs(
        User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view')
    );

    $organization = Organization::factory()->create(['name' => 'Acme University']);

    Contact::factory()->for($organization, 'organization')->create();

    livewire(ListContacts::class)
        ->assertSeeText('Acme University');
});

it('links the contact name to the view page', function () {
    actingAs(
        User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view')
    );

    $contact = Contact::factory()->create();

    livewire(ListContacts::class)
        ->assertSee(ContactResource::getUrl('view', ['record' => $contact]));
});

it('does not render standalone view or edit actions', function () {
    actingAs(
        User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view', 'contact.*.update')
    );

    $contact = Contact::factory()->create();

    livewire(ListContacts::class)
        ->assertActionDoesNotExist(TestAction::make('view')->table($contact))
        ->assertActionDoesNotExist(TestAction::make('edit')->table($contact));
});

it('only shows the bulk delete action to a user with the contact.delete permission', function () {
    Contact::factory(15)->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('contact.view-any', 'contact.*.view');

    actingAs($user);

    livewire(ListContacts::class)
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('contact.*.delete');

    livewire(ListContacts::class)
        ->assertActionVisible(TestAction::make('delete')->table()->bulk());
});
