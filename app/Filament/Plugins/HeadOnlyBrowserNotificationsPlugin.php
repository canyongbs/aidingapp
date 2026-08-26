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

use Closure;
use Emuniq\FilamentBrowserNotifications\BrowserNotificationsPlugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;

/**
 * Subscribing is handled entirely by the Notifications profile page, so this
 * skips the vendor's own floating prompt banner and keeps only the VAPID meta
 * tag it needs to inject.
 */
final class HeadOnlyBrowserNotificationsPlugin extends BrowserNotificationsPlugin
{
    public function register(Panel $panel): void
    {
        // BrowserNotificationsServiceProvider auto-registers its own plugin
        // instance via Panel::configureUsing() before this plugin runs (Filament
        // resolves the panel via Panel::make() first), which already queued its
        // own head meta tag and the prompt banner onto this same panel. Render
        // hooks can only be appended, never removed, so both are cleared here
        // before this plugin adds its own head meta tag hook.
        Closure::bind(function () {
            unset(
                $this->renderHooks[PanelsRenderHook::HEAD_END][''],
                $this->renderHooks[PanelsRenderHook::BODY_END][''],
            );
        }, $panel, Panel::class)();

        $panel->renderHook(
            PanelsRenderHook::HEAD_END,
            fn () => $this->renderVapidMeta(),
        );
    }
}
