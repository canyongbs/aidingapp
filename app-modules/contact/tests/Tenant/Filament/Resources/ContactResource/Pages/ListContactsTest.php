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
use Filament\Support\Enums\IconSize;

use function Filament\Support\generate_icon_html;

use Filament\Support\View\ComponentAttributeBag;
use Filament\Tables\View\Components\Columns\TextColumnComponent\ItemComponent\IconComponent;
use Illuminate\Contracts\Support\Htmlable;
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

    Livewire::test(ListContacts::class)
        ->assertSeeText('2 Open')
        ->assertSeeText('3 Total');
});

it('displays 0 open and 0 total when the contact has no service requests', function (): void {
    actingAs(
        User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view')
    );

    Contact::factory()->create();

    Livewire::test(ListContacts::class)
        ->assertSeeText('0 Open')
        ->assertSeeText('0 Total');
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

    Contact::factory()->for($organization, 'organization')->create();

    Livewire::test(ListContacts::class)
        ->assertSeeText('Acme University');
});

it('links the contact name to the view page', function (): void {
    actingAs(
        User::factory()->create()->givePermissionTo('contact.view-any', 'contact.*.view')
    );

    $contact = Contact::factory()->create();

    $component = Livewire::test(ListContacts::class)
        ->assertSee(ContactResource::getUrl('view', ['record' => $contact]));

    $matched = preg_match('/<td\b[^>]*class="[^"]*\bfi-ta-cell-contact\b[^"]*"[^>]*>(.*?)<\/td>/s', $component->html(), $matches);

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

        $presenceIcon = generate_icon_html($icon, attributes: (new ComponentAttributeBag())->color(IconComponent::class, $color), size: IconSize::Small);
        assert($presenceIcon instanceof Htmlable);

        Livewire::test(ListContacts::class)
            ->assertSeeHtml('aria-label="' . e($contact->full_name) . ' (Presence: ' . $label . ')" class="flex items-center gap-x-1.5"')
            ->assertSeeHtml('x-tooltip="{ content: ' . Js::from('Presence: ' . $label)->toHtml() . ', theme: $store.theme }"')
            ->assertSeeHtml($presenceIcon->toHtml())
            ->assertSee(ContactResource::getUrl('view', ['record' => $contact]));
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

        $lockIcon = generate_icon_html('heroicon-m-lock-closed', attributes: new ComponentAttributeBag(['class' => 'h-4 w-4 shrink-0 text-gray-400 dark:text-gray-500']));
        assert($lockIcon instanceof Htmlable);

        Livewire::test(ListContacts::class)
            ->assertSee('Presence: Online')
            ->assertSeeHtml(trim(Str::between($lockIcon->toHtml(), '>', '</svg>')))
            ->assertSeeText("This is a User's managed non-administrative account for the self-service portal.")
            ->assertSee(ContactResource::getUrl('view', ['record' => $contact]))
            ->assertSeeText($contact->email)
            ->assertSeeHtml('aria-label="Copy email address"');
    });

    it('displays presence without linking a contact the user cannot view', function (): void {
        actingAs(
            User::factory()->create()->givePermissionTo('contact.view-any')
        );

        $contact = Contact::factory()->create(['last_activity_at' => null]);

        Livewire::test(ListContacts::class)
            ->assertSeeHtml('aria-label="' . e($contact->full_name) . ' (Presence: Offline)"')
            ->assertSee('Presence: Offline')
            ->assertDontSee(ContactResource::getUrl('view', ['record' => $contact]));
    });
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
