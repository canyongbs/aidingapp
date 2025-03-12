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

namespace AidingApp\IntegrationAwsSesEventHandling\Providers;

use AidingApp\IntegrationAwsSesEventHandling\Events\SesBounceEvent;
use AidingApp\IntegrationAwsSesEventHandling\Events\SesClickEvent;
use AidingApp\IntegrationAwsSesEventHandling\Events\SesComplaintEvent;
use AidingApp\IntegrationAwsSesEventHandling\Events\SesDeliveryDelayEvent;
use AidingApp\IntegrationAwsSesEventHandling\Events\SesDeliveryEvent;
use AidingApp\IntegrationAwsSesEventHandling\Events\SesOpenEvent;
use AidingApp\IntegrationAwsSesEventHandling\Events\SesRejectEvent;
use AidingApp\IntegrationAwsSesEventHandling\Events\SesRenderingFailureEvent;
use AidingApp\IntegrationAwsSesEventHandling\Events\SesSendEvent;
use AidingApp\IntegrationAwsSesEventHandling\Events\SesSubscriptionEvent;
use AidingApp\IntegrationAwsSesEventHandling\IntegrationAwsSesEventHandlingPlugin;
use AidingApp\IntegrationAwsSesEventHandling\Listeners\EnsureSesConfigurationSetHeadersArePresent;
use AidingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesBounceEvent;
use AidingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesClickEvent;
use AidingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesComplaintEvent;
use AidingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesDeliveryDelayEvent;
use AidingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesDeliveryEvent;
use AidingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesOpenEvent;
use AidingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesRejectEvent;
use AidingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesRenderingFailureEvent;
use AidingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesSendEvent;
use AidingApp\IntegrationAwsSesEventHandling\Listeners\HandleSesSubscriptionEvent;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class IntegrationAwsSesEventHandlingServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => ($panel->getId() !== 'admin') || $panel->plugin(new IntegrationAwsSesEventHandlingPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([]);

        $this->registerEvents();
    }

    public function registerEvents(): void
    {
        Event::listen(
            MessageSending::class,
            EnsureSesConfigurationSetHeadersArePresent::class
        );

        Event::listen(
            SesBounceEvent::class,
            HandleSesBounceEvent::class
        );

        Event::listen(
            SesDeliveryEvent::class,
            HandleSesDeliveryEvent::class
        );

        Event::listen(
            SesDeliveryDelayEvent::class,
            HandleSesDeliveryDelayEvent::class
        );

        Event::listen(
            SesRejectEvent::class,
            HandleSesRejectEvent::class
        );

        Event::listen(
            SesRenderingFailureEvent::class,
            HandleSesRenderingFailureEvent::class
        );

        Event::listen(
            SesClickEvent::class,
            HandleSesClickEvent::class
        );

        Event::listen(
            SesComplaintEvent::class,
            HandleSesComplaintEvent::class
        );

        Event::listen(
            SesOpenEvent::class,
            HandleSesOpenEvent::class
        );

        Event::listen(
            SesSendEvent::class,
            HandleSesSendEvent::class
        );

        Event::listen(
            SesSubscriptionEvent::class,
            HandleSesSubscriptionEvent::class
        );
    }
}
