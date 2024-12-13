<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;
use Filament\Forms\Form;
use App\Models\Authenticatable;
use Filament\Pages\SettingsPage;
use AidingApp\Ai\Settings\AiSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use App\Filament\Clusters\ProductIntegrations;

class ManageAiSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = AiSettings::class;

    protected static ?string $title = 'Cognitive Services Settings';

    protected static ?string $navigationLabel = 'Cognitive Services';

    protected static ?int $navigationSort = 100;

    protected static ?string $cluster = ProductIntegrations::class;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->hasRole(Authenticatable::SUPER_ADMIN_ROLE);
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('url')
                            ->label('URL')
                            ->string()
                            ->url(),
                        TextInput::make('key')
                            ->string()
                            ->password()
                            ->revealable(),
                        TextInput::make('api_version')
                            ->label('API Version')
                            ->string(),
                        TextInput::make('model')
                            ->string(),
                    ]),
            ]);
    }
}
