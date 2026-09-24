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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceMonitorings\Schemas\Components;

use AidingApp\ServiceManagement\Enums\MonitorType;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Features\ServiceMonitoringApiEndpointFeature;
use Filament\Forms\Components\Radio;

class MonitorTypeRadio
{
    public static function make(): Radio
    {
        return Radio::make('monitor_type')
            ->label('Monitor Type')
            // TODO: Cleanup Task (service-monitoring-api-endpoint-feature): once the flag
            // is removed, pass MonitorType::class directly to ->options() again instead of
            // filtering the case list.
            ->options(fn (?ServiceMonitoringTarget $record): array => collect(MonitorType::cases())
                // Keep API Endpoint selectable for a record that's already using it, even if the
                // flag is currently off — otherwise an existing API Endpoint monitor fails
                // validation ("the selected monitor type is invalid") the moment you try to save
                // any other change to it while the flag is deactivated.
                ->filter(fn (MonitorType $monitorType): bool => $monitorType !== MonitorType::ApiEndpoint
                    || ServiceMonitoringApiEndpointFeature::active()
                    || $record?->monitor_type === MonitorType::ApiEndpoint)
                ->mapWithKeys(fn (MonitorType $monitorType): array => [$monitorType->value => $monitorType->getLabel()])
                ->all())
            ->enum(MonitorType::class)
            ->default(MonitorType::Availability)
            ->live()
            ->inline()
            ->columnSpanFull();
    }
}
