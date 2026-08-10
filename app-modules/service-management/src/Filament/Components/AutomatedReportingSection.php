<?php

namespace AidingApp\ServiceManagement\Filament\Components;

use AidingApp\ServiceManagement\Enums\ServiceMonitoringReportFrequency;
use App\Features\ServiceMonitoringReportFeature;
use App\Filament\Forms\Components\UserSelect;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class AutomatedReportingSection
{
    public static function make(bool $isCreatePage = false): Section
    {
        return Section::make('Automated Reporting')
            ->schema([
                Toggle::make('is_reporting_active')
                    ->label('Activate Reporting')
                    ->default($isCreatePage ? false : null)
                    ->live()
                    ->columnSpanFull(),
                Radio::make('report_frequency')
                    ->label('Frequency')
                    ->options(ServiceMonitoringReportFrequency::class)
                    ->enum(ServiceMonitoringReportFrequency::class)
                    ->required(fn (Get $get) => $get('is_reporting_active'))
                    ->visible(fn (Get $get) => $get('is_reporting_active')),
                Hidden::make('is_reported_via_email')
                    ->default($isCreatePage ? false : null),
                Hidden::make('is_reported_via_database')
                    ->default($isCreatePage ? false : null),
                ReportChannelCheckboxList::make($isCreatePage),
                Section::make('Recipients')
                    ->schema([
                        UserSelect::make('report_users')
                            ->relationship('reportUsers')
                            ->label('Users')
                            ->multiple()
                            ->preload(),
                        Select::make('report_departments')
                            ->relationship('reportDepartments', 'name')
                            ->label('Departments')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        Select::make('report_contacts')
                            ->relationship('reportContacts', 'full_name')
                            ->label('Contacts')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(3)
                    ->visible(fn (Get $get) => $get('is_reporting_active')),
            ])
            ->visible(fn () => ServiceMonitoringReportFeature::active())
            ->columns(2);
    }
}
