<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Schemas\Components;

use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Collection;

class ServiceRequestStatusSelect
{
    public static function make(ServiceRequest $serviceRequest): Select
    {
        return Select::make('status_id')
            ->label('Status')
            ->allowHtml()
            ->options(fn () => ServiceRequestStatus::orderBy('sort')
                ->get(['id', 'name', 'classification', 'color'])
                ->groupBy(fn (ServiceRequestStatus $status) => $status->classification->getLabel())
                ->map(fn (Collection $group) => $group->mapWithKeys(fn (ServiceRequestStatus $status): array => [
                    $status->getKey() => view('service-management::components.service-request-status-select-option-label', ['status' => $status])->render(),
                ])))
            ->exists((new ServiceRequestStatus())->getTable(), 'id')
            ->default(fn () => $serviceRequest->status->getKey());
    }
}
