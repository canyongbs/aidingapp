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

use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\AdvisoryResource;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\Pages\ViewAdvisory;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('The correct details are displayed on the ViewAdvisory page', function () {
    $user = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');

    $advisory = Advisory::factory()->create();

    asSuperAdmin()
        ->get(
            AdvisoryResource::getUrl('view', [
                'record' => $advisory,
            ])
        )
        ->assertSuccessful()
        ->assertSeeInOrder(
            [
                'Properties',
                'Title',
                $advisory->title,
                'Description',
                $advisory->description,
                'Tracking Details',
                'Severity',
                $advisory->severity->name,
                'Status',
                $advisory->status->name,
                'Assignment',
                'Department',
                $advisory->assignedDepartment->name,
            ]
        );
});

test('ViewAdvisory is gated with proper access control', function () {
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

test('the Properties section edit action is gated with proper access control', function () {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');

    livewire(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertActionDoesNotExist(TestAction::make('editProperties')->schemaComponent('properties'));

    $user->givePermissionTo('advisory.*.update');

    livewire(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertActionVisible(TestAction::make('editProperties')->schemaComponent('properties'));
});

test('the Properties section can be updated', function () {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');
    $user->givePermissionTo('advisory.*.update');

    livewire(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->callAction(TestAction::make('editProperties')->schemaComponent('properties'), data: [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
        ])
        ->assertHasNoFormErrors();

    expect($advisory->fresh())
        ->title->toEqual('Updated Title')
        ->description->toEqual('Updated Description');
});

test('the Properties section validates the inputs', function () {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');
    $user->givePermissionTo('advisory.*.update');

    livewire(ViewAdvisory::class, [
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

test('the Tracking Details section edit action is gated with proper access control', function () {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');

    livewire(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertActionDoesNotExist(TestAction::make('editTrackingDetails')->schemaComponent('trackingDetails'));

    $user->givePermissionTo('advisory.*.update');

    livewire(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertActionVisible(TestAction::make('editTrackingDetails')->schemaComponent('trackingDetails'));
});

test('the Tracking Details section can be updated', function () {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    $severity = AdvisorySeverity::factory()->create();
    $status = AdvisoryStatus::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');
    $user->givePermissionTo('advisory.*.update');

    livewire(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->callAction(TestAction::make('editTrackingDetails')->schemaComponent('trackingDetails'), data: [
            'severity_id' => $severity->getKey(),
            'status_id' => $status->getKey(),
        ])
        ->assertHasNoFormErrors();

    expect($advisory->fresh())
        ->severity_id->toEqual($severity->getKey())
        ->status_id->toEqual($status->getKey());
});

test('the Status entry reflects the new value immediately after updating the Tracking Details section', function () {
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

    $component = livewire(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->callAction(TestAction::make('editTrackingDetails')->schemaComponent('trackingDetails'), data: [
            'severity_id' => $newSeverity->getKey(),
            'status_id' => $newStatus->getKey(),
        ])
        ->assertHasNoFormErrors();

    dump([
        'old severity seen' => str_contains($component->html(), e($oldSeverity->name)),
        'new severity seen' => str_contains($component->html(), e($newSeverity->name)),
        'old status seen' => str_contains($component->html(), e($oldStatus->name)),
        'new status seen' => str_contains($component->html(), e($newStatus->name)),
    ]);
});

test('the Tracking Details section validates the inputs', function ($overrides, $errors) {
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

    livewire(ViewAdvisory::class, [
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

test('the Assignment section edit action is gated with proper access control', function () {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');

    livewire(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertActionDoesNotExist(TestAction::make('editAssignment')->schemaComponent('assignment'));

    $user->givePermissionTo('advisory.*.update');

    livewire(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertActionVisible(TestAction::make('editAssignment')->schemaComponent('assignment'));
});

test('the Assignment section can be updated', function () {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    $department = Department::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');
    $user->givePermissionTo('advisory.*.update');

    livewire(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->callAction(TestAction::make('editAssignment')->schemaComponent('assignment'), data: [
            'assigned_department_id' => $department->getKey(),
        ])
        ->assertHasNoFormErrors();

    expect($advisory->fresh()->assigned_department_id)->toEqual($department->getKey());
});

test('the Assignment section validates the department exists', function () {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.view');
    $user->givePermissionTo('advisory.*.update');

    livewire(ViewAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->callAction(TestAction::make('editAssignment')->schemaComponent('assignment'), data: [
            'assigned_department_id' => fake()->uuid(),
        ])
        ->assertHasFormErrors([
            'assigned_department_id' => 'in',
        ]);
});
