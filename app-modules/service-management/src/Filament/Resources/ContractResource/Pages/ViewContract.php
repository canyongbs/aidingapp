<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ContractResource\Pages;

use Illuminate\Support\Str;
use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Actions\DeleteAction;
use App\Features\ContractManagement;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AidingApp\ServiceManagement\Models\Contract;
use AidingApp\ServiceManagement\Filament\Resources\ContractResource;

class ViewContract extends ViewRecord
{
    protected static string $resource = ContractResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('status'),
                    TextEntry::make('contractType.name')
                        ->visible(ContractManagement::active())
                        ->label('Contract Type'),
                    TextEntry::make('vendor_name')
                        ->label('Vendor Name'),
                    TextEntry::make('start_date')
                        ->label('Start Date'),
                    TextEntry::make('end_date')
                        ->label('End Date'),
                ])
                ->columns(2),
        ]);
    }

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var Contract $record */
        $record = $this->getRecord();

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('view', ['record' => $record]) => Str::limit($record->name, 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    public function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
