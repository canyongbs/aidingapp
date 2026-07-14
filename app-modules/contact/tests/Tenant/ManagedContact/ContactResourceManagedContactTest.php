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
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ListContacts;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ViewContact;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Services\ManagedContactService;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

function makeManagedContact(): Contact
{
    $type = ContactType::factory()->create();
    $managedUser = User::factory()->create();

    return app(ManagedContactService::class)->enable($managedUser, $type->getKey());
}

it('hides the edit action for a managed contact in the list', function () {
    $user = User::factory()->create()
        ->givePermissionTo('contact.view-any', 'contact.*.view', 'contact.*.update');

    actingAs($user);

    $managed = makeManagedContact();
    $unmanaged = Contact::factory()->create();

    livewire(ListContacts::class)
        ->assertActionHidden(TestAction::make('edit')->table($managed))
        ->assertActionVisible(TestAction::make('edit')->table($unmanaged));
});

it('shows a lock action instead of edit on the view page of a managed contact', function () {
    $user = User::factory()->create()
        ->givePermissionTo('contact.view-any', 'contact.*.view', 'contact.*.update');

    actingAs($user);

    $managed = makeManagedContact();

    livewire(ViewContact::class, ['record' => $managed->getKey()])
        ->assertActionVisible('managed')
        ->assertActionDoesNotExist('edit');
});

it('shows the edit action on the view page of an unmanaged contact', function () {
    $user = User::factory()->create()
        ->givePermissionTo('contact.view-any', 'contact.*.view', 'contact.*.update');

    actingAs($user);

    $unmanaged = Contact::factory()->create();

    livewire(ViewContact::class, ['record' => $unmanaged->getKey()])
        ->assertActionVisible('edit')
        ->assertActionDoesNotExist('managed');
});

it('forbids access to and hides the edit page of a managed contact', function () {
    $user = User::factory()->create()
        ->givePermissionTo('contact.view-any', 'contact.*.view', 'contact.*.update');

    actingAs($user);

    $managed = makeManagedContact();
    $unmanaged = Contact::factory()->create();

    expect(EditContact::canAccess(['record' => $managed]))->toBeFalse()
        ->and(EditContact::canAccess(['record' => $unmanaged]))->toBeTrue();

    get(ContactResource::getUrl('edit', ['record' => $managed]))
        ->assertForbidden();

    get(ContactResource::getUrl('edit', ['record' => $unmanaged]))
        ->assertSuccessful();
});

it('denies the update ability for a managed contact via the policy', function () {
    $user = User::factory()->create()
        ->givePermissionTo('contact.view-any', 'contact.*.view', 'contact.*.update');

    $managed = makeManagedContact();
    $unmanaged = Contact::factory()->create();

    expect($user->can('update', $managed))->toBeFalse()
        ->and($user->can('update', $unmanaged))->toBeTrue();
});

it('excludes managed contacts from the bulk update action via individual record authorization', function () {
    $user = User::factory()->create()
        ->givePermissionTo('contact.view-any', 'contact.*.view', 'contact.*.update');

    actingAs($user);

    $managed = makeManagedContact();
    $unmanaged = Contact::factory()->create();

    livewire(ListContacts::class)
        ->callTableBulkAction('bulk_update', [$managed, $unmanaged], [
            'field' => 'description',
            'description' => 'bulk-updated-description',
        ])
        ->assertHasNoTableBulkActionErrors();

    expect($unmanaged->refresh()->description)->toBe('bulk-updated-description')
        ->and($managed->refresh()->description)->not->toBe('bulk-updated-description');
});

it('prevents super admins from bulk updating managed contacts', function () {
    asSuperAdmin();

    $managed = makeManagedContact();
    $unmanaged = Contact::factory()->create();

    livewire(ListContacts::class)
        ->callTableBulkAction('bulk_update', [$managed, $unmanaged], [
            'field' => 'description',
            'description' => 'super-admin-bulk-description',
        ]);

    expect($unmanaged->refresh()->description)->toBe('super-admin-bulk-description')
        ->and($managed->refresh()->description)->not->toBe('super-admin-bulk-description');
});
