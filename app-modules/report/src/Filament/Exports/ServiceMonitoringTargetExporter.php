<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
