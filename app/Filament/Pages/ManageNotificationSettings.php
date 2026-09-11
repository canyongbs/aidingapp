<?php

namespace App\Filament\Pages;

use App\Enums\CommunicationNavigationGroup;
use App\Features\NotificationSettingsFeature;
use App\Filament\Clusters\Communication;
use App\Models\User;
use App\Settings\NotificationSettings;
use CanyonGBS\Common\Filament\Forms\Components\ColorSelect;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageNotificationSettings extends SettingsPage
{
    protected static string $settings = NotificationSettings::class;

    protected static ?string $cluster = Communication::class;

    protected static string | UnitEnum | null $navigationGroup = CommunicationNavigationGroup::Communication;

    protected static string | null $navigationLabel = 'Notifications';

    protected static ?int $navigationSort = 110;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        assert($user instanceof User);

        return NotificationSettingsFeature::active() && $user->can('settings.view-any') && parent::canAccess();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->disabled(! auth()->user()->can('settings.*.update'))
            ->components([
                TextInput::make('from_name')
                    ->string()
                    ->maxLength(150)
                    ->autocomplete(false),
                ColorSelect::make('primary_color'),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->disk('s3-public')
                    ->collection('logo')
                    ->visibility('public')
                    ->model(NotificationSettings::getSettingsPropertyModel('notifications.logo'))
                    ->image()
                    ->maxSize(10240),
            ]);
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
