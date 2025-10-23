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

namespace AidingApp\IntegrationAwsSesEventHandling\Filament\Pages;

use AidingApp\IntegrationAwsSesEventHandling\Settings\SesSettings;
use App\Features\ParagraphTextColorFeature;
use App\Filament\Clusters\ProductIntegrations;
use App\Models\Tenant;
use App\Models\User;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\SettingsPage;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;

use function Filament\Support\is_app_url;

use Illuminate\Support\Facades\DB;
use Throwable;

class ManageAmazonSesSettings extends SettingsPage
{
    protected static string $settings = SesSettings::class;

    protected static ?string $title = 'Amazon SES Settings';

    protected static ?string $navigationLabel = 'Amazon SES';

    protected static ?int $navigationSort = 50;

    protected static ?string $cluster = ProductIntegrations::class;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->isSuperAdmin() && parent::canAccess();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('isDemoModeEnabled')
                    ->label('Demo Mode')
                    ->live(),
                Checkbox::make('isExcludingSystemNotificationsFromDemoMode')
                    ->label('Exclude authentication related messages')
                    ->visible(fn (Get $get): bool => (bool) $get('isDemoModeEnabled')),
                Toggle::make('dynamic_engagements')
                    ->label('Dynamic Engagements')
                    ->visible(fn (Get $get): bool => ! $get('isDemoModeEnabled')),
                ColorPicker::make('paragraph_text_color')
                    ->label('Default Font Color')
                    ->regex('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b$/')
                    ->visible(ParagraphTextColorFeature::active()),
            ]);
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();
            DB::connection('landlord')->beginTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            /** @var Tenant $tenant */
            $tenant = Tenant::current();

            /** @var TenantConfig $config */
            $config = $tenant->config;

            if ($data['isDemoModeEnabled']) {
                $config->mail->isDemoModeEnabled = $data['isDemoModeEnabled'];
                $config->mail->isExcludingSystemNotificationsFromDemoMode = $data['isExcludingSystemNotificationsFromDemoMode'];
            } else {
                $config->mail->isDemoModeEnabled = $data['isDemoModeEnabled'];
            }

            $tenant->config = $config;

            $tenant->save();

            unset(
                $data['isDemoModeEnabled'],
            );

            $settings = app(static::getSettings());

            $settings->fill($data);
            $settings->save();

            $this->callHook('afterSave');

            $this->commitDatabaseTransaction();
            DB::connection('landlord')->commit();
        } catch (Halt $exception) {
            if ($exception->shouldRollbackDatabaseTransaction()) {
                $this->rollBackDatabaseTransaction();
                DB::connection('landlord')->rollBack();
            } else {
                $this->commitDatabaseTransaction();
                DB::connection('landlord')->commit();
            }

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();
            DB::connection('landlord')->rollBack();

            throw $exception;
        }

        $this->rememberData();

        $this->getSavedNotification()?->send();

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode() && is_app_url($redirectUrl));
        }
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $settings = app(static::getSettings());

        /** @var Tenant $tenant */
        $tenant = Tenant::current();

        /** @var TenantConfig $config */
        $config = $tenant->config;

        $data = $this->mutateFormDataBeforeFill(
            [
                ...$settings->toArray(),
                'isDemoModeEnabled' => $config->mail->isDemoModeEnabled ?? false,
                'isExcludingSystemNotificationsFromDemoMode' => $config->mail->isExcludingSystemNotificationsFromDemoMode ?? true,
            ]
        );

        $this->form->fill($data);

        $this->callHook('afterFill');
    }
}
