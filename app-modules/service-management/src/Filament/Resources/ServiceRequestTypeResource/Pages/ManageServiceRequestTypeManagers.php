<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages;

use Filament\Tables\Table;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use App\Filament\Tables\Columns\OpenSearch\TextColumn;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use App\Features\ServiceRequestTypeManagerAuditor;

class ManageServiceRequestTypeManagers extends ManageRelatedRecords
{
    protected static string $resource = ServiceRequestTypeResource::class;

    protected static string $relationship = 'managers';

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    public static function canAccess(array $parameters = []): bool
    {
        return ServiceRequestTypeManagerAuditor::active();
    }

    public static function getNavigationLabel(): string
    {
        return 'Managers';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->inverseRelationship('managableServiceRequestTypes')
            ->columns([
                TextColumn::make('name')
                    ->label('Team'),
            ])
            ->headerActions([
                AttachAction::make(),
            ])
            ->actions([
                DetachAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
