<?php

namespace App\Filament\Pages;

use App\Settings\DisplaySettings;
use CanyonGBS\Common\Filament\Forms\Components\TimezoneSelect;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * @property Schema $form
 */
class Timezone extends ProfilePage
{
    protected static ?string $slug = 'timezone';

    protected static ?string $title = 'Timezone';

    protected static ?int $navigationSort = 30;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Timezone')
                    ->description('Update your timezone.')
                    ->schema([
                        TimezoneSelect::make('timezone')
                            ->required()
                            ->selectablePlaceholder(false)
                            ->helperText(function (): string {
                                $timezone = config('app.timezone');

                                if (
                                    filled($displaySettingsTimezone = app(DisplaySettings::class)->timezone)
                                ) {
                                    $timezone = $displaySettingsTimezone;
                                }

                                return "Default: {$timezone}";
                            }),
                    ]),
            ]);
    }
}
