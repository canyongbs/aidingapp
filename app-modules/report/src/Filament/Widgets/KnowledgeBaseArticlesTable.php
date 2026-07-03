<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor's trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Report\Filament\Widgets;

use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\KnowledgeBaseItemResource;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\Report\Filament\Exports\KnowledgeBaseArticlesExporter;
use AidingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class KnowledgeBaseArticlesTable extends BaseWidget
{
    use InteractsWithPageFilters;

    public string $cacheTag;

    protected static ?string $heading = 'KB Articles';

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 4,
        'md' => 4,
        'lg' => 4,
    ];

    public function mount(string $cacheTag): void
    {
        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget(): void
    {
        $this->dispatch('$refresh');
    }

    /** @return list<string>|null */
    public function getCategories(): ?array
    {
        $categories = $this->pageFilters['categories'] ?? null;

        return filled($categories) ? (array) $categories : null;
    }

    public function table(Table $table): Table
    {
        $categories = $this->getCategories();

        return $table
            ->query(
                KnowledgeBaseItem::query()
                    ->with(['status', 'category', 'managers', 'concerns'])
                    ->withCount([
                        'votes',
                        'votes as helpful_votes_count' => fn (Builder $query) => $query->where('is_helpful', true),
                    ])
                    ->when(
                        $categories,
                        fn (Builder $query) => $query->whereIn('category_id', $categories)
                    )
                    ->orderBy('updated_at', 'desc')
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Article Title')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('title', 'ilike', "%{$search}%");
                    })
                    ->url(fn (KnowledgeBaseItem $record): string => KnowledgeBaseItemResource::getUrl('view', ['record' => $record])),
                TextColumn::make('managers.name')
                    ->label('Manager')
                    ->searchable(),
                TextColumn::make('portal_view_count')
                    ->label('Views')
                    ->sortable(),
                TextColumn::make('rating')
                    ->label('Rating')
                    ->getStateUsing(function (KnowledgeBaseItem $record): string {
                        $totalVotes = $record->votes_count;

                        if ($totalVotes === 0) {
                            return 'Unrated';
                        }

                        return (int) round(($record->getAttribute('helpful_votes_count') / $totalVotes) * 100) . '%';
                    }),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                IconColumn::make('health')
                    ->label('Health')
                    ->boolean(),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->sortable(),
                TextColumn::make('public')
                    ->label('Public')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('public'),
                Filter::make('created_at')
                    ->label('Created After')
                    ->schema([
                        DatePicker::make('created_after')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['created_after'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        );
                    }),
                Filter::make('updated_at')
                    ->label('Updated After')
                    ->schema([
                        DatePicker::make('updated_after')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['updated_after'],
                            fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '>=', $date),
                        );
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('Export')
                    ->exporter(KnowledgeBaseArticlesExporter::class),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->paginationPageOptions([5, 10, 25])
            ->defaultSort('updated_at', 'desc');
    }
}
