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

namespace AidingApp\ServiceManagement\Filament\Components;

use AidingApp\ServiceManagement\Enums\ServiceMonitoringReportFrequency;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

class ReportFrequencyInfolistSection
{
    public static function make(ServiceMonitoringReportFrequency $frequency): Section
    {
        return Section::make($frequency->getLabel() . ' Reporting')
            ->schema([
                IconEntry::make("reportConfigurations.{$frequency->value}.email")
                    ->label('Email')
                    ->boolean()
                    ->state(fn (ServiceMonitoringTarget $record): bool => (bool) $record->reportConfigurationFor($frequency)?->is_reported_via_email),
                IconEntry::make("reportConfigurations.{$frequency->value}.database")
                    ->label('Application')
                    ->boolean()
                    ->state(fn (ServiceMonitoringTarget $record): bool => (bool) $record->reportConfigurationFor($frequency)?->is_reported_via_database),
                TextEntry::make("reportConfigurations.{$frequency->value}.users")
                    ->label('Users')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->state(fn (ServiceMonitoringTarget $record): array => $record->reportConfigurationFor($frequency)?->reportUsers->pluck('name')->all() ?? [])
                    ->visible(fn (ServiceMonitoringTarget $record): bool => filled($record->reportConfigurationFor($frequency)?->reportUsers)),
                TextEntry::make("reportConfigurations.{$frequency->value}.departments")
                    ->label('Departments')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->state(fn (ServiceMonitoringTarget $record): array => $record->reportConfigurationFor($frequency)?->reportDepartments->pluck('name')->all() ?? [])
                    ->visible(fn (ServiceMonitoringTarget $record): bool => filled($record->reportConfigurationFor($frequency)?->reportDepartments)),
                TextEntry::make("reportConfigurations.{$frequency->value}.contacts")
                    ->label('Contacts')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->state(fn (ServiceMonitoringTarget $record): array => $record->reportConfigurationFor($frequency)?->reportContacts->pluck('full_name')->all() ?? [])
                    ->visible(fn (ServiceMonitoringTarget $record): bool => filled($record->reportConfigurationFor($frequency)?->reportContacts)),
            ])
            ->visible(fn (ServiceMonitoringTarget $record): bool => (bool) $record->reportConfigurationFor($frequency)?->is_active)
            ->columns(3);
    }
}
