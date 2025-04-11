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

namespace AidingApp\Authorization\Filament\Pages\Auth;

use AidingApp\Authorization\Settings\AzureSsoSettings;
use AidingApp\Authorization\Settings\GoogleSsoSettings;
use Filament\Actions\Action;
use Filament\Pages\Auth\Login as FilamentLogin;

class Login extends FilamentLogin
{
    protected static string $view = 'authorization::login';

    protected static string $layout = 'filament-panels::components.layouts.login';

    /**
     * @return array<Action>
     */
    protected function getSsoFormActions(): array
    {
        $ssoActions = [];

        $azureSsoSettings = app(AzureSsoSettings::class);

        if ($azureSsoSettings->is_enabled && ! empty($azureSsoSettings->client_id)) {
            $ssoActions[] = Action::make('azure_sso')
                ->label(__('Microsoft'))
                ->url(route('socialite.redirect', ['provider' => 'azure']))
                ->color('gray')
                ->icon('icon-microsoft')
                ->size('sm');
        }

        $googleSsoSettings = app(GoogleSsoSettings::class);

        if ($googleSsoSettings->is_enabled && ! empty($googleSsoSettings->client_id)) {
            $ssoActions[] = Action::make('google_sso')
                ->label(__('Google'))
                ->url(route('socialite.redirect', ['provider' => 'google']))
                ->icon('icon-google')
                ->color('gray')
                ->size('sm');
        }

        return $ssoActions;
    }
}
