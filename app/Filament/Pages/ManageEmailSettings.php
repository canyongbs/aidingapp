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

namespace App\Filament\Pages;

use App\Filament\Clusters\DisplaySettings as DisplaySettingsCluster;
use App\Models\User;
use App\Settings\EmailSettings;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;

class ManageEmailSettings extends SettingsPage
{
    protected static ?string $navigationLabel = 'Email';

    protected static ?int $navigationSort = 20;

    protected static string $settings = EmailSettings::class;

    protected static ?string $cluster = DisplaySettingsCluster::class;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('settings.view-any');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SpatieMediaLibraryFileUpload::make('header_logo')
                    ->collection('header_logo')
                    ->disk('s3-public')
                    ->visibility('public')
                    ->image()
                    ->maxSize(10240)
                    ->acceptedFileTypes([
                        'image/png',
                        'image/jpeg',
                        'image/jpg',
                        'image/webp',
                        'image/svg+xml',
                    ])
                    ->model(
                        EmailSettings::getSettingsPropertyModel('email.header_logo'),
                    )
                    ->columnSpanFull(),
                ColorPicker::make('background_color')
                    ->regex('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b$/'),
                ColorPicker::make('h1_text_color')
                    ->regex('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b$/'),
                ColorPicker::make('h2_text_color')
                    ->regex('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b$/'),
                ColorPicker::make('paragraph_text_color')
                    ->regex('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b$/'),
                RichEditor::make('footer')
                    ->label('Footer text')
                    ->columnSpanFull()
                    ->toolbarButtons(['bold', 'italic', 'underline', 'link'])
                    ->json(),
            ])
            ->disabled(! auth()->user()->can('settings.*.update'));
    }

    public function save(): void
    {
        if (! auth()->user()->can('settings.*.update')) {
            return;
        }

        parent::save();
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getFormActions(): array
    {
        if (! auth()->user()->can('settings.*.update')) {
            return [];
        }

        return parent::getFormActions();
    }
}
