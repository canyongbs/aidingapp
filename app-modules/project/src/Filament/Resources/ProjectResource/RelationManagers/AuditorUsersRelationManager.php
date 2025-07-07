<?php

namespace AidingApp\Project\Filament\Resources\ProjectResource\RelationManagers;

use AidingApp\Project\Models\Project;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditorUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'auditorUsers';

    protected static ?string $title = 'Users';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->authorize('update', Project::class),
            ])
            ->actions([
                DetachAction::make()
                    ->authorize('update', Project::class),
            ])
            ->bulkActions([
                DetachBulkAction::make()
                    ->authorize('update', Project::class),
            ])
            ->inverseRelationship('auditProjects');
    }
}
