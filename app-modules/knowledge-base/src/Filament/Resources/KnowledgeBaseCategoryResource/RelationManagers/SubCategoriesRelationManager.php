<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource\RelationManagers;

use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use App\Filament\Forms\Components\IconSelect;
use App\Filament\Tables\Columns\OpenSearch\TextColumn;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\AssociateAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DissociateAction;
use Filament\Tables\Actions\DissociateBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubCategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'subCategories';

    protected static ?string $inverseRelationship = 'parentCategory';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('icon')
                            ->state(fn (KnowledgeBaseCategory $record): string => (string) str($record->icon)->after('heroicon-o-')->headline())
                            ->icon(fn (KnowledgeBaseCategory $record): string => $record->icon)
                            ->hidden(fn (KnowledgeBaseCategory $record): bool => blank($record->icon)),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        TextEntry::make('slug')
                            ->hidden(fn (KnowledgeBaseCategory $record): bool => blank($record->slug)),
                    ])
                    ->columns(),
            ]);
    }

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
                    ->unique(ignoreRecord: true)
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
                TextColumn::make('slug'),
                IconColumn::make('icon')
                    ->icon(fn (string $state): string => $state)
                    ->tooltip(fn (?string $state): ?string => filled($state) ? (string) str($state)->after('heroicon-o-')->headline() : null),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Subcategory')
                    ->modalHeading('Create knowledge base subcategory'),
                AssociateAction::make()
                    ->modalHeading('Associate knowledge base subcategory')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(
                        fn (Builder $query) => $query->where('id', '!=', $this->getOwnerRecord()->getKey())
                            ->doesntHave('parentCategory')
                            ->doesntHave('subCategories')
                    ),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
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
