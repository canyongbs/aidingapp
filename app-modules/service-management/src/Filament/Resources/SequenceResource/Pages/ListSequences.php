<?php

namespace AidingApp\ServiceManagement\Filament\Resources\SequenceResource\Pages;

use AidingApp\ServiceManagement\Filament\Resources\SequenceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSequences extends ListRecords
{
    protected static string $resource = SequenceResource::class;

    protected static string $view = 'filament.pages.coming-soon';

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         CreateAction::make(),
    //     ];
    // }
}
