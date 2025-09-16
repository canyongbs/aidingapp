<?php

namespace AidingApp\ServiceManagement\Filament\Actions;

use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Filament\Actions\Action;

class ResetAction extends Action
{
    protected function setup(): void
    {
        parent::setup();

        $this->form([])
            ->label('Reset Monitoring')
            ->action(function (array $data, ServiceMonitoringTarget $record): void {
                $record->histories()->delete();
            })
            ->requiresConfirmation()
            ->modalDescription('Are you sure you wish to reset monitoring, the data loss is irreversible.');
    }

    public static function getDefaultName(): ?string
    {
        return 'reset';
    }
}
