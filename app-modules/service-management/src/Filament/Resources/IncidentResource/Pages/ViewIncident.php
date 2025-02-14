<?php

namespace AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages;

use AidingApp\ServiceManagement\Filament\Resources\IncidentResource;
use AidingApp\ServiceManagement\Models\Incident;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

class ViewIncident extends ViewRecord
{
    protected static string $resource = IncidentResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title'),
                        TextEntry::make('description')
                            ->label('Description'),
                        ColorEntry::make('severity.rgb_color')
                            ->label('Severity')
                            ->tooltip(fn (Incident $record) => $record->severity->name),
                        TextEntry::make('status.name')
                            ->label('Status'),
                        TextEntry::make('assignedTeam.name')
                            ->label('Assigned Team'),
                    ])
                    ->columns(),
            ]);
    }

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var Incident $record */
        $record = $this->getRecord();

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('edit', ['record' => $record]) => Str::limit($record->title, 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
