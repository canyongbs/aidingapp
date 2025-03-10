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

namespace AidingApp\Notification\Providers;

use AidingApp\Notification\Events\SubscriptionCreated;
use AidingApp\Notification\Events\SubscriptionDeleted;
use AidingApp\Notification\Events\TriggeredAutoSubscription;
use AidingApp\Notification\Listeners\CreateAutoSubscription;
use AidingApp\Notification\Listeners\HandleNotificationFailed;
use AidingApp\Notification\Listeners\HandleNotificationSent;
use AidingApp\Notification\Listeners\NotifyUserOfSubscriptionCreated;
use AidingApp\Notification\Listeners\NotifyUserOfSubscriptionDeleted;
use AidingApp\Notification\Models\OutboundDeliverable;
use AidingApp\Notification\Models\Subscription;
use App\Concerns\ImplementsGraphQL;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    use ImplementsGraphQL;

    public function register(): void {}

    public function boot(): void
    {
        Relation::morphMap([
            'subscription' => Subscription::class,
            'outbound_deliverable' => OutboundDeliverable::class,
        ]);

        $this->registerEvents();

        $this->discoverSchema(__DIR__ . '/../../graphql/subscription.graphql');
    }

    protected function registerEvents(): void
    {
        Event::listen(
            NotificationSent::class,
            HandleNotificationSent::class
        );

        Event::listen(
            NotificationFailed::class,
            HandleNotificationFailed::class
        );

        // TODO Listen to the MessageSent event in order to update email statuses
        // as best as we can without the SES information...

        // TODO Should subscriptions exist in their own module???
        Event::listen(
            SubscriptionCreated::class,
            NotifyUserOfSubscriptionCreated::class
        );

        Event::listen(
            SubscriptionDeleted::class,
            NotifyUserOfSubscriptionDeleted::class
        );

        Event::listen(
            TriggeredAutoSubscription::class,
            CreateAutoSubscription::class,
        );
    }
}
