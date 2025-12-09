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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ServiceRequestsExporter extends Exporter
{
    protected static ?string $model = ServiceRequest::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('service_request_number')
                ->label('Request #'),
            ExportColumn::make('title')
                ->label('Title'),
            ExportColumn::make('priority.type.name')
                ->label('Type'),
            ExportColumn::make('status.name')
                ->label('Status'),
            ExportColumn::make('respondent_name')
                ->label('Related To')
                ->state(
                    fn (ServiceRequest $record): ?string => $record->respondent->{$record->respondent::displayNameKey()}
                ),
            ExportColumn::make('organization_name')
                ->label('Organization')
                ->state(
                    fn (ServiceRequest $record): ?string => $record->respondent->organization->name ?? null
                ),
            ExportColumn::make('assignedTo.user.name')
                ->label('Assigned To'),
            ExportColumn::make('created_at')
                ->label('Date/Time Opened')
                ->dateTime('m/d/Y g:i A'),
            ExportColumn::make('closed_at')
                ->label('Date/Time Closed')
                ->state(function (ServiceRequest $record): ?string {
                    if ($record->status->classification !== SystemServiceRequestClassification::Closed) {
                        return null;
                    }
                    $resolvedAt = $record->getResolvedAt();

                    return $resolvedAt->format('m/d/Y g:i A');
                }),
            ExportColumn::make('age')
                ->label('Age')
                ->state(function (ServiceRequest $record): ?string {
                    if ($record->status->classification !== SystemServiceRequestClassification::Closed) {
                        return null;
                    }

                    $resolvedAt = $record->getResolvedAt();
                    $interval = $record->created_at->diff($resolvedAt);
                    $days = $interval->days;
                    $hours = $interval->h;
                    $minutes = $interval->i;

                    if ($days > 0) {
                        return "{$days}d {$hours}h {$minutes}m";
                    } elseif ($hours > 0) {
                        return "{$hours}h {$minutes}m";
                    }

                    return "{$minutes}m";
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your service requests export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
