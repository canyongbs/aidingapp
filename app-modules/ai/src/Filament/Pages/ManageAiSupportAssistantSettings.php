<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\Ai\Filament\Pages;

use AidingApp\Ai\Settings\AiSupportAssistantSettings;
use AidingApp\Portal\Settings\PortalSettings;
use App\Features\AiFeatureTogglesFeature;
use App\Filament\Clusters\GlobalArtificialIntelligence;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ManageAiSupportAssistantSettings extends SettingsPage
{
    protected static string $settings = AiSupportAssistantSettings::class;

    protected static ?string $title = 'AI Support Assistant';

    protected static ?string $cluster = GlobalArtificialIntelligence::class;

    protected static ?int $navigationSort = 50;

    public static function canAccess(): bool
    {
        if (! AiFeatureTogglesFeature::active()) {
            return false;
        }

        $user = auth()->user();
        assert($user instanceof User);

        return $user->isSuperAdmin();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('AI Support Assistant Settings')
                    ->description('Configure the global AI Support Assistant for the portal. This is the master toggle — when disabled, the AI Support Assistant will not be available anywhere in the product, and the portal-level setting will be hidden.')
                    ->schema([
                        Toggle::make('is_enabled')
                            ->label('Enable AI Support Assistant')
                            ->helperText('When enabled, the AI Support Assistant can be activated in portal settings. When disabled, the assistant will be turned off globally and the portal-level setting will also be disabled.')
                            ->live(),
                        Callout::make('Disabling the AI Support Assistant globally will also disable it in the portal settings. Any portal that currently has the AI Support Assistant enabled will have it turned off.')
                            ->warning()
                            ->visible(fn (Get $get) => app(AiSupportAssistantSettings::class)->is_enabled && ! $get('is_enabled')),
                    ]),
            ])
            ->disabled(! auth()->user()->isSuperAdmin());
    }

    public function save(): void
    {
        if (! auth()->user()->isSuperAdmin()) {
            return;
        }

        $wasEnabled = app(AiSupportAssistantSettings::class)->is_enabled;

        parent::save();

        $isNowEnabled = app(AiSupportAssistantSettings::class)->is_enabled;

        if ($wasEnabled && ! $isNowEnabled) {
            $portalSettings = app(PortalSettings::class);

            if ($portalSettings->ai_support_assistant) {
                $portalSettings->ai_support_assistant = false;
                $portalSettings->save();

                Notification::make()
                    ->warning()
                    ->title('Portal AI Support Assistant Disabled')
                    ->body('The AI Support Assistant has been disabled in the portal settings as a result of globally disabling this feature.')
                    ->send();
            }
        }
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getFormActions(): array
    {
        if (! auth()->user()->isSuperAdmin()) {
            return [];
        }

        return parent::getFormActions();
    }
}
