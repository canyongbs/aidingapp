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

namespace AidingApp\Assistant\Filament\Pages;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\IntegrationAI\Settings\AISettings;
use App\Filament\Clusters\ArtificialIntelligence;

class ManageAiSettings extends SettingsPage
{
    protected static string $settings = AISettings::class;

    protected static ?string $title = 'Manage AI Settings';

    protected static ?string $cluster = ArtificialIntelligence::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->hasLicense(LicenseType::ConversationalAi)) {
            return false;
        }

        return $user->can(['assistant.access_ai_settings']);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('prompt_system_context')
                    ->label('Base Prompt')
                    ->required()
                    ->string()
                    ->rows(12)
                    ->columnSpan('full'),
                TextInput::make('max_tokens')
                    ->label('Max Tokens')
                    ->required()
                    ->numeric()
                    ->columnSpan('1/2'),
                TextInput::make('temperature')
                    ->label('Temperature')
                    ->required()
                    ->numeric()
                    ->inputMode('decimal')
                    ->step(0.1)
                    ->minValue(0.0)
                    ->maxValue(2.0)
                    ->columnSpan('1/2'),
            ]);
    }
}
