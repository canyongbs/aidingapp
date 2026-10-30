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

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Models\Organization;
use AidingApp\ServiceManagement\Filament\Tables\CreateServiceRequestContactsTable;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use Filament\Forms\Components\TableSelect\Livewire\TableSelectLivewireComponent;
use Filament\Tables\Columns\TextColumn;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Tests\asSuperAdmin;

if (! function_exists('liveCreateServiceRequestContactsTable')) {
    function liveCreateServiceRequestContactsTable(): Testable
    {
        return Livewire::test(TableSelectLivewireComponent::class, [
            'tableConfiguration' => base64_encode(CreateServiceRequestContactsTable::class),
        ]);
    }
}
it('displays the open and total service request counts', function (): void {
    asSuperAdmin();

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

    $component = liveCreateServiceRequestContactsTable()
        ->assertCanSeeTableRecords([$contact])
        ->assertTableColumnStateSet('open_service_requests_count', 2, $contact)
        ->assertTableColumnFormattedStateSet('open_service_requests_count', '2 Open', $contact)
        ->assertTableColumnHasDescription('open_service_requests_count', '3 Total', $contact)
        ->assertSeeText('2 Open')
        ->assertSeeText('3 Total');

    $column = $component->instance()->getTable()->getColumn('open_service_requests_count');
    assert($column instanceof TextColumn);

    expect($column->getColor(2))->toBe('warning');
});

it('displays 0 open and 0 total when the contact has no service requests', function (): void {
    asSuperAdmin();

    $contact = Contact::factory()->create();

    $component = liveCreateServiceRequestContactsTable()
        ->assertCanSeeTableRecords([$contact])
        ->assertTableColumnStateSet('open_service_requests_count', 0, $contact)
        ->assertTableColumnFormattedStateSet('open_service_requests_count', '0 Open', $contact)
        ->assertTableColumnHasDescription('open_service_requests_count', '0 Total', $contact)
        ->assertSeeText('0 Open')
        ->assertSeeText('0 Total');

    $column = $component->instance()->getTable()->getColumn('open_service_requests_count');
    assert($column instanceof TextColumn);

    expect($column->getColor(0))->toBe('gray');
});

it('shows the organization as the classification description when present', function (): void {
    asSuperAdmin();

    $organization = Organization::factory()->create(['name' => 'Acme University']);

    $contact = Contact::factory()->for($organization, 'organization')->create();

    $component = liveCreateServiceRequestContactsTable()
        ->assertCanSeeTableRecords([$contact])
        ->assertTableColumnHasDescription('type.name', 'Acme University', $contact)
        ->assertSeeText('Acme University');

    $column = $component->instance()->getTable()->getColumn('type.name');
    assert($column instanceof TextColumn);
    $column->record($contact);

    expect($column->getColor($contact->type->name))->toBe($contact->type->color->value);
});

it('does not show an organization description when none is associated', function (): void {
    asSuperAdmin();

    $contact = Contact::factory()->create(['organization_id' => null]);

    liveCreateServiceRequestContactsTable()
        ->assertCanSeeTableRecords([$contact])
        ->assertTableColumnHasDescription('type.name', null, $contact);
});

describe('search', function (): void {
    it('searches by contact name and email', function (string $search): void {
        asSuperAdmin();

        $matchingContact = Contact::factory()->create([
            'first_name' => 'Searchable',
            'last_name' => 'Contact',
            'full_name' => 'Searchable Contact',
            'email' => 'searchable-contact@example.com',
        ]);
        $otherContact = Contact::factory()->create([
            'full_name' => 'Hidden Person',
            'email' => 'hidden-person@example.com',
        ]);

        liveCreateServiceRequestContactsTable()
            ->searchTable($search)
            ->assertCanSeeTableRecords([$matchingContact])
            ->assertCanNotSeeTableRecords([$otherContact]);
    })->with(['Searchable Contact', 'searchable-contact@example.com']);

    it('searches by classification type name', function (): void {
        asSuperAdmin();

        $matchingContact = Contact::factory()
            ->for(ContactType::factory()->create(['name' => 'Alumni']), 'type')
            ->create();
        $otherContact = Contact::factory()
            ->for(ContactType::factory()->create(['name' => 'Prospect']), 'type')
            ->create();

        liveCreateServiceRequestContactsTable()
            ->searchTable('Alumni')
            ->assertCanSeeTableRecords([$matchingContact])
            ->assertCanNotSeeTableRecords([$otherContact]);
    });

    it('searches by organization name', function (): void {
        asSuperAdmin();

        $matchingContact = Contact::factory()
            ->for(Organization::factory()->create(['name' => 'Northwind University']), 'organization')
            ->create();
        $otherContact = Contact::factory()
            ->for(Organization::factory()->create(['name' => 'Globex Institute']), 'organization')
            ->create();

        liveCreateServiceRequestContactsTable()
            ->searchTable('Northwind University')
            ->assertCanSeeTableRecords([$matchingContact])
            ->assertCanNotSeeTableRecords([$otherContact]);
    });
});

describe('filters', function (): void {
    it('filters by type', function (): void {
        asSuperAdmin();

        $type = ContactType::factory()->create();

        $matchingContact = Contact::factory()->for($type, 'type')->create();
        $otherContact = Contact::factory()->create();

        liveCreateServiceRequestContactsTable()
            ->assertCanSeeTableRecords([$matchingContact, $otherContact])
            ->filterTable('type_id', [$type->getKey()])
            ->assertCanSeeTableRecords([$matchingContact])
            ->assertCanNotSeeTableRecords([$otherContact]);
    });

    it('filters by organization', function (): void {
        asSuperAdmin();

        $organization = Organization::factory()->create();

        $matchingContact = Contact::factory()->for($organization, 'organization')->create();
        $otherContact = Contact::factory()->create(['organization_id' => null]);

        liveCreateServiceRequestContactsTable()
            ->assertCanSeeTableRecords([$matchingContact, $otherContact])
            ->filterTable('organization_id', $organization->getKey())
            ->assertCanSeeTableRecords([$matchingContact])
            ->assertCanNotSeeTableRecords([$otherContact]);
    });
});
