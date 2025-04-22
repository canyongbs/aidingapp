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

use AidingApp\Notification\Notifications\Channels\MailChannel;
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
        new ServiceRequestCreated($serviceRequest, MailChannel::class),
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
        new ServiceRequestCreated($serviceRequest, MailChannel::class),
        false,
        true,
    );

    Notification::assertSentTo($users->get(0), ServiceRequestCreated::class);
    Notification::assertSentTo($users->get(1), ServiceRequestCreated::class);
    Notification::assertNotSentTo($users->get(2), ServiceRequestCreated::class);
});

it('does not notify a user if they belong to a team managing a service request type but teams do not recieve notifications', function () {
    Notification::fake();

    $serviceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($serviceRequestType, [], 'manageableServiceRequestTypes')
        ->for(User::factory()->create())
        ->create();

    $serviceRequestPriority = ServiceRequestPriority::factory()
        ->for($serviceRequestType, 'type')
        ->create();

    $anotherServiceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($anotherServiceRequestType, [], 'manageableServiceRequestTypes')
        ->for(User::factory()->create())
        ->create();

    ServiceRequestPriority::factory()
        ->for($anotherServiceRequestType, 'type')
        ->create();

    $serviceRequest = ServiceRequest::factory()
        ->for($serviceRequestPriority, 'priority')
        ->create();

    app(NotifyServiceRequestUsers::class)->execute(
        $serviceRequest,
        new ServiceRequestCreated($serviceRequest, MailChannel::class),
        false,
        false,
    );

    Notification::assertNotSentTo(User::first(), ServiceRequestCreated::class);
    Notification::assertNotSentTo(User::skip(1)->first(), ServiceRequestCreated::class);
    Notification::assertNotSentTo(User::skip(2)->first(), ServiceRequestCreated::class);
});

it('does not notify a user if they belong to a team auditing a service request type but teams do not recieve notifications', function () {
    Notification::fake();

    $serviceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($serviceRequestType, [], 'auditableServiceRequestTypes')
        ->has(User::factory()->count(2))
        ->create();

    $serviceRequestPriority = ServiceRequestPriority::factory()
        ->for($serviceRequestType, 'type')
        ->create();

    $anotherServiceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($anotherServiceRequestType, [], 'auditableServiceRequestTypes')
        ->has(User::factory()->count(1))
        ->create();

    ServiceRequestPriority::factory()
        ->for($anotherServiceRequestType, 'type')
        ->create();

    $serviceRequest = ServiceRequest::factory()
        ->for($serviceRequestPriority, 'priority')
        ->create();

    app(NotifyServiceRequestUsers::class)->execute(
        $serviceRequest,
        new ServiceRequestCreated($serviceRequest, MailChannel::class),
        false,
        false,
    );

    Notification::assertNotSentTo(User::first(), ServiceRequestCreated::class);
    Notification::assertNotSentTo(User::skip(1)->first(), ServiceRequestCreated::class);
    Notification::assertNotSentTo(User::skip(2)->first(), ServiceRequestCreated::class);
});

it('does not notify a user twice if they belong to a team managing and auditing a service request type', function () {
    Notification::fake();

    $serviceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($serviceRequestType, [], 'manageableServiceRequestTypes')
        ->hasAttached($serviceRequestType, [], 'auditableServiceRequestTypes')
        ->has(User::factory()->count(2))
        ->create();

    $serviceRequestPriority = ServiceRequestPriority::factory()
        ->for($serviceRequestType, 'type')
        ->create();

    $anotherServiceRequestType = ServiceRequestType::factory()->create();

    Team::factory()
        ->hasAttached($anotherServiceRequestType, [], 'manageableServiceRequestTypes')
        ->hasAttached($anotherServiceRequestType, [], 'auditableServiceRequestTypes')
        ->has(User::factory())
        ->create();

    ServiceRequestPriority::factory()
        ->for($anotherServiceRequestType, 'type')
        ->create();

    $serviceRequest = ServiceRequest::factory()
        ->for($serviceRequestPriority, 'priority')
        ->create();

    app(NotifyServiceRequestUsers::class)->execute(
        $serviceRequest,
        new ServiceRequestCreated($serviceRequest, MailChannel::class),
        true,
        true,
    );

    Notification::assertSentToTimes(User::first(), ServiceRequestCreated::class, 1);
    Notification::assertSentToTimes(User::skip(1)->first(), ServiceRequestCreated::class, 1);
    Notification::assertNotSentTo(User::skip(2)->first(), ServiceRequestCreated::class);
});
