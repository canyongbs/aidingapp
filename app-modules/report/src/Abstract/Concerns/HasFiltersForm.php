<?php

namespace AidingApp\Report\Abstract\Concerns;

use AidingApp\Report\Abstract\ServiceRequestFeedbackReport;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm as ConcernsHasFiltersForm;

trait HasFiltersForm
{
    use ConcernsHasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                            ->afterStateUpdated(function (callable $set, mixed $state, Get $get) {
                                if (blank($get('endDate')) && filled($state)) {
                                    $set('endDate', $state);
                                }
                            }),
                        DatePicker::make('endDate')
                            ->minDate(fn (Get $get) => $get('startDate') ?: now())
                            ->maxDate(now())
                            ->afterStateUpdated(function (callable $set, mixed $state, Get $get) {
                                if (blank($get('startDate')) && filled($state)) {
                                    $set('startDate', $state);
                                }
                            }),
                    ])
                    ->columns(2)
                    ->visible($this instanceof ServiceRequestFeedbackReport),
                Section::make('Advance Filters')
                    ->schema([
                        Select::make('serviceRequestTypes')
                            ->label('Service Request Type Filter')
                            ->options(ServiceRequestType::query()
                                ->orderBy('name')
                                ->pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->placeholder('All'),
                    ])
                    ->columns(1)
                    ->visible($this instanceof ServiceRequestFeedbackReport),
            ]);
    }
}
