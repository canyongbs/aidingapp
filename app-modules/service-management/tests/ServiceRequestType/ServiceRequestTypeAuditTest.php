<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\ManageServiceRequestTypeAudit;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Team\Models\Team;
use App\Models\User;
use Filament\Tables\Actions\AttachAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('can attach audit member to service request type',function(){
    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $team = Team::factory()->create();

    actingAs($user)
        ->get(
            ServiceRequestTypeResource::getUrl('service-request-type-audits', [
                'record' => $serviceRequestType->getRouteKey(),
            ])
        )->assertForbidden();

    $user->givePermissionTo('service_request_type.view-any');
    $user->givePermissionTo('team.view-any');

    livewire(ManageServiceRequestTypeAudit::class, [
                'record' => $serviceRequestType->getRouteKey(),
            ])
            ->callTableAction(
                AttachAction::class,
                data: ['recordId' => $team->getKey()]
            )->assertSuccessful();

    expect($serviceRequestType->refresh())
            ->auditTeams
            ->pluck('id')
            ->toContain($team->getKey());
});