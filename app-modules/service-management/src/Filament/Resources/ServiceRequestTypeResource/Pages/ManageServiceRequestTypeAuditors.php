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

class ManageServiceRequestTypeAuditors extends ManageRelatedRecords
{
    protected static string $resource = ServiceRequestTypeResource::class;

    protected static string $relationship = 'auditors';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function canAccess(array $parameters = []): bool
    {
        return ServiceRequestTypeManagerAuditor::active();
    }

    public static function getNavigationLabel(): string
    {
        return 'Auditors';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->inverseRelationship('auditableServiceRequestTypes')
            ->columns([
                TextColumn::make('name'),
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
