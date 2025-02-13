<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource\Pages\EditIncidentStatus;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use AidingApp\ServiceManagement\Tests\RequestFactories\IncidentStatusRequestFactory;
use App\Models\User;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('EditIncidentStatus is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $incidentStatus = IncidentStatus::factory()->create();

    actingAs($user)
        ->get(
            IncidentStatusResource::getUrl('edit', [
                'record' => $incidentStatus,
            ])
        )->assertForbidden();

    livewire(EditIncidentStatus::class, [
        'record' => $incidentStatus->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    actingAs($user)
        ->get(
            IncidentStatusResource::getUrl('edit', [
                'record' => $incidentStatus,
            ])
        )->assertSuccessful();

    $request = collect(IncidentStatusRequestFactory::new()->create());

    livewire(EditIncidentStatus::class, [
        'record' => $incidentStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($incidentStatus->fresh()->name)->toEqual($request->get('name'))
        ->and($incidentStatus->fresh()->classification)->toEqual($request->get('classification'));
});

test('EditIncidentStatus validates the inputs', function ($data, $errors) {
    asSuperAdmin();

    $incidentStatus = IncidentStatus::factory()->create();

    $request = collect(IncidentStatusRequestFactory::new($data)->create());

    livewire(EditIncidentStatus::class, [
        'record' => $incidentStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasFormErrors($errors);
})->with(
    [
        'name required' => [
            IncidentStatusRequestFactory::new()->state(['name' => null]),
            ['name' => 'required'],
        ],
        'name string' => [
            IncidentStatusRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            IncidentStatusRequestFactory::new()->state(['name' => str()->random(256)]),
            ['name' => 'max'],
        ],
        'classification required' => [
            IncidentStatusRequestFactory::new()->state(['classification' => null]),
            ['classification' => 'required'],
        ],
    ]
);

test('delete action visible with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $incidentStatus = IncidentStatus::factory()->create();

    actingAs($user);

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    livewire(EditIncidentStatus::class, [
        'record' => $incidentStatus->getRouteKey(),
    ])
        ->assertActionHidden(DeleteAction::class);

    $user->givePermissionTo('product_admin.*.delete');

    livewire(EditIncidentStatus::class, [
        'record' => $incidentStatus->getRouteKey(),
    ])
        ->assertActionVisible(DeleteAction::class);
});
