<?php

namespace AidingApp\Ai\Filament\Pages;

use App\Models\User;
use Filament\Forms\Form;
use App\Models\Authenticatable;
use Filament\Pages\SettingsPage;
use App\Features\AiSettingsFeature;
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

        return AiSettingsFeature::active() && $user->hasRole(Authenticatable::SUPER_ADMIN_ROLE) && parent::canAccess();
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
