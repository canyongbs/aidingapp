<?php

namespace AidingApp\ServiceManagement\Filament\Components;

use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class ReportChannelCheckboxList
{
    public static function make(bool $isCreatePage = false): CheckboxList
    {
        $checkboxList = CheckboxList::make('report_channels')
            ->label('Channels')
            ->dehydrated(false)
            ->options([
                'is_reported_via_email' => 'Email',
                'is_reported_via_database' => 'Application',
            ])
            ->live()
            ->afterStateUpdated(function (Set $set, array $state = []): void {
                $set('is_reported_via_email', in_array('is_reported_via_email', $state, true));
                $set('is_reported_via_database', in_array('is_reported_via_database', $state, true));
            })
            ->dehydrated(false)
            ->visible(fn (Get $get) => $get('is_reporting_active'));

        if (! $isCreatePage) {
            $checkboxList->afterStateHydrated(fn (Set $set, ServiceMonitoringTarget $record) => $set(
                'report_channels',
                [
                    ...($record->is_reported_via_email ? ['is_reported_via_email'] : []),
                    ...($record->is_reported_via_database ? ['is_reported_via_database'] : []),
                ]
            ));
        }

        return $checkboxList;
    }
}