<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages;

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class Feedback extends ViewRecord
{
    protected static string $resource = ServiceRequestResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('feedback.csat_answer')
                            ->label('Customer Satisfaction (CSAT)')
                            ->default('N/A'),
                        TextEntry::make('feedback.nps_answer')
                            ->label('Net Promoter Score (NPS)')
                            ->default('N/A'),
                    ])
                    ->columns(),
            ]);
    }
}
