<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Models\User;
use AidingApp\Team\Models\Team;

use function Tests\asSuperAdmin;

use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AidingApp\Contact\Models\Contact;
use Filament\Forms\Components\Select;
use AidingApp\Authorization\Models\License;

use function PHPUnit\Framework\assertCount;
use function Pest\Laravel\assertDatabaseHas;

use AidingApp\Authorization\Enums\LicenseType;

use function Pest\Laravel\assertDatabaseMissing;

use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeAssignmentTypes;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AidingApp\ServiceManagement\Tests\RequestFactories\CreateServiceRequestRequestFactory;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\CreateServiceRequest;

test('A successful action on the CreateServiceRequest page', function () {
    asSuperAdmin()
        ->get(
            ServiceRequestResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = collect(CreateServiceRequestRequestFactory::new()->create());

    livewire(CreateServiceRequest::class)
        ->fillForm($request->toArray())
        ->fillForm([
            'respondent_id' => Contact::factory()->create()->getKey(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequest::all());

    assertDatabaseHas(
        ServiceRequest::class,
        $request->except(
            [
                'division_id',
                'status_id',
                'priority_id',
                'respondent_id',
                'type_id',
            ]
        )->toArray()
    );

    $serviceRequest = ServiceRequest::first();

    expect($serviceRequest->division->id)
        ->toEqual($request->get('division_id'))
        ->and($serviceRequest->status->id)
        ->toEqual($request->get('status_id'))
        ->and($serviceRequest->priority->id)
        ->toEqual($request->get('priority_id'));
});

test('CreateServiceRequest requires valid data', function ($data, $errors, $setup = null) {
    if ($setup) {
        $setup();
    }

    asSuperAdmin();

    $request = collect(CreateServiceRequestRequestFactory::new($data)->create());

    livewire(CreateServiceRequest::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertDatabaseMissing(ServiceRequest::class, $request->except(['division_id', 'status_id', 'priority_id', 'type_id'])->toArray());
})->with(
    [
        'division_id missing' => [CreateServiceRequestRequestFactory::new()->without('division_id'), ['division_id' => 'required']],
        'division_id does not exist' => [
            CreateServiceRequestRequestFactory::new()->state(['division_id' => fake()->uuid()]),
            ['division_id' => 'exists'],
        ],
        'status_id missing' => [CreateServiceRequestRequestFactory::new()->without('status_id'), ['status_id' => 'required']],
        'status_id does not exist' => [
            CreateServiceRequestRequestFactory::new()->state(['status_id' => fake()->uuid()]),
            ['status_id' => 'exists'],
        ],
        'priority_id missing' => [CreateServiceRequestRequestFactory::new()->without('priority_id'), ['priority_id' => 'required']],
        'priority_id does not exist' => [
            CreateServiceRequestRequestFactory::new()->state(['priority_id' => fake()->uuid(), 'type_id' => fake()->uuid()]),
            ['priority_id' => 'exists'],
        ],
        'close_details is not a string' => [CreateServiceRequestRequestFactory::new()->state(['close_details' => 1]), ['close_details' => 'string']],
        'res_details is not a string' => [CreateServiceRequestRequestFactory::new()->state(['res_details' => 1]), ['res_details' => 'string']],
    ]
);

// Permission Tests

test('CreateServiceRequest is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateServiceRequest::class)
        ->assertForbidden();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.create');

    $team = Team::factory()->create();

    $user->teams()->attach($team);

    $user->refresh();

    $serviceRequestTypesWithManager = ServiceRequestType::factory()->create();

    $serviceRequestTypesWithManager->managers()->attach($team);

    $serviceRequestTypesWithManager->save();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateServiceRequestRequestFactory::new()->create([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestTypesWithManager->getKey(),
        ])->getKey(),
    ]));

    livewire(CreateServiceRequest::class)
        ->fillForm($request->toArray())
        ->fillForm([
            'respondent_id' => Contact::factory()->create()->getKey(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequest::all());

    assertDatabaseHas(
        ServiceRequest::class,
        $request->except(
            [
                'division_id',
                'status_id',
                'priority_id',
                'respondent_id',
                'type_id',
            ]
        )->toArray()
    );

    $serviceRequest = ServiceRequest::first();

    expect($serviceRequest->division->id)
        ->toEqual($request->get('division_id'))
        ->and($serviceRequest->status->id)
        ->toEqual($request->get('status_id'))
        ->and($serviceRequest->priority->id)
        ->toEqual($request->get('priority_id'));
});

test('CreateServiceRequest is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $team = Team::factory()->create();

    $user->teams()->attach($team);

    $user->refresh();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.create');

    livewire(CreateServiceRequest::class)
        ->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managers()->attach($team);

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateServiceRequestRequestFactory::new()->create([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ]));

    livewire(CreateServiceRequest::class)
        ->fillForm($request->toArray())
        ->fillForm([
            'respondent_id' => Contact::factory()->create()->getKey(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequest::all());

    assertDatabaseHas(ServiceRequest::class, $request->except(['division_id', 'respondent_id', 'type_id'])->toArray());

    $serviceRequest = ServiceRequest::first();

    expect($serviceRequest->division->id)->toEqual($request['division_id']);
});

test('cannot create service requests if user is manager of any service request type', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.create');

    $team = Team::factory()->create();

    $user->teams()->attach($team);

    $user->refresh();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    livewire(CreateServiceRequest::class)
        ->assertForbidden();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('create')
        )->assertForbidden();
});

test('displays only service request types managed by the current user', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $team = Team::factory()->create();

    $user->teams()->attach($team);

    $user->refresh();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.create');

    actingAs($user);

    $serviceRequestTypesWithManagers = ServiceRequestType::factory()
        ->hasAttached($team, [], 'managers')
        ->create();

    $serviceRequestTypesWithoutManagers = ServiceRequestType::factory()->create();

    livewire(CreateServiceRequest::class)
        ->assertFormFieldExists('type_id', function (Select $field) use ($serviceRequestTypesWithManagers, $serviceRequestTypesWithoutManagers): bool {
            $options = $field->getOptions();

            return in_array($serviceRequestTypesWithManagers->getKey(), array_keys($options)) &&
                ! in_array($serviceRequestTypesWithoutManagers->getKey(), array_keys($options));
        });
});

test('create service requests if user is manager of any service request type', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $team = Team::factory()->create();

    $user->teams()->attach($team);

    $user->refresh();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.create');

    actingAs($user);

    $serviceRequestTypesWithManager = ServiceRequestType::factory()->create();

    $serviceRequestTypesWithManager->managers()->attach($team);

    $serviceRequestTypesWithManager->save();

    $request = collect(CreateServiceRequestRequestFactory::new()->create([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestTypesWithManager->getKey(),
        ])->getKey(),
    ]));

    livewire(CreateServiceRequest::class)
        ->fillForm($request->toArray())
        ->fillForm([
            'respondent_id' => Contact::factory()->create()->getKey(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceRequest::all());

    assertDatabaseHas(ServiceRequest::class, $request->except(['division_id', 'respondent_id', 'type_id'])->toArray());

    $serviceRequest = ServiceRequest::first();

    expect($serviceRequest->division->id)->toEqual($request['division_id']);
});

test('validate service requests type if user is manager of any service request type', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $team = Team::factory()->create();

    $user->teams()->attach($team);

    $user->refresh();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.create');

    actingAs($user);

    $request = collect(CreateServiceRequestRequestFactory::new()->create());

    $serviceRequestTypesWithoutManagers = ServiceRequestType::factory()->count(2)->create();

    $serviceRequestTypesWithManagers = ServiceRequestType::factory()->count(2)->create();

    $serviceRequestTypesWithManagers->each(function ($serviceRequest) use ($team) {
        $serviceRequest->managers()->attach($team);
    });

    $serviceRequestTypes = $serviceRequestTypesWithoutManagers->toBase()->merge($serviceRequestTypesWithManagers);

    livewire(CreateServiceRequest::class)
        ->fillForm($request->toArray())
        ->fillForm([
            'respondent_id' => Contact::factory()->create()->getKey(),
            'priority_id' => ServiceRequestPriority::factory()->create([
                'type_id' => $serviceRequestTypes->first()->getKey(),
            ])->getKey(),
        ])
        ->call('create')
        ->assertHasFormErrors(['type_id']);
});

test('assignment type individual manager will auto assign to new service requests', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.create');

    $team = Team::factory()->create();

    $user->teams()->attach($team);

    $user->refresh();

    actingAs($user);

    $serviceRequestTypesWithManager = ServiceRequestType::factory()
        ->hasAttached(
            factory: $team,
            relationship: 'managers'
        )
        ->state([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Individual,
            'assignment_type_individual_id' => $user->getKey(),
        ])
        ->create();

    $request = collect(CreateServiceRequestRequestFactory::new()->create([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestTypesWithManager->getKey(),
        ])->getKey(),
    ]));

    livewire(CreateServiceRequest::class)
        ->fillForm($request->toArray())
        ->fillForm([
            'respondent_id' => Contact::factory()->create()->getKey(),
        ])
        ->call('create');

    $serviceRequest = ServiceRequest::first();

    expect($serviceRequest->assignments()->first())->user->id->toBe($user->getKey());
});

test('assignment type round robin will auto-assign to new service requests', function () {
    asSuperAdmin();
    $factoryUsers = User::factory()->licensed(LicenseType::cases())->count(3)->create();
    $team = Team::factory()
        ->hasAttached($factoryUsers, [], 'users')->create();

    $serviceRequestTypeWithManager = ServiceRequestType::factory()
        ->hasAttached(
            factory: $team,
            relationship: 'managers'
        )
        ->state([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::RoundRobin,
        ])
        ->create();

    $request = collect(CreateServiceRequestRequestFactory::new()->create([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestTypeWithManager->getKey(),
        ])->getKey(),
    ]));

    $users = $team->users()->orderBy('name')->orderBy('id')->get();

    foreach ($users as $user) {
        livewire(CreateServiceRequest::class)
            ->fillForm($request->toArray())
            ->fillForm([
                'respondent_id' => Contact::factory()->create()->getKey(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $latestServiceRequest = ServiceRequest::latest()->first();
        $getServiceRequestType = ServiceRequestType::where('assignment_type', 'round-robin')->first();
        expect($getServiceRequestType->assignment_type)->toBe(ServiceRequestTypeAssignmentTypes::RoundRobin);
        expect($getServiceRequestType->fresh()->last_assigned_id)->ToBe($user->getKey());
        expect($latestServiceRequest->assignedTo->user_id)->ToBe($user->getKey());
    }

    livewire(CreateServiceRequest::class)
        ->fillForm($request->toArray())
        ->fillForm([
            'respondent_id' => Contact::factory()->create()->getKey(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $latestServiceRequest = ServiceRequest::latest()->first();
    $getServiceRequestType = ServiceRequestType::where('assignment_type', 'round-robin')->first();
    expect($getServiceRequestType->last_assigned_id)->ToBe($team->users()->orderBy('name')->orderBy('id')->first()->getKey());
    expect($latestServiceRequest->assignedTo->user_id)->ToBe($team->users()->orderBy('name')->orderBy('id')->first()->getKey());
});

test('assignment type workload will auto-assign to new service requests', function () {
    asSuperAdmin();
    $factoryUsers = User::factory()->licensed(LicenseType::cases())->count(5)->create();
    $team = Team::factory()
        ->hasAttached($factoryUsers, [], 'users')->create();

    $serviceRequestTypeWithManager = ServiceRequestType::factory()
        ->hasAttached(
            factory: $team,
            relationship: 'managers'
        )
        ->state([
            'assignment_type' => ServiceRequestTypeAssignmentTypes::Workload,
        ])
        ->create();

    foreach ($factoryUsers->take(-2) as $factoryUser) {
        $serviceRequest = ServiceRequest::factory()->create();
        $serviceRequest->assignments()->create([
            'user_id' => $factoryUser->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);

        $serviceRequest = ServiceRequest::factory()->create();
        $serviceRequest->assignments()->create([
            'user_id' => $factoryUser->getKey(),
            'assigned_at' => now(),
            'status' => ServiceRequestAssignmentStatus::Active,
        ]);
    }

    $request = collect(CreateServiceRequestRequestFactory::new()->create([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestTypeWithManager->getKey(),
        ])->getKey(),
    ]));

    foreach ($factoryUsers->take(3)->sortBy('id')->sortBy('name') as $user) {
        livewire(CreateServiceRequest::class)
            ->fillForm($request->toArray())
            ->fillForm([
                'respondent_id' => Contact::factory()->create()->getKey(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $latestServiceRequest = ServiceRequest::latest()->first();
        $getServiceRequestType = ServiceRequestType::where('assignment_type', 'workload')->first();
        expect($getServiceRequestType->assignment_type)->toBe(ServiceRequestTypeAssignmentTypes::Workload);
        expect($getServiceRequestType->fresh()->last_assigned_id)->ToBe($user->getKey());
        expect($latestServiceRequest->assignedTo->user_id)->ToBe($user->getKey());
    }

    livewire(CreateServiceRequest::class)
        ->fillForm($request->toArray())
        ->fillForm([
            'respondent_id' => Contact::factory()->create()->getKey(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $latestServiceRequest = ServiceRequest::latest()->first();
    $getServiceRequestType = ServiceRequestType::where('assignment_type', 'workload')->first();
    expect($getServiceRequestType->last_assigned_id)->ToBe($factoryUsers->take(3)->sortBy('id')->sortBy('name')->first()->getKey());
    expect($latestServiceRequest->assignedTo->user_id)->ToBe($factoryUsers->take(3)->sortBy('id')->sortBy('name')->first()->getKey());
});
