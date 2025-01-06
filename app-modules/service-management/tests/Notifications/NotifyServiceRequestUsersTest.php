<?php

use AidingApp\Notification\Notifications\Channels\EmailChannel;
use AidingApp\ServiceManagement\Actions\NotifyServiceRequestUsers;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Notifications\ServiceRequestCreated;
use AidingApp\Team\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

it('can notify a user if they belong to a team managing a service request type', function () {
    Notification::fake();

    $users = User::factory(3)
        ->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($serviceRequestType, [], 'manageableServiceRequestTypes')
        ->hasAttached($users->take(2))
        ->create();

    $serviceRequestPriority = ServiceRequestPriority::factory()
        ->for($serviceRequestType, 'type')
        ->create();

    $anotherServiceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($anotherServiceRequestType, [], 'manageableServiceRequestTypes')
        ->hasAttached($users->last())
        ->create();

    ServiceRequestPriority::factory()
        ->for($anotherServiceRequestType, 'type')
        ->create();

    $serviceRequest = ServiceRequest::factory()
        ->for($serviceRequestPriority, 'priority')
        ->create();

    app(NotifyServiceRequestUsers::class)->execute(
        $serviceRequest,
        app(ServiceRequestCreated::class, ['serviceRequest' => $serviceRequest, 'channel' => EmailChannel::class]),
        true,
        false,
    );

    Notification::assertSentTo($users->get(0), ServiceRequestCreated::class);
    Notification::assertSentTo($users->get(1), ServiceRequestCreated::class);
    Notification::assertNotSentTo($users->get(2), ServiceRequestCreated::class);
});

it('can notify a user if they belong to a team auditing a service request type', function () {
    Notification::fake();

    $users = User::factory(3)
        ->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($serviceRequestType, [], 'auditableServiceRequestTypes')
        ->hasAttached($users->take(2))
        ->create();

    $serviceRequestPriority = ServiceRequestPriority::factory()
        ->for($serviceRequestType, 'type')
        ->create();

    $anotherServiceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($anotherServiceRequestType, [], 'auditableServiceRequestTypes')
        ->hasAttached($users->last())
        ->create();

    ServiceRequestPriority::factory()
        ->for($anotherServiceRequestType, 'type')
        ->create();

    $serviceRequest = ServiceRequest::factory()
        ->for($serviceRequestPriority, 'priority')
        ->create();

    app(NotifyServiceRequestUsers::class)->execute(
        $serviceRequest,
        app(ServiceRequestCreated::class, ['serviceRequest' => $serviceRequest, 'channel' => EmailChannel::class]),
        false,
        true,
    );

    Notification::assertSentTo($users->get(0), ServiceRequestCreated::class);
    Notification::assertSentTo($users->get(1), ServiceRequestCreated::class);
    Notification::assertNotSentTo($users->get(2), ServiceRequestCreated::class);
});

it('does not notify a user if they belong to a team managing a service request type but teams do not recieve notifications', function () {
    Notification::fake();

    $users = User::factory(3)
        ->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($serviceRequestType, [], 'manageableServiceRequestTypes')
        ->hasAttached($users->take(2))
        ->create();

    $serviceRequestPriority = ServiceRequestPriority::factory()
        ->for($serviceRequestType, 'type')
        ->create();

    $anotherServiceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($anotherServiceRequestType, [], 'manageableServiceRequestTypes')
        ->hasAttached($users->last())
        ->create();

    ServiceRequestPriority::factory()
        ->for($anotherServiceRequestType, 'type')
        ->create();

    $serviceRequest = ServiceRequest::factory()
        ->for($serviceRequestPriority, 'priority')
        ->create();

    app(NotifyServiceRequestUsers::class)->execute(
        $serviceRequest,
        app(ServiceRequestCreated::class, ['serviceRequest' => $serviceRequest, 'channel' => EmailChannel::class]),
        false,
        false,
    );

    Notification::assertNotSentTo($users->get(0), ServiceRequestCreated::class);
    Notification::assertNotSentTo($users->get(1), ServiceRequestCreated::class);
    Notification::assertNotSentTo($users->get(2), ServiceRequestCreated::class);
});

it('does not notify a user if they belong to a team auditing a service request type but teams do not recieve notifications', function () {
    Notification::fake();

    $users = User::factory(3)
        ->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($serviceRequestType, [], 'auditableServiceRequestTypes')
        ->hasAttached($users->take(2))
        ->create();

    $serviceRequestPriority = ServiceRequestPriority::factory()
        ->for($serviceRequestType, 'type')
        ->create();

    $anotherServiceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($anotherServiceRequestType, [], 'auditableServiceRequestTypes')
        ->hasAttached($users->last())
        ->create();

    ServiceRequestPriority::factory()
        ->for($anotherServiceRequestType, 'type')
        ->create();

    $serviceRequest = ServiceRequest::factory()
        ->for($serviceRequestPriority, 'priority')
        ->create();

    app(NotifyServiceRequestUsers::class)->execute(
        $serviceRequest,
        app(ServiceRequestCreated::class, ['serviceRequest' => $serviceRequest, 'channel' => EmailChannel::class]),
        false,
        false,
    );

    Notification::assertNotSentTo($users->get(0), ServiceRequestCreated::class);
    Notification::assertNotSentTo($users->get(1), ServiceRequestCreated::class);
    Notification::assertNotSentTo($users->get(2), ServiceRequestCreated::class);
});

it('does not notify a user twice if they belong to a team managing and auditing a service request type', function () {
    Notification::fake();

    $users = User::factory(3)
        ->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($serviceRequestType, [], 'manageableServiceRequestTypes')
        ->hasAttached($serviceRequestType, [], 'auditableServiceRequestTypes')
        ->hasAttached($users->take(2))
        ->create();

    $serviceRequestPriority = ServiceRequestPriority::factory()
        ->for($serviceRequestType, 'type')
        ->create();

    $anotherServiceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($anotherServiceRequestType, [], 'manageableServiceRequestTypes')
        ->hasAttached($anotherServiceRequestType, [], 'auditableServiceRequestTypes')
        ->hasAttached($users->last())
        ->create();

    ServiceRequestPriority::factory()
        ->for($anotherServiceRequestType, 'type')
        ->create();

    $serviceRequest = ServiceRequest::factory()
        ->for($serviceRequestPriority, 'priority')
        ->create();

    app(NotifyServiceRequestUsers::class)->execute(
        $serviceRequest,
        app(ServiceRequestCreated::class, ['serviceRequest' => $serviceRequest, 'channel' => EmailChannel::class]),
        true,
        true,
    );

    Notification::assertSentToTimes($users->get(0), ServiceRequestCreated::class, 1);
    Notification::assertSentToTimes($users->get(1), ServiceRequestCreated::class, 1);
    Notification::assertNotSentTo($users->get(2), ServiceRequestCreated::class);
});
