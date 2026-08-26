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

use App\Filament\Plugins\HeadOnlyBrowserNotificationsPlugin;
use App\Filament\Plugins\NullBrowserNotificationsPlugin;
use Filament\Facades\Filament;
use Filament\View\PanelsRenderHook;

it('suppresses the browser notifications plugin on the landlord panel', function () {
    expect(Filament::getPanel('landlord')->getPlugin('browser-notifications'))
        ->toBeInstanceOf(NullBrowserNotificationsPlugin::class);
});

it('keeps the browser notifications plugin active on the admin panel', function () {
    expect(Filament::getPanel('admin')->getPlugin('browser-notifications'))
        ->toBeInstanceOf(HeadOnlyBrowserNotificationsPlugin::class);
});

// Panel::make() runs the vendor's Panel::configureUsing() callback before our
// plugin ever runs, which already queues its meta tag and prompt banner onto
// the panel. Render hooks can only be appended, never removed, so the plugins
// above must actively clear what was queued rather than merely replacing the
// plugin instance in Filament's plugin registry.
it('clears the render hooks the vendor queued before the landlord panel plugin registers', function () {
    $renderHooks = (new ReflectionProperty(Filament::getPanel('landlord'), 'renderHooks'))
        ->getValue(Filament::getPanel('landlord'));

    expect($renderHooks[PanelsRenderHook::HEAD_END][''] ?? [])->toBeEmpty()
        ->and($renderHooks[PanelsRenderHook::BODY_END][''] ?? [])->toBeEmpty();
});
