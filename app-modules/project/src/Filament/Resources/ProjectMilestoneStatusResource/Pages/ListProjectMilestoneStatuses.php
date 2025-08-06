<?php

namespace AidingApp\Project\Filament\Resources\ProjectMilestoneStatusResource\Pages;

use AidingApp\Project\Filament\Resources\ProjectMilestoneStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectMilestoneStatuses extends ListRecords
{
    protected static string $resource = ProjectMilestoneStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
