<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Filament\Plugins;

use Filament\Contracts\Plugin;
use Filament\Panel;

/**
 * A no-op stand-in for the emuniq browser-notifications plugin.
 *
 * The package registers its plugin on every Filament panel through
 * `Panel::configureUsing()`, guarding only against a panel that already has a
 * plugin with the id `browser-notifications`. Registering this null plugin on a
 * panel therefore suppresses the real one, which is required for panels that
 * run without a current tenant (e.g. the landlord panel) where push
 * subscriptions cannot be resolved.
 */
final class NullBrowserNotificationsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'browser-notifications';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return new static();
    }
}
