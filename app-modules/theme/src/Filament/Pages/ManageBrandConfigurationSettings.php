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

namespace AidingApp\Theme\Filament\Pages;

use AidingApp\Theme\Settings\ThemeSettings;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\SettingsPage;

class ManageBrandConfigurationSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationLabel = 'Branding';

    protected static ?int $navigationSort = 30;

    protected static string $settings = ThemeSettings::class;

    protected static ?string $title = 'Branding';

    protected static ?string $navigationGroup = 'Global Administration';

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
                Section::make('Favicon')
                    ->aside()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('favicon')
                            ->disk('s3')
                            ->collection('favicon')
                            ->visibility('private')
                            ->image()
                            ->model(
                                ThemeSettings::getSettingsPropertyModel('theme.is_favicon_active'),
                            )
                            ->afterStateUpdated(fn (Set $set) => $set('is_favicon_active', true))
                            ->deleteUploadedFileUsing(fn (Set $set) => $set('is_favicon_active', false))
                            ->hiddenLabel(),
                        Toggle::make('is_favicon_active')
                            ->label('Active')
                            ->hidden(fn (Get $get): bool => blank($get('favicon'))),
                    ]),
                Section::make('Logo')
                    ->aside()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->disk('s3')
                            ->collection('logo')
                            ->visibility('private')
                            ->image()
                            ->model(
                                ThemeSettings::getSettingsPropertyModel('theme.is_logo_active'),
                            )
                            ->afterStateUpdated(fn (Set $set) => $set('is_logo_active', true))
                            ->deleteUploadedFileUsing(fn (Set $set) => $set('is_logo_active', false))
                            ->hiddenLabel(),
                        SpatieMediaLibraryFileUpload::make('dark_logo')
                            ->disk('s3')
                            ->collection('dark_logo')
                            ->visibility('private')
                            ->image()
                            ->model(
                                ThemeSettings::getSettingsPropertyModel('theme.is_logo_active'),
                            )
                            ->hidden(fn (Get $get): bool => blank($get('logo'))),
                        Toggle::make('is_logo_active')
                            ->label('Active')
                            ->hidden(fn (Get $get): bool => blank($get('logo'))),
                    ]),
            ]);
    }

    public function getRedirectUrl(): ?string
    {
        // After saving, redirect to the current page to refresh
        // the logo preview in the layout.
        return ManageBrandConfigurationSettings::getUrl();
    }
}
