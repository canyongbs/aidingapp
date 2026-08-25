<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Contact\Filament\Exports;

use AidingApp\Contact\Models\Organization;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Collection;

class OrganizationExporter extends Exporter
{
    protected static ?string $model = Organization::class;

    /**
     * @return array<ExportFormat>
     */
    public function getFormats(): array
    {
        // The importer only accepts CSV, so exports must be re-importable.
        return [
            ExportFormat::Csv,
        ];
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Name'),
            ExportColumn::make('email')
                ->label('Email'),
            ExportColumn::make('phone_number')
                ->label('Phone Number'),
            ExportColumn::make('website')
                ->label('Website'),
            ExportColumn::make('industry.name')
                ->label('Industry'),
            ExportColumn::make('type.name')
                ->label('Type'),
            ExportColumn::make('description')
                ->label('Description'),
            ExportColumn::make('number_of_employees')
                ->label('Number of Employees'),
            ExportColumn::make('address')
                ->label('Address'),
            ExportColumn::make('city')
                ->label('City'),
            ExportColumn::make('state')
                ->label('State'),
            ExportColumn::make('postalcode')
                ->label('Postal Code'),
            ExportColumn::make('country')
                ->label('Country'),
            ExportColumn::make('linkedin_url')
                ->label('LinkedIn URL'),
            ExportColumn::make('facebook_url')
                ->label('Facebook URL'),
            ExportColumn::make('twitter_url')
                ->label('Twitter URL'),
            ExportColumn::make('domains')
                ->label('Domains')
                ->state(fn (Organization $record): string => Collection::make($record->domains ?? [])
                    ->pluck('domain')
                    ->filter()
                    ->implode('|')),
            ExportColumn::make('is_contact_generation_enabled')
                ->label('Automatically generate contact record on login')
                ->state(fn (Organization $record): string => $record->is_contact_generation_enabled ? 'true' : 'false'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your organization export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
