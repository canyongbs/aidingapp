<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\ManageServiceRequestTypeManager;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Team\Models\Team;
use App\Models\User;
use Filament\Tables\Actions\AttachAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('can attach team member to service request type',function(){
    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $team = Team::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('service-request-type-manager', [
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )->assertForbidden();

    $user->givePermissionTo('service_request_type.view-any');
    $user->givePermissionTo('team.view-any');

    livewire(ManageServiceRequestTypeManager::class, [
                'record' => $serviceRequestType->getRouteKey(),
            ])
            ->callTableAction(
                AttachAction::class,
                data: ['recordId' => $team->getKey()]
            )->assertSuccessful();

    expect($serviceRequestType->refresh())
            ->serviceRequestTypeManager
            ->pluck('id')
            ->toContain($team->getKey());
});