<?php

namespace App\Filament\Clusters\ServiceManagementAdministration\Resources;

use AidingApp\ServiceManagement\Models\Sequence;
use App\Filament\Clusters\ServiceManagementAdministration;
use App\Filament\Clusters\ServiceManagementAdministration\Resources\SequenceResource\Pages\CreateSequence;
use App\Filament\Clusters\ServiceManagementAdministration\Resources\SequenceResource\Pages\EditSequence;
use App\Filament\Clusters\ServiceManagementAdministration\Resources\SequenceResource\Pages\ListSequences;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;

class SequenceResource extends Resource
{
    protected static ?string $model = Sequence::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Service Requests';

    protected static ?int $navigationSort = 50;

    protected static ?string $cluster = ServiceManagementAdministration::class;

    protected static ?string $view = 'filament.pages.coming-soon';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSequences::route('/'),
            'create' => CreateSequence::route('/create'),
            'edit' => EditSequence::route('/{record}/edit'),
        ];
    }
}
