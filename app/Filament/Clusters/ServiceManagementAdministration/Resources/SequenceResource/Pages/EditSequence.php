<?php

namespace App\Filament\Clusters\ServiceManagementAdministration\Resources\SequenceResource\Pages;

use App\Filament\Clusters\ServiceManagementAdministration\Resources\SequenceResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables\Actions\DeleteAction;

class EditSequence extends EditRecord
{
    protected static string $resource = SequenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
