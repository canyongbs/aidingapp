<?php

namespace AidingApp\Project\Filament\Resources\ProjectMilestoneStatusResource\Pages;

use AidingApp\Project\Filament\Resources\ProjectMilestoneStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProjectMilestoneStatus extends EditRecord
{
    protected static string $resource = ProjectMilestoneStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
