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

use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ListContacts;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Notifications\NotificationMailbox;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    actingAs(User::factory()->create()->givePermissionTo('contacts.view-any', 'contacts.*.view', 'contacts.*.update'));
});

it('changes the type of the selected contacts', function (): void {
    $newType = ContactType::factory()->create();

    $selected = Contact::factory()->count(2)->create();
    $unselected = Contact::factory()->create();
    $unselectedType = $unselected->type_id;

    expect($selected->pluck('type_id')->all())->each->not->toBe($newType->getKey());

    Livewire::test(ListContacts::class)
        ->selectTableRecords($selected->modelKeys())
        ->callAction(TestAction::make('changeType')->table()->bulk(), data: ['type_id' => $newType->getKey()])
        ->assertHasNoFormErrors();

    expect($selected->each->refresh()->pluck('type_id')->unique()->all())->toBe([$newType->getKey()])
        ->and($unselected->refresh()->type_id)->toBe($unselectedType);
});

it('skips managed contacts and reports why', function (): void {
    $newType = ContactType::factory()->create();

    $contact = Contact::factory()->create();
    $managed = Contact::factory()->for(User::factory(), 'managedByUser')->create();
    $managedType = $managed->type_id;

    Livewire::test(ListContacts::class)
        ->selectTableRecords([$contact->getKey(), $managed->getKey()])
        ->callAction(TestAction::make('changeType')->table()->bulk(), data: ['type_id' => $newType->getKey()]);

    app(NotificationMailbox::class)->flush();

    $notifications = new Notifications();
    $notifications->mount();
    $notifications->pullNotificationsFromSession();

    expect($notifications->notifications->map(fn (Notification $notification): string => (string) $notification->getBody())->all())
        ->toContain('<p>One of the selected contacts is managed and cannot be edited.</p>')
        ->and($contact->refresh()->type_id)->toBe($newType->getKey())
        ->and($managed->refresh()->type_id)->toBe($managedType);
});

it('only offers the action to a user with the `contacts.*.update` permission', function (): void {
    Contact::factory()->create();

    actingAs(User::factory()->create()->givePermissionTo('contacts.view-any', 'contacts.*.view'));

    Livewire::test(ListContacts::class)
        ->assertActionHidden(TestAction::make('changeType')->table()->bulk());

    actingAs(User::factory()->create()->givePermissionTo('contacts.view-any', 'contacts.*.view', 'contacts.*.update'));

    Livewire::test(ListContacts::class)
        ->assertActionVisible(TestAction::make('changeType')->table()->bulk());
});

it('requires an existing contact type', function (mixed $type): void {
    $contact = Contact::factory()->create();
    $originalType = $contact->type_id;

    Livewire::test(ListContacts::class)
        ->selectTableRecords([$contact->getKey()])
        ->callAction(TestAction::make('changeType')->table()->bulk(), data: ['type_id' => $type])
        ->assertHasFormErrors(['type_id']);

    expect($contact->refresh()->type_id)->toBe($originalType);
})->with([
    'missing' => [null],
    'unknown' => [fn (): string => (string) fake()->uuid()],
]);

it('offers the contact types in alphabetical order', function (): void {
    ContactType::query()->forceDelete();

    $student = ContactType::factory()->create(['name' => 'Student']);
    ContactType::factory()->create(['name' => 'Alumni']);
    ContactType::factory()->create(['name' => 'Parent']);

    $contact = Contact::factory()->for($student, 'type')->create();

    Livewire::test(ListContacts::class)
        ->selectTableRecords([$contact->getKey()])
        ->mountAction(TestAction::make('changeType')->table()->bulk())
        ->assertSchemaComponentExists('type_id', 'mountedActionSchema0', fn (Select $select): bool => array_values($select->getOptions()) === ['Alumni', 'Parent', 'Student']);
});
