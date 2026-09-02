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

use App\Features\DesktopNotificationsFeature;
use App\Models\User;
use CanyonGBS\Common\BrowserNotifications\BrowserNotificationsManager;
use CanyonGBS\Common\BrowserNotifications\Filament\BrowserNotificationsPlugin;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    config()->set('webpush.vapid.subject', 'mailto:test@example.com');
    config()->set('webpush.vapid.public_key', 'test-public-key');
    config()->set('webpush.vapid.private_key', 'test-private-key');
});

it('registers browser notifications on the admin panel', function () {
    expect(Filament::getPanel('admin')->getPlugin('browser-notifications'))
        ->toBeInstanceOf(BrowserNotificationsPlugin::class);
});

it('uses the Aiding App favicon for desktop notifications', function () {
    expect(app(BrowserNotificationsManager::class)->resolveIcon())
        ->toEndWith('/images/default-favicon.png');
});

it('does not show the browser notification prompt', function () {
    actingAs(User::factory()->create());

    Filament::setCurrentPanel('admin');
    Filament::bootCurrentPanel();

    expect(FilamentView::renderHook(PanelsRenderHook::BODY_END)->toHtml())
        ->not->toContain('x-data="browserNotificationsPrompt"');
});

it('does not show the browser notification prompt before the tenant migration activates the feature', function () {
    DesktopNotificationsFeature::deactivate();

    actingAs(User::factory()->create());

    Filament::setCurrentPanel('admin');
    Filament::bootCurrentPanel();

    expect(FilamentView::renderHook(PanelsRenderHook::BODY_END)->toHtml())
        ->not->toContain('x-data="browserNotificationsPrompt"');
});
