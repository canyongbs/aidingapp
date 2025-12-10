<?php

namespace AidingApp\Report\Filament\Exports;

use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ServiceMonitoringTargetExporter extends Exporter
{
    protected static ?string $model = ServiceMonitoringTarget::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('domain')
                ->label('URL'),
            ExportColumn::make('frequency'),
            ExportColumn::make('one_day')
                ->label('Last 1 Day')
                ->state(fn (ServiceMonitoringTarget $record) => $record->getUptimePercentage(1)),
            ExportColumn::make('seven_days')
                ->label('Last 7 Days')
                ->state(fn (ServiceMonitoringTarget $record) => $record->getUptimePercentage(7)),
            ExportColumn::make('thirty_days')
                ->label('Last 30 Days')
                ->state(fn (ServiceMonitoringTarget $record) => $record->getUptimePercentage(30)),
            ExportColumn::make('one_year')
                ->label('Last 1 Year')
                ->state(fn (ServiceMonitoringTarget $record) => $record->getUptimePercentage(365)),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your service monitoring target export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
