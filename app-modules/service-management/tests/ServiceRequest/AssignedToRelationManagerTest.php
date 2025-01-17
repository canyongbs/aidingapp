<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageAssignments;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\RelationManagers\AssignedToRelationManager;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Team\Models\Team;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Forms\Components\Select;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('During assignment only Users that are on a Team that is Manager of the Type of this Service Request should be options', function () {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();
    $userWithoutTeam = User::factory()->licensed([Contact::getLicenseType()])->create();

    $team = Team::factory()->create();

    $user->teams()->attach($team);

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managers()->attach($team);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->mountTableAction('assign-service-request')
        ->assertFormFieldExists('userId', 'mountedTableActionForm', function (Select $select) use ($user) {
            $options = $select->getSearchResults($user->name);

            return empty($options) ? false : true;
        })
        ->assertSuccessful()
        ->assertFormFieldExists('userId', 'mountedTableActionForm', function (Select $select) use ($userWithoutTeam) {
            $options = $select->getSearchResults($userWithoutTeam->name);

            return empty($options) ? true : false;
        })
        ->assertSuccessful();
});

test('During reassignment current assigned user should not be in options', function () {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();
    $secondUser = User::factory()->licensed([Contact::getLicenseType()])->create();

    $team = Team::factory()->create();

    $user->teams()->attach($team);

    $secondUser->teams()->attach($team);

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managers()->attach($team);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    $assignedServiceRequest = ServiceRequestAssignment::factory()->state([
        'service_request_id' => $serviceRequestsWithManager->getKey(),
        'user_id' => $user->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->mountTableAction('assign-service-request')
        ->assertFormFieldExists('userId', 'mountedTableActionForm', function (Select $select) use ($user) {
            $options = $select->getSearchResults($user->name);

            return empty($options) ? true : false;
        })
        ->assertSuccessful()
        ->assertFormFieldExists('userId', 'mountedTableActionForm', function (Select $select) use ($secondUser) {
            $options = $select->getSearchResults($secondUser->name);

            return empty($options) ? false : true;
        })
        ->assertSuccessful();
});
