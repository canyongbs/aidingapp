<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages;

use Filament\Tables\Table;
use Laravel\Pennant\Feature;
use Filament\Actions\CreateAction;
use App\Models\Scopes\TagsForClass;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use AidingApp\Division\Models\Division;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseStatus;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseQuality;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;

class ListKnowledgeBaseItems extends ListRecords
{
    protected ?string $heading = 'Knowledge Management';

    protected static string $resource = KnowledgeBaseItemResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('title')
                    ->label('Title')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('quality.name')
                    ->label('Quality')
                    ->translateLabel()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->translateLabel()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('public')
                    ->label('Public')
                    ->translateLabel()
                    ->toggleable()
                    ->sortable()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d-m-Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('tags.name')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn (): bool => Feature::active('tags')),
            ])
            ->filters([
                SelectFilter::make('quality')
                    ->relationship('quality', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->multiple()
                    ->preload(),
                TernaryFilter::make('public'),
                Filter::make('created_at')
                    ->label('Created After')
                    ->form([
                        DatePicker::make('created_after')
                            ->native(false)
                            ->format('d-m-Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_after'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            );
                    }),
                Filter::make('updated_at')
                    ->label('Updated After')
                    ->form([
                        DatePicker::make('updated_after')
                            ->native(false)
                            ->format('d-m-Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['updated_after'],
                                fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '>=', $date),
                            );
                    }),
            ])
            ->actions([
                EditAction::make(),
                ReplicateAction::make()
                    ->label('Duplicate')
                    ->form([
                        Section::make()
                            ->schema([
                                TextInput::make('title')
                                    ->label('Article Title')
                                    ->required()
                                    ->string(),
                                Toggle::make('public')
                                    ->label('Public')
                                    ->default(false)
                                    ->onColor('success')
                                    ->offColor('gray'),
                                Textarea::make('notes')
                                    ->label('Notes')
                                    ->string(),
                                Select::make('tags')
                                    ->relationship(
                                        'tags',
                                        'name',
                                        fn (Builder $query) => $query->tap(new TagsForClass(new KnowledgeBaseItem()))
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->multiple()
                                    ->columnSpanFull()
                                    ->visible(fn (): bool => Feature::active('tags')),
                            ]),
                        Section::make()
                            ->schema([
                                Select::make('quality_id')
                                    ->label('Quality')
                                    ->relationship('quality', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->exists((new KnowledgeBaseQuality())->getTable(), (new KnowledgeBaseQuality())->getKeyName()),
                                Select::make('status_id')
                                    ->label('Status')
                                    ->relationship('status', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->exists((new KnowledgeBaseStatus())->getTable(), (new KnowledgeBaseStatus())->getKeyName()),
                                Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->exists((new KnowledgeBaseCategory())->getTable(), (new KnowledgeBaseCategory())->getKeyName()),
                                Select::make('division')
                                    ->label('Division')
                                    ->multiple()
                                    ->relationship('division', 'name')
                                    ->searchable(['name', 'code'])
                                    ->preload()
                                    ->exists((new Division())->getTable(), (new Division())->getKeyName()),
                            ]),
                    ])
                    ->before(function (array $data, Model $record) {
                        $record->title = $data['title'];
                        $record->public = $data['public'];
                        $record->notes = $data['notes'];
                    })
                    ->after(function (KnowledgeBaseItem $replica, KnowledgeBaseItem $record): void {
                        $record->load('division');

                        foreach ($record->division as $divison) {
                            $replica->division()->attach($divison->id);
                        }

                        foreach ($record->tags as $tag) {
                            $replica->tags()->attach($tag->id, [
                                // Include any pivot data if necessary
                                'taggable_type' => $tag->pivot->taggable_type,
                            ]);
                        }

                        $replica->article_details = tiptap_converter()
                            ->record($record, 'article_details')
                            ->copyImagesToNewRecord($replica->article_details, $replica, disk: 's3-public');
                        $replica->save();
                    })
                    ->excludeAttributes(['views_count', 'upvotes_count', 'my_upvotes_count'])
                    ->successNotificationTitle('Article replicated successfully!'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->disabled(fn (): bool => ! auth()->user()->can('knowledge_base_item.create'))
                ->label('New Article')
                ->modalHeading('New Article')
                ->createAnother(false)
                ->successRedirectUrl(fn (Model $record): string => KnowledgeBaseItemResource::getUrl('edit', ['record' => $record])),
        ];
    }
}
