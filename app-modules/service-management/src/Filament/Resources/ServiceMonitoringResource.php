<?php

namespace AidingApp\ServiceManagement\Filament\Resources;

use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource\Pages\CreateServiceMonitoring;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource\Pages\EditServiceMonitoring;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource\Pages\ListServiceMonitorings;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource\Pages\ViewServiceMonitoring;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Features\ServiceMonitoring;
use Filament\Resources\Resource;

class ServiceMonitoringResource extends Resource
{
    protected static ?string $model = ServiceMonitoringTarget::class;

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?string $modelLabel = 'service monitoring';

    protected static ?int $navigationSort = 80;

    public static function canAccess(): bool
    {
        return ServiceMonitoring::active() && parent::canAccess();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceMonitorings::route('/'),
            'create' => CreateServiceMonitoring::route('/create'),
            'view' => ViewServiceMonitoring::route('/{record}'),
            'edit' => EditServiceMonitoring::route('/{record}/edit'),
        ];
    }
}
