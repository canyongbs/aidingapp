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
use App\Settings\PresenceSettings;
use Filament\Actions\Testing\TestAction;

use function Filament\Support\generate_icon_html;

use Filament\Support\View\ComponentAttributeBag;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\freezeTime;

it('displays the open and total service request counts', function (): void {
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

    $component = Livewire::test(ListContacts::class)
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
    actingAs(
        User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view')
    );

    $contact = Contact::factory()->create();

    $component = Livewire::test(ListContacts::class)
        ->assertTableColumnStateSet('open_service_requests_count', 0, $contact)
        ->assertTableColumnFormattedStateSet('open_service_requests_count', '0 Open', $contact)
        ->assertTableColumnHasDescription('open_service_requests_count', '0 Total', $contact)
        ->assertSeeText('0 Open')
        ->assertSeeText('0 Total');

    $column = $component->instance()->getTable()->getColumn('open_service_requests_count');
    assert($column instanceof TextColumn);

    expect($column->getColor(0))->toBe('gray');
});

it('only displays populated identifiers', function (): void {
    actingAs(
        User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view')
    );

    Contact::factory()->create([
        'employee_id' => 'EMP-777',
        'student_id' => null,
    ]);

    Livewire::test(ListContacts::class)
        ->assertSeeText('Employee ID:')
        ->assertSeeText('EMP-777')
        ->assertDontSeeText('Student ID:');
});

it('only displays the organization when one is associated', function (): void {
    actingAs(
        User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view')
    );

    $organization = Organization::factory()->create(['name' => 'Acme University']);

    $contact = Contact::factory()->for($organization, 'organization')->create();

    $component = Livewire::test(ListContacts::class)
        ->assertTableColumnStateSet('type.name', $contact->type->name, $contact)
        ->assertTableColumnHasDescription('type.name', 'Acme University', $contact)
        ->assertSeeText('Acme University');

    $column = $component->instance()->getTable()->getColumn('type.name');
    assert($column instanceof TextColumn);
    $column->record($contact);

    expect($column->getColor($contact->type->name))->toBe($contact->type->color->value);
});

it('does not show an organization description when none is associated', function (): void {
    actingAs(User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view'));

    $contact = Contact::factory()->create(['organization_id' => null]);

    Livewire::test(ListContacts::class)
        ->assertTableColumnHasDescription('type.name', null, $contact);
});

it('links the contact name to the view page', function (): void {
    actingAs(
        User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view')
    );

    $contact = Contact::factory()->create();

    $component = Livewire::test(ListContacts::class)
        ->assertSee(ContactResource::getUrl('view', ['record' => $contact]));

    expect($component->instance()->getTable()->getRecordUrl($contact))
        ->toBe(ContactResource::getUrl('view', ['record' => $contact]));

    $matched = preg_match('/<td\b[^>]*class="[^"]*\bfi-ta-cell-full-name\b[^"]*"[^>]*>(.*?)<\/td>/s', $component->html(), $matches);

    expect($matched)->toBe(1);
    expect(preg_match_all('/<a\b/', $matches[1]))->toBe(1);
});

it('does not render standalone view or edit actions', function (): void {
    actingAs(
        User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view', 'contact.*.update')
    );

    $contact = Contact::factory()->create();

    Livewire::test(ListContacts::class)
        ->assertActionDoesNotExist(TestAction::make('view')->table($contact))
        ->assertActionDoesNotExist(TestAction::make('edit')->table($contact));
});

describe('presence', function (): void {
    it('displays the presence icon, color, tooltip and accessible label', function (?int $minutesAgo, string $label, string $icon, string $color): void {
        freezeTime();

        actingAs(
            User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view')
        );

        $settings = app(PresenceSettings::class);
        $settings->active_threshold = 5;
        $settings->idle_threshold = 30;
        $settings->inactive_threshold = 480;
        $settings->save();

        $contact = Contact::factory()->create([
            'last_activity_at' => $minutesAgo === null ? null : now()->subMinutes($minutesAgo),
        ]);

        $component = Livewire::test(ListContacts::class)
            ->assertTableColumnHasExtraAttributes('full_name', [
                'role' => 'group',
                'aria-label' => "{$contact->full_name} (Presence: {$label})",
            ], $contact)
            ->assertTableColumnFormattedStateSet('full_name', $contact->full_name, $contact)
            ->assertSee(ContactResource::getUrl('view', ['record' => $contact]));

        $column = $component->instance()->getTable()->getColumn('full_name');
        assert($column instanceof TextColumn);
        $column->record($contact);

        expect($column->getTooltip())->toBe("Presence: {$label}")
            ->and($column->getIcon($contact->full_name))->toBe($icon)
            ->and($column->getIconColor($contact->full_name))->toBe($color);
    })->with([
        'online' => [0, 'Online', 'heroicon-m-check-circle', 'success'],
        'idle' => [10, 'Idle', 'heroicon-m-clock', 'warning'],
        'inactive' => [60, 'Inactive', 'heroicon-m-minus-circle', 'gray'],
        'offline' => [500, 'Offline', 'heroicon-m-x-circle', 'gray'],
        'no activity' => [null, 'Offline', 'heroicon-m-x-circle', 'gray'],
    ]);

    it('preserves the managed account explanation, link and email controls', function (): void {
        freezeTime();

        actingAs(
            User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view')
        );

        $contact = Contact::factory()->for(User::factory(), 'managedByUser')->create([
            'last_activity_at' => now(),
        ]);

        $lockIcon = generate_icon_html('heroicon-m-lock-closed', attributes: new ComponentAttributeBag(['class' => 'h-4 w-4 text-gray-400 dark:text-gray-500']));
        assert($lockIcon instanceof Htmlable);

        Livewire::test(ListContacts::class)
            ->assertSee('Presence: Online')
            ->assertSeeHtml(trim(Str::between($lockIcon->toHtml(), '>', '</svg>')))
            ->assertSeeText("This is a User's managed non-administrative account for the self-service portal.")
            ->assertSee(ContactResource::getUrl('view', ['record' => $contact]))
            ->assertSeeText($contact->email)
            ->assertSeeHtml('aria-label="Copy Email Address"');
    });

    it('displays presence without linking a contact the user cannot view', function (): void {
        actingAs(
            User::factory()->create()->givePermissionTo('contact.view-any')
        );

        $contact = Contact::factory()->create(['last_activity_at' => null]);

        $component = Livewire::test(ListContacts::class)
            ->assertTableColumnHasExtraAttributes('full_name', [
                'role' => 'group',
                'aria-label' => "{$contact->full_name} (Presence: Offline)",
            ], $contact)
            ->assertSee('Presence: Offline')
            ->assertDontSee(ContactResource::getUrl('view', ['record' => $contact]));

        expect($component->instance()->getTable()->getRecordUrl($contact))->toBeNull();
    });
});

describe('copy controls', function (): void {
    it('uses the same email description, tooltip and confirmation as Users', function (): void {
        actingAs(User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view'));

        $contact = Contact::factory()->create();

        $component = Livewire::test(ListContacts::class)
            ->assertSeeHtml('aria-label="Copy Email Address"')
            ->assertSeeHtml('x-on:click.prevent.stop=');

        $column = $component->instance()->getTable()->getColumn('full_name');
        assert($column instanceof TextColumn);
        $column->record($contact);

        $description = $column->getDescriptionBelow();
        assert($description instanceof View);

        expect($description->name())->toBe('filament.tables.columns.copyable-description')
            ->and($description->getData())->toMatchArray([
                'text' => $contact->email,
                'tooltip' => 'Copy Email Address',
                'copyMessage' => 'Email address copied to clipboard',
            ]);
    });

    it('omits the email copy control when there is no email', function (): void {
        actingAs(User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view'));

        $contact = Contact::factory()->create(['email' => null]);

        Livewire::test(ListContacts::class)
            ->assertTableColumnHasDescription('full_name', null, $contact)
            ->assertDontSeeHtml('aria-label="Copy Email Address"');
    });

    it('shows only populated identifiers and copies their raw values', function (?string $employeeId, ?string $studentId, array $fields): void {
        actingAs(User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view'));

        $contact = Contact::factory()->create([
            'employee_id' => $employeeId,
            'student_id' => $studentId,
        ]);

        $component = Livewire::test(ListContacts::class)
            ->assertTableColumnStateSet('identifiers', $fields === [] ? null : $fields, $contact);

        $column = $component->instance()->getTable()->getColumn('identifiers');
        assert($column instanceof TextColumn);
        $column->record($contact);

        foreach ([
            'employee_id' => [$employeeId, 'Employee ID:', 'Copy employee ID', 'Employee ID copied'],
            'student_id' => [$studentId, 'Student ID:', 'Copy student ID', 'Student ID copied'],
        ] as $field => [$value, $label, $tooltip, $message]) {
            if (blank($value)) {
                $component->assertDontSeeText($label)
                    ->assertDontSeeHtml('aria-label="' . $tooltip . '"');

                continue;
            }

            $formatted = $column->formatState($field);
            assert($formatted instanceof Htmlable);

            expect($formatted->toHtml())->toContain($label)
                ->toContain(e($value))
                ->toContain('aria-label="' . $tooltip . '"')
                ->toContain('x-on:click.prevent.stop=')
                ->toContain(Js::from($value)->toHtml())
                ->toContain(Js::from($message)->toHtml());

            expect($formatted->toHtml())->not->toContain('<script>');

            $component->assertSeeText($label)
                ->assertSeeText($value)
                ->assertSeeHtml('aria-label="' . $tooltip . '"')
                ->assertSeeHtml('x-on:click.prevent.stop=');
        }
    })->with([
        'both identifiers' => ['EMP-777', 'STU-888', ['employee_id', 'student_id']],
        'employee only' => ['EMP-777', null, ['employee_id']],
        'student only' => [null, 'STU-888', ['student_id']],
        'matching values' => ['SAME-123', 'SAME-123', ['employee_id', 'student_id']],
        'quoted and HTML values' => ['EMP-\'" & <script>x</script>', 'STU-<&>', ['employee_id', 'student_id']],
        'zero value' => ['0', null, ['employee_id']],
        'neither identifier' => [null, null, []],
    ]);
});

describe('search and filters', function (): void {
    it('searches by contact name and email', function (string $search): void {
        actingAs(User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view'));

        $matchingContact = Contact::factory()->create([
            'first_name' => 'Searchable',
            'last_name' => 'Contact',
            'email' => 'searchable-contact@example.com',
        ]);
        $otherContact = Contact::factory()->create();

        Livewire::test(ListContacts::class)
            ->searchTable($search)
            ->assertCanSeeTableRecords([$matchingContact])
            ->assertCanNotSeeTableRecords([$otherContact]);
    })->with(['Searchable Contact', 'searchable-contact@example.com']);
});

it('only shows the bulk delete action to a user with the contact.delete permission', function (): void {
    Contact::factory(15)->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('contact.view-any', 'contact.*.view');

    actingAs($user);

    Livewire::test(ListContacts::class)
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('contact.*.delete');

    Livewire::test(ListContacts::class)
        ->assertActionVisible(TestAction::make('delete')->table()->bulk());
});

it('can sort contacts by name', function (): void {
    actingAs(
        User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view')
    );

    $contacts = collect(['Charlie Brown', 'Alice Smith', 'Bob Jones'])
        ->map(fn (string $name): Contact => Contact::factory()->create(['full_name' => $name]));

    Livewire::test(ListContacts::class)
        ->sortTable('full_name')
        ->assertCanSeeTableRecords($contacts->sortBy('full_name')->values(), inOrder: true)
        ->sortTable('full_name', 'desc')
        ->assertCanSeeTableRecords($contacts->sortByDesc('full_name')->values(), inOrder: true);
});

it('hides the created at and updated at columns by default and allows sorting them', function (string $column): void {
    actingAs(
        User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view')
    );

    $contacts = Contact::factory()->count(3)->create();

    Livewire::test(ListContacts::class)
        ->assertTableColumnExists($column, fn (TextColumn $column): bool => $column->isToggleable() && $column->isToggledHiddenByDefault() && $column->isSortable())
        ->assertCanNotRenderTableColumn($column)
        ->toggleAllTableColumns()
        ->assertCanRenderTableColumn($column)
        ->sortTable($column)
        ->assertCanSeeTableRecords($contacts);
})->with(['created_at', 'updated_at']);
