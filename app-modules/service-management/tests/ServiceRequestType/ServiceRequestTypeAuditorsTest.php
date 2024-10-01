<?php

use App\Models\User;
use AidingApp\Team\Models\Team;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AidingApp\Contact\Models\Contact;
use Filament\Tables\Actions\AttachAction;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\ManageServiceRequestTypeAuditors;

it('can attach audit member to service request type', function () {
    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $team = Team::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('service-request-type-auditors', [
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )->assertForbidden();

    $user->givePermissionTo('service_request_type.view-any');
    $user->givePermissionTo('team.view-any');

    livewire(ManageServiceRequestTypeAuditors::class, [
        'record' => $serviceRequestType->getRouteKey(),
    ])
        ->callTableAction(
            AttachAction::class,
            data: ['recordId' => $team->getKey()]
        )->assertSuccessful();

    expect($serviceRequestType->refresh())
        ->auditors
        ->pluck('id')
        ->toContain($team->getKey());
});
