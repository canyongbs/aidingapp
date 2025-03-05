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
use AidingApp\Notification\Listeners\NotifyUserOfSubscriptionCreated;
use AidingApp\Notification\Listeners\NotifyUserOfSubscriptionDeleted;
use AidingApp\Notification\Models\DatabaseMessage;
use AidingApp\Notification\Models\EmailMessage;
use AidingApp\Notification\Models\EmailMessageEvent;
use AidingApp\Notification\Models\OutboundDeliverable;
use AidingApp\Notification\Models\SmsMessage;
use AidingApp\Notification\Models\SmsMessageEvent;
use AidingApp\Notification\Models\Subscription;
use AidingApp\Notification\Notifications\ChannelManager;
use AidingApp\Notification\Notifications\Channels\EmailChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\Notification\Notifications\Channels\SmsChannel;
use App\Concerns\ImplementsGraphQL;
use App\Features\NewMessageModels;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\ChannelManager as BaseChannelManager;
use Illuminate\Notifications\Channels\DatabaseChannel as BaseDatabaseChannel;
use Illuminate\Notifications\Channels\MailChannel as BaseMailChannel;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    use ImplementsGraphQL;

    public function register(): void
    {
        $this->app->bind(BaseMailChannel::class, fn (Container $app) => NewMessageModels::active() ? MailChannel::class : EmailChannel::class);
        $this->app->bind(BaseDatabaseChannel::class, DatabaseChannel::class);
        $this->app->singleton(BaseChannelManager::class, fn (Container $app) => (new ChannelManager($app))
            ->extend('sms', fn (): SmsChannel => $this->app->make(SmsChannel::class)));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'subscription' => Subscription::class,
            'outbound_deliverable' => OutboundDeliverable::class,
            'email_message' => EmailMessage::class,
            'email_message_event' => EmailMessageEvent::class,
            'sms_message' => SmsMessage::class,
            'sms_message_event' => SmsMessageEvent::class,
            'database_message' => DatabaseMessage::class,
        ]);

        $this->registerEvents();

        $this->discoverSchema(__DIR__ . '/../../graphql/subscription.graphql');
    }

    protected function registerEvents(): void
    {
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
