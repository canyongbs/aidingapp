<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\EditServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Notifications\SendClosedServiceFeedbackNotification;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\EditServiceRequestRequestFactory;
use AidingApp\Team\Models\Team;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\freezeTime;
use function Pest\Laravel\travel;
use function Pest\Laravel\travelBack;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('A successful action on the EditServiceRequest page', function () {
    $serviceRequest = ServiceRequest::factory([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->id,
    ])->create();

    asSuperAdmin()
        ->get(
            ServiceRequestResource::getUrl('edit', [
                'record' => $serviceRequest->getRouteKey(),
            ])
        )
        ->assertSuccessful();

    $request = collect(EditServiceRequestRequestFactory::new([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::InProgress,
        ])->id,
    ])->create());

    livewire(EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(
        ServiceRequest::class,
        $request->except(
            [
                'division_id',
                'status_id',
                'priority_id',
            ]
        )->toArray()
    );

    $serviceRequest->refresh();

    expect($serviceRequest->division->id)
        ->toEqual($request->get('division_id'))
        ->and($serviceRequest->status->id)
        ->toEqual($request->get('status_id'))
        ->and($serviceRequest->priority->id)
        ->toEqual($request->get('priority_id'));
});

test('check if time to resolution has correct value when status is changed', function () {
    travel(-10)->seconds();

    $serviceRequest = ServiceRequest::factory([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->id,
    ])->create();

    asSuperAdmin();

    travelBack();

    freezeTime(function () use ($serviceRequest) {
        $request = collect(EditServiceRequestRequestFactory::new([
            'status_id' => ServiceRequestStatus::factory()->create([
                'classification' => SystemServiceRequestClassification::Closed,
            ])->id,
        ])->create());

        livewire(EditServiceRequest::class, [
            'record' => $serviceRequest->getRouteKey(),
        ])
            ->fillForm($request->toArray())
            ->call('save')
            ->assertHasNoFormErrors();

        $serviceRequest->refresh();

        $createdTime = $serviceRequest->created_at;
        $updatedTime = $serviceRequest->updated_at;

        // Calculate the difference in seconds
        $secondsDifference = ($createdTime && $updatedTime) ? round($createdTime->diffInSeconds(now())) : null;

        expect($serviceRequest->time_to_resolution)
            ->toEqual($secondsDifference);
    });
});

test('EditServiceRequest requires valid data', function ($data, $errors) {
    $serviceRequest = ServiceRequest::factory([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->id,
    ])->create();

    asSuperAdmin();

    $request = collect(EditServiceRequestRequestFactory::new($data)->create());

    livewire(EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseHas(ServiceRequest::class, $serviceRequest->withoutRelations()->toArray());

    expect($serviceRequest->fresh()->division->id)
        ->toEqual($serviceRequest->division->id)
        ->and($serviceRequest->fresh()->status->id)
        ->toEqual($serviceRequest->status->id)
        ->and($serviceRequest->fresh()->priority->id)
        ->toEqual($serviceRequest->priority->id);
})->with(
    [
        'division_id missing' => [EditServiceRequestRequestFactory::new()->state(['division_id' => null]), ['division_id' => 'required']],
        'division_id does not exist' => [
            EditServiceRequestRequestFactory::new()->state(['division_id' => fake()->uuid()]),
            ['division_id' => 'exists'],
        ],
        'status_id missing' => [EditServiceRequestRequestFactory::new()->state(['status_id' => null]), ['status_id' => 'required']],
        'status_id does not exist' => [
            EditServiceRequestRequestFactory::new()->state(['status_id' => fake()->uuid()]),
            ['status_id' => 'exists'],
        ],
        'priority_id missing' => [EditServiceRequestRequestFactory::new()->state(['priority_id' => null]), ['priority_id' => 'required']],
        'priority_id does not exist' => [
            EditServiceRequestRequestFactory::new()->state(['priority_id' => fake()->uuid()]),
            ['priority_id' => 'exists'],
        ],
        'close_details is not a string' => [EditServiceRequestRequestFactory::new()->state(['close_details' => 1]), ['close_details' => 'string']],
        'res_details is not a string' => [EditServiceRequestRequestFactory::new()->state(['res_details' => 1]), ['res_details' => 'string']],
    ]
);

// Permission Tests

test('EditServiceRequest is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $team = Team::factory()->create();

    $user->team()->associate($team)->save();

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managers()->attach($team);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('edit', [
                'record' => $serviceRequest,
            ])
        )->assertForbidden();

    livewire(EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.update');

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('edit', [
                'record' => $serviceRequest,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestRequestFactory::new([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->id,
    ])->create());

    livewire(EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(
        ServiceRequest::class,
        $request->except(
            [
                'division_id',
                'status_id',
                'priority',
            ]
        )->toArray()
    );

    $serviceRequest->refresh();

    expect($serviceRequest->division->id)
        ->toEqual($request->get('division_id'))
        ->and($serviceRequest->status->id)
        ->toEqual($request->get('status_id'))
        ->and($serviceRequest->priority->id)
        ->toEqual($request->get('priority_id'));
});

test('EditServiceRequest is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $team = Team::factory()->create();

    $user->team()->associate($team)->save();

    $user->refresh();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.update');

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managers()->attach($team);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('edit', [
                'record' => $serviceRequest,
            ])
        )->assertForbidden();

    livewire(EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('edit', [
                'record' => $serviceRequest,
            ])
        )->assertSuccessful();

    $request = collect(EditServiceRequestRequestFactory::new()->create());

    livewire(EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($serviceRequest->fresh()->only($request->except('division_id')->keys()->toArray()))
        ->toEqual($request->except('division_id')->toArray())
        ->and($serviceRequest->fresh()->division->id)->toEqual($request['division_id']);
});

test('send feedback email if service request is closed', function () {
    Notification::fake();

    $settings = app(LicenseSettings::class);

    $settings->data->addons->feedbackManagement = true;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $team = Team::factory()->create();

    $user->team()->associate($team)->save();

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create([
        'has_enabled_feedback_collection' => true,
        'has_enabled_csat' => true,
        'has_enabled_nps' => true,
        'is_customers_survey_response_email_enabled' => true,
    ]);

    $serviceRequestType->managers()->attach($team);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    $request = collect(EditServiceRequestRequestFactory::new([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create());

    livewire(EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(
        ServiceRequest::class,
        $request->except(
            [
                'division_id',
                'status_id',
                'priority',
            ]
        )->toArray()
    );

    $serviceRequest->refresh();

    Notification::assertSentTo(
        $serviceRequest->respondent,
        SendClosedServiceFeedbackNotification::class
    );

    Notification::assertNotSentTo(
        [$user],
        SendClosedServiceFeedbackNotification::class
    );
});

test('service requests not authorized if user is not manager of the service request type', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.update');

    $team = Team::factory()->create();

    $user->team()->associate($team)->save();

    $user->refresh();

    actingAs($user);

    $serviceRequest = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->for(ServiceRequestType::factory()
            ->hasAttached(Team::factory(), [], 'managers'), 'type'),
    ])
        ->create();

    livewire(EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])->assertForbidden();
});

test('service requests not authorized if user is auditor of the service request type', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.update');

    $team = Team::factory()->create();

    $user->team()->associate($team)->save();

    $user->refresh();

    actingAs($user);

    $serviceRequest = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->for(ServiceRequestType::factory()
            ->hasAttached(Team::factory(), [], 'auditors'), 'type'),
    ])
        ->create();

    livewire(EditServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])->assertForbidden();
});
