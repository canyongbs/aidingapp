<?php

namespace AidingApp\Project\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AidingApp\Project\Filament\Resources\ProjectMilestoneStatusResource\Pages;
use AidingApp\Project\Filament\Resources\ProjectMilestoneStatusResource\RelationManagers;
use AidingApp\Project\Models\ProjectMilestoneStatus;

class ProjectMilestoneStatusResource extends Resource
{
    protected static ?string $model = ProjectMilestoneStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required(),

                TextInput::make('description')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectMilestoneStatuses::route('/'),
            'create' => Pages\CreateProjectMilestoneStatus::route('/create'),
            'edit' => Pages\EditProjectMilestoneStatus::route('/{record}/edit'),
        ];
    }
}
