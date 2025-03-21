<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource\Pages;

use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

class ViewServiceMonitoring extends ViewRecord
{
    protected static string $resource = ServiceMonitoringResource::class;

    protected static ?string $navigationLabel = 'View';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('description')
                            ->label('Description'),
                        TextEntry::make('domain')
                            ->label('URL'),
                        TextEntry::make('frequency')
                            ->label('Frequency'),
                        Section::make('Notification Group')
                            ->schema([
                                TextEntry::make('teams.name')
                                    ->label('Teams')
                                    ->listWithLineBreaks()
                                    ->limitList(3)
                                    ->expandableLimitedList()
                                    ->visible($this->getRecord()->teams()->count()),
                                TextEntry::make('users.name')
                                    ->label('Users')
                                    ->listWithLineBreaks()
                                    ->limitList(3)
                                    ->expandableLimitedList()
                                    ->visible($this->getRecord()->users()->count()),
                            ])
                            ->columns()
                            ->visible($this->getRecord()->teams()->count() || $this->getRecord()->users()->count()),
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
        /** @var ServiceMonitoringTarget $record */
        $record = $this->getRecord();

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('edit', ['record' => $record]) => Str::limit($record->name, 16),
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
