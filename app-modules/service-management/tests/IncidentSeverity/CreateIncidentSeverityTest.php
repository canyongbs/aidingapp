<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource\Pages\CreateIncidentSeverity;
use AidingApp\ServiceManagement\Models\IncidentSeverity;
use AidingApp\ServiceManagement\Tests\RequestFactories\IncidentSeverityRequestFactory;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Tests\asSuperAdmin;

test('CreateIncidentSeverity is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            IncidentSeverityResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateIncidentSeverity::class)
        ->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.create');

    actingAs($user)
        ->get(
            IncidentSeverityResource::getUrl('create')
        )->assertSuccessful();

    $request = IncidentSeverityRequestFactory::new()->create();

    livewire(CreateIncidentSeverity::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, IncidentSeverity::all());

    assertDatabaseHas(IncidentSeverity::class, $request);
});

test('CreateIncidentSeverity validates the inputs', function ($data, $errors) {
    asSuperAdmin();

    $request = IncidentSeverityRequestFactory::new($data)->create();

    livewire(CreateIncidentSeverity::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasFormErrors($errors);
})->with(
    [
        'name required' => [
            IncidentSeverityRequestFactory::new()->without('name'),
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
