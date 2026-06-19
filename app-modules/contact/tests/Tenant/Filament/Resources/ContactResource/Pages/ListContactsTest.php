<?php

use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ListContacts;
use AidingApp\Contact\Filament\Resources\OrganizationResource\Pages\ListOrganizations;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

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