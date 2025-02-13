<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource\Pages\EditIncidentSeverity;
use AidingApp\ServiceManagement\Models\IncidentSeverity;
use AidingApp\ServiceManagement\Tests\RequestFactories\IncidentSeverityRequestFactory;
use App\Models\User;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('EditIncidentSeverity is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $incidentSeverity = IncidentSeverity::factory()->create();

    actingAs($user)
        ->get(
            IncidentSeverityResource::getUrl('edit', [
                'record' => $incidentSeverity,
            ])
        )->assertForbidden();

    livewire(EditIncidentSeverity::class, [
        'record' => $incidentSeverity->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    actingAs($user)
        ->get(
            IncidentSeverityResource::getUrl('edit', [
                'record' => $incidentSeverity,
            ])
        )->assertSuccessful();

    $request = collect(IncidentSeverityRequestFactory::new()->create());

    livewire(EditIncidentSeverity::class, [
        'record' => $incidentSeverity->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($incidentSeverity->fresh()->name)->toEqual($request->get('name'));
});

test('EditIncidentSeverity validates the inputs', function ($data, $errors) {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    $incidentSeverity = IncidentSeverity::factory()->create();

    $request = IncidentSeverityRequestFactory::new($data)->create();

    livewire(EditIncidentSeverity::class, [
        'record' => $incidentSeverity->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors);
})->with(
    [
        'name required' => [
            IncidentSeverityRequestFactory::new()->state(['name' => null]),
            ['name' => 'required'],
        ],
        'name string' => [
            IncidentSeverityRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            IncidentSeverityRequestFactory::new()->state(['name' => str()->random(256)]),
            ['name' => 'max'],
        ],
    ]
);

test('delete action visible with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $incidentSeverity = IncidentSeverity::factory()->create();

    actingAs($user);

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    livewire(EditIncidentSeverity::class, [
        'record' => $incidentSeverity->getRouteKey(),
    ])
        ->assertActionHidden(DeleteAction::class);

    $user->givePermissionTo('product_admin.*.delete');

    livewire(EditIncidentSeverity::class, [
        'record' => $incidentSeverity->getRouteKey(),
    ])
        ->assertActionVisible(DeleteAction::class);
});
