<?php

namespace AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\AssociateAction;
use Filament\Tables\Actions\BulkActionGroup;
use App\Filament\Forms\Components\IconSelect;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DissociateAction;
use Filament\Tables\Actions\DissociateBulkAction;
use App\Filament\Tables\Columns\OpenSearch\TextColumn;
use Filament\Resources\RelationManagers\RelationManager;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource;

class SubCategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'subCategories';

    protected static ?string $inverseRelationship = 'parentCategory';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->string(),
                IconSelect::make('icon'),
                TextInput::make('slug')
                    ->regex('/^[a-zA-Z0-9]+(-[a-zA-Z0-9]+)*$/')
                    ->unique()
                    ->maxLength(255)
                    ->required()
                    ->dehydrateStateUsing(fn (string $state): string => strtolower($state)),
                Textarea::make('description')
                    ->label('Description')
                    ->nullable()
                    ->string()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->heading('Subcategories')
            ->columns([
                TextColumn::make('name'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Subcategory')
                    ->modalHeading('Create knowledge base subcategory'),
                AssociateAction::make()
                    ->modalHeading('Associate knowledge base subcategory')
                    ->recordSelectOptionsQuery(
                        fn (Builder $query) => $query->where('id', '!=', $this->getOwnerRecord()->getKey())
                            ->doesntHave('parentCategory')
                            ->doesntHave('subCategories')
                    ),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->url(fn (KnowledgeBaseCategory $record) => KnowledgeBaseCategoryResource::getUrl('view', ['record' => $record->getKey()])),
                    EditAction::make()
                        ->url(fn (KnowledgeBaseCategory $record) => KnowledgeBaseCategoryResource::getUrl('edit', ['record' => $record->getKey()])),
                    DissociateAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
