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

use AidingApp\Contact\Models\ContactType;
use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Enums\AdvisoryVisibility;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\AdvisoryResource;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\Pages\ViewAdvisory;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\RelationManagers\AdvisoryUpdatesRelationManager;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use App\Features\AdvisoryVisibilityFeature;
use App\Models\User;
use CanyonGBS\Common\Enums\Color;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

$visibilityInfolistEntry = function (Testable $component, string $name): TextEntry {
    $entry = $component->instance()
        ->getSchema('infolist')
        ->getComponent(fn ($entry): bool => $entry instanceof TextEntry && $entry->getName() === $name, withHidden: true);
    assert($entry instanceof TextEntry);

    return $entry;
};

test('The correct details are displayed on the ViewAdvisory page', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');

    $advisory = Advisory::factory()->create();

    asSuperAdmin();

    $component = Livewire::test(ViewAdvisory::class, ['record' => $advisory->getRouteKey()])
        ->assertSuccessful();

    $expected = [
        'title' => $advisory->title,
        'description' => $advisory->description,
        'severity.name' => $advisory->severity->name,
        'status.name' => $advisory->status->name,
        'assignedDepartment.name' => $advisory->assignedDepartment->name,
    ];

    expect(\Tests\infolistState($component->instance(), array_keys($expected)))->toEqual($expected);
});

test('the Severity entry renders as a badge using the severity color', function (): void {
    asSuperAdmin();

    $advisory = Advisory::factory()
        ->for(AdvisorySeverity::factory()->color(Color::Red), 'severity')
        ->create();

    $entry = Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertSuccessful()
        ->instance()
        ->getSchema('infolist')
        ->getComponent(fn ($component): bool => $component instanceof TextEntry && $component->getName() === 'severity.name');

    expect($entry)
        ->not->toBeNull()
        ->and($entry->isBadge())->toBeTrue()
        ->and($entry->getColor($advisory->severity->name))->toBe(Color::Red->value);
});

test('the Tracking Details status refreshes when an advisory update changes the status', function (): void {
    $user = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');

    $originalStatus = AdvisoryStatus::factory()->create(['name' => 'Original Status']);
    $newStatus = AdvisoryStatus::factory()->create(['name' => 'New Status']);

    $advisory = Advisory::factory()->create([
        'status_id' => $originalStatus->getKey(),
    ]);

    $component = Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertSuccessful();

    expect(\Tests\infolistState($component->instance(), ['status.name']))
        ->toBe(['status.name' => $originalStatus->name]);

    $advisory->update(['status_id' => $newStatus->getKey()]);

    $component->dispatch('advisory-updated');

    expect(\Tests\infolistState($component->instance(), ['status.name']))
        ->toBe(['status.name' => $newStatus->name]);
});

test('ViewAdvisory is gated with proper access control', function (): void {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    asSuperAdmin($user);

    actingAs($user)
        ->get(
            AdvisoryResource::getUrl('view', [
                'record' => $advisory,
            ])
        )->assertSuccessful();
});

test('the Properties section edit action is gated with proper access control', function (): void {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');

    Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertActionDoesNotExist(TestAction::make('editProperties')->schemaComponent('properties'));

    $user->givePermissionTo('advisory.*.update');

    Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertActionVisible(TestAction::make('editProperties')->schemaComponent('properties'));
});

test('the Properties section can be updated', function (): void {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');
    $user->givePermissionTo('advisory.*.update');

    Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->callAction(TestAction::make('editProperties')->schemaComponent('properties'), data: [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ])
        ->assertHasNoFormErrors();

    expect($advisory->fresh()->title)->toEqual('Updated Title')->and($advisory->fresh()->description)->toEqual('Updated Description');
});

test('the Properties section validates the inputs', function (): void {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');
    $user->givePermissionTo('advisory.*.update');

    Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->callAction(TestAction::make('editProperties')->schemaComponent('properties'), data: [
            'title' => null,
            'description' => null,
        ])
        ->assertHasFormErrors([
            'title' => 'required',
            'description' => 'required',
        ]);
});

test('the Tracking Details section edit action is gated with proper access control', function (): void {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');

    Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertActionDoesNotExist(TestAction::make('editTrackingDetails')->schemaComponent('trackingDetails'));

    $user->givePermissionTo('advisory.*.update');

    Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertActionVisible(TestAction::make('editTrackingDetails')->schemaComponent('trackingDetails'));
});

test('the Tracking Details section can be updated', function (): void {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    $severity = AdvisorySeverity::factory()->create();
    $status = AdvisoryStatus::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');
    $user->givePermissionTo('advisory.*.update');

    Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->callAction(TestAction::make('editTrackingDetails')->schemaComponent('trackingDetails'), data: [
            'severity_id' => $severity->getKey(),
            'status_id' => $status->getKey(),
        ])
        ->assertHasNoFormErrors();

    expect($advisory->fresh()->severity_id)->toEqual($severity->getKey())->and($advisory->fresh()->status_id)->toEqual($status->getKey());
});

test('the Status entry reflects the new value immediately after updating the Tracking Details section', function (): void {
    $user = User::factory()->create();

    $oldStatus = AdvisoryStatus::factory()->create();
    $oldSeverity = AdvisorySeverity::factory()->create();

    $advisory = Advisory::factory()->create([
        'status_id' => $oldStatus->getKey(),
        'severity_id' => $oldSeverity->getKey(),
    ]);

    $newSeverity = AdvisorySeverity::factory()->create();
    $newStatus = AdvisoryStatus::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');
    $user->givePermissionTo('advisory.*.update');

    Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->callAction(TestAction::make('editTrackingDetails')->schemaComponent('trackingDetails'), data: [
            'severity_id' => $newSeverity->getKey(),
            'status_id' => $newStatus->getKey(),
        ])
        ->assertHasNoFormErrors()
        ->assertSee($newSeverity->name)
        ->assertSee($newStatus->name)
        ->assertDontSee($oldSeverity->name)
        ->assertDontSee($oldStatus->name);
});

test('the Tracking Details section validates the inputs', function (array $overrides, array $errors): void {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    $severity = AdvisorySeverity::factory()->create();
    $status = AdvisoryStatus::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');
    $user->givePermissionTo('advisory.*.update');

    $data = array_merge([
        'severity_id' => $severity->getKey(),
        'status_id' => $status->getKey(),
    ], $overrides);

    Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->callAction(TestAction::make('editTrackingDetails')->schemaComponent('trackingDetails'), data: $data)
        ->assertHasFormErrors($errors);
})->with([
    'severity_id missing' => [
        ['severity_id' => null],
        ['severity_id' => 'required'],
    ],
    'severity_id does not exist' => [
        ['severity_id' => fake()->uuid()],
        ['severity_id' => 'in'],
    ],
    'status_id missing' => [
        ['status_id' => null],
        ['status_id' => 'required'],
    ],
    'status_id does not exist' => [
        ['status_id' => fake()->uuid()],
        ['status_id' => 'in'],
    ],
]);

test('the Assignment section edit action is gated with proper access control', function (): void {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');

    Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertActionDoesNotExist(TestAction::make('editAssignment')->schemaComponent('assignment'));

    $user->givePermissionTo('advisory.*.update');

    Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertActionVisible(TestAction::make('editAssignment')->schemaComponent('assignment'));
});

test('the Assignment section can be updated', function (): void {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    $department = Department::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');
    $user->givePermissionTo('advisory.*.update');

    Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->callAction(TestAction::make('editAssignment')->schemaComponent('assignment'), data: [
            'assigned_department_id' => $department->getKey(),
        ])
        ->assertHasNoFormErrors();

    expect($advisory->fresh()->assigned_department_id)->toEqual($department->getKey());
});

test('the Assignment section validates the department exists', function (): void {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');
    $user->givePermissionTo('advisory.*.update');

    Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->callAction(TestAction::make('editAssignment')->schemaComponent('assignment'), data: [
            'assigned_department_id' => fake()->uuid(),
        ])
        ->assertHasFormErrors([
            'assigned_department_id' => 'in',
        ]);
});

test('the advisory updates are only embedded for users who can view them', function (): void {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');

    Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertDontSeeLivewire(AdvisoryUpdatesRelationManager::class);

    $user->givePermissionTo('advisory_update.view-any');

    Livewire::test(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertSeeLivewire(AdvisoryUpdatesRelationManager::class);
});

describe('advisory visibility', function () use ($visibilityInfolistEntry): void {
    it('displays the visibility of the advisory', function () use ($visibilityInfolistEntry): void {
        asSuperAdmin();

        $contactTypes = ContactType::factory()->count(2)->sequence(['name' => 'Student'], ['name' => 'Alumni'])->create();

        $advisory = Advisory::factory()->restrictedToContactTypes(...$contactTypes)->create();

        $component = Livewire::test(ViewAdvisory::class, ['record' => $advisory->getRouteKey()]);

        $component->assertSuccessful();

        $state = \Tests\infolistState($component->instance(), ['visibility', 'is_visibility_restricted', 'restrictedToContactTypes.name']);

        $names = $state['restrictedToContactTypes.name'];
        sort($names);

        expect($state['visibility'])->toBe(AdvisoryVisibility::Authenticated)
            ->and($state['is_visibility_restricted'])->toBeTrue()
            ->and($names)->toBe(['Alumni', 'Student']);

        expect($visibilityInfolistEntry($component, 'is_visibility_restricted')->isVisible())->toBeTrue()
            ->and($visibilityInfolistEntry($component, 'restrictedToContactTypes.name')->isVisible())->toBeTrue();
    });

    it('does not display the contact types for an advisory that is not restricted', function () use ($visibilityInfolistEntry): void {
        asSuperAdmin();

        $advisory = Advisory::factory()
            ->restrictedToContactTypes(ContactType::factory()->create())
            ->create(['is_visibility_restricted' => false]);

        $component = Livewire::test(ViewAdvisory::class, ['record' => $advisory->getRouteKey()]);

        $component->assertSuccessful();

        expect($visibilityInfolistEntry($component, 'is_visibility_restricted')->isVisible())->toBeTrue()
            ->and($visibilityInfolistEntry($component, 'restrictedToContactTypes.name')->isVisible())->toBeFalse();
    });

    it('does not display the restriction for a public advisory', function () use ($visibilityInfolistEntry): void {
        asSuperAdmin();

        $component = Livewire::test(ViewAdvisory::class, ['record' => Advisory::factory()->create()->getRouteKey()]);

        $component->assertSuccessful();

        expect($visibilityInfolistEntry($component, 'is_visibility_restricted')->isVisible())->toBeFalse()
            ->and($visibilityInfolistEntry($component, 'restrictedToContactTypes.name')->isVisible())->toBeFalse();
    });

    it('does not display the visibility section until the advisory visibility migration has run', function (): void {
        asSuperAdmin();

        $record = Advisory::factory()->create()->getRouteKey();

        $section = fn (Testable $component): Section => $component->instance()
            ->getSchema('infolist')
            ->getComponent('visibility', withHidden: true);

        expect($section(Livewire::test(ViewAdvisory::class, ['record' => $record]))->isVisible())->toBeTrue();

        AdvisoryVisibilityFeature::deactivate();

        expect($section(Livewire::test(ViewAdvisory::class, ['record' => $record]))->isVisible())->toBeFalse();
    });

    it('gates the edit action with proper access control', function (): void {
        $user = User::factory()->create();

        $advisory = Advisory::factory()->create();

        actingAs($user);

        $user->givePermissionTo('advisory.view-any');
        $user->givePermissionTo('advisory.*.view');

        Livewire::test(ViewAdvisory::class, ['record' => $advisory->getRouteKey()])
            ->assertActionDoesNotExist(TestAction::make('editVisibility')->schemaComponent('visibility'));

        $user->givePermissionTo('advisory.*.update');

        Livewire::test(ViewAdvisory::class, ['record' => $advisory->getRouteKey()])
            ->assertActionVisible(TestAction::make('editVisibility')->schemaComponent('visibility'));
    });

    it('loads the current visibility into the edit action', function (): void {
        asSuperAdmin();

        $contactTypes = ContactType::factory()->count(2)->create();

        $advisory = Advisory::factory()->restrictedToContactTypes(...$contactTypes)->create();

        Livewire::test(ViewAdvisory::class, ['record' => $advisory->getRouteKey()])
            ->mountAction(TestAction::make('editVisibility')->schemaComponent('visibility'))
            ->assertSchemaStateSet(['is_visibility_restricted' => true])
            ->assertSchemaComponentExists('restrictedToContactTypes', 'mountedActionSchema0', function (Select $select) use ($contactTypes): bool {
                $state = $select->getState();
                $expected = $contactTypes->pluck('id')->all();

                assert(is_array($state));

                sort($state);
                sort($expected);

                return $state === $expected;
            });
    });

    it('can update the visibility to specific contact types', function (): void {
        asSuperAdmin();

        $advisory = Advisory::factory()->create();
        $contactTypes = ContactType::factory()->count(2)->create();

        Livewire::test(ViewAdvisory::class, ['record' => $advisory->getRouteKey()])
            ->callAction(TestAction::make('editVisibility')->schemaComponent('visibility'), data: [
                'visibility' => AdvisoryVisibility::Authenticated->value,
                'is_visibility_restricted' => true,
                'restrictedToContactTypes' => $contactTypes->pluck('id')->all(),
            ])
            ->assertHasNoFormErrors();

        $advisory->refresh();

        expect($advisory->visibility)->toBe(AdvisoryVisibility::Authenticated)
            ->and($advisory->is_visibility_restricted)->toBeTrue()
            ->and($advisory->restrictedToContactTypes->pluck('id')->sort()->values()->all())->toBe($contactTypes->pluck('id')->sort()->values()->all());
    });

    it('can update the visibility back to public', function (): void {
        asSuperAdmin();

        $advisory = Advisory::factory()->restrictedToContactTypes(ContactType::factory()->create())->create();

        Livewire::test(ViewAdvisory::class, ['record' => $advisory->getRouteKey()])
            ->callAction(TestAction::make('editVisibility')->schemaComponent('visibility'), data: [
                'visibility' => AdvisoryVisibility::Public->value,
            ])
            ->assertHasNoFormErrors();

        expect($advisory->refresh()->visibility)->toBe(AdvisoryVisibility::Public);
    });

    it('requires at least one contact type when restricted to specific contact types', function (): void {
        asSuperAdmin();

        $advisory = Advisory::factory()->create();

        Livewire::test(ViewAdvisory::class, ['record' => $advisory->getRouteKey()])
            ->callAction(TestAction::make('editVisibility')->schemaComponent('visibility'), data: [
                'visibility' => AdvisoryVisibility::Authenticated->value,
                'is_visibility_restricted' => true,
                'restrictedToContactTypes' => [],
            ])
            ->assertHasFormErrors(['restrictedToContactTypes' => 'required']);

        expect($advisory->refresh()->visibility)->toBe(AdvisoryVisibility::Public);
    });
});
