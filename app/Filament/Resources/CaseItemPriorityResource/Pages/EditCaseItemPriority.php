<?php

namespace App\Filament\Resources\CaseItemPriorityResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CaseItemPriorityResource;

class EditCaseItemPriority extends EditRecord
{
    protected static string $resource = CaseItemPriorityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
