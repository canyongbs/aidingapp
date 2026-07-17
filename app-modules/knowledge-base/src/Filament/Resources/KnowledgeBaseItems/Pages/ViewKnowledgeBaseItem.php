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
      of the licensor in the software. Any use of the licensor’s trademarks is subject
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

namespace AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\Pages;

use AidingApp\Division\Models\Division;
use AidingApp\KnowledgeBase\Filament\Actions\CreateConcernAction;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\KnowledgeBaseItemResource;
use AidingApp\KnowledgeBase\Filament\Widgets\KnowledgeBaseItemConcernsTable;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Url;

class ViewKnowledgeBaseItem extends ViewRecord
{
    protected static string $resource = KnowledgeBaseItemResource::class;

    #[Url]
    public ?string $tab = null;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        if (! in_array($this->tab, ['resource', 'properties', 'concerns', 'health'])) {
            $this->tab = 'resource';
        }
    }

    public function getTitle(): string|Htmlable
    {
        assert($this->record instanceof KnowledgeBaseItem);

        return $this->record->title;
    }

    public function infolist(Schema $schema): Schema
    {
        $this->record->loadCount([
            'votes',
            'votes as helpful_votes_count' => fn ($query) => $query->where('is_helpful', true),
        ]);

        return $schema
            ->components([
                View::make('knowledge-base::filament.pages.badges'),
                Tabs::make()
                    ->tabs([
                        'resource' => Tab::make('Content')
                            ->label('Resource')
                            ->schema([
                                TextEntry::make('article_details')
                                    ->columnSpanFull()
                                    ->hiddenLabel()
                                    ->html()
                                    ->prose()
                                    ->extraAttributes(['class' => 'knowledge-base-article']),
                            ]),
                        'properties' => Tab::make('Properties')
                            ->schema([
                                Grid::make(Division::count() > 1 ? 4 : 3)
                                    ->schema([
                                        TextEntry::make('status.name')
                                            ->label('Status'),
                                        TextEntry::make('category.name')
                                            ->label('Category'),
                                        TextEntry::make('division.name')
                                            ->visible(fn (): bool => Division::count() > 1)
                                            ->label('Division'),
                                        TextEntry::make('managers')
                                            ->label('Managers')
                                            ->getStateUsing(fn (KnowledgeBaseItem $record) => $record->managers->pluck('name')->join(', ')),
                                    ]),
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('public')
                                            ->label('Public')
                                            ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                                        TextEntry::make('is_featured')
                                            ->label('Featured')
                                            ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                                        TextEntry::make('has_table_of_contents')
                                            ->label('Table of Contents')
                                            ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                                    ]),
                                TextEntry::make('notes')
                                    ->label('Notes')
                                    ->columnSpanFull(),
                                TextEntry::make('tags')
                                    ->getStateUsing(fn (KnowledgeBaseItem $record) => $record->tags->pluck('name'))
                                    ->badge()
                                    ->columnSpanFull(),
                                TextEntry::make('rating')
                                    ->label('Rating')
                                    ->getStateUsing(function (KnowledgeBaseItem $record): string {
                                        $totalVotes = $record->votes_count;

                                        if ($totalVotes === 0) {
                                            return 'Unrated';
                                        }

                                        return (int) round(($record->getAttribute('helpful_votes_count') / $totalVotes) * 100) . '%';
                                    }),
                            ])
                            ->columns(2),
                        'concerns' => Tab::make('Concerns')
                            ->schema([
                                Livewire::make(KnowledgeBaseItemConcernsTable::class, ['record' => $this->getRecord()]),
                            ]),
                        'health' => Tab::make('Health')
                            ->schema([
                                IconEntry::make('title_filled')
                                    ->label('Title Filled')
                                    ->boolean(),
                                IconEntry::make('article_filled')
                                    ->label('Article Filled')
                                    ->boolean(),
                                IconEntry::make('manager_assigned')
                                    ->label('Manager Assigned')
                                    ->boolean(),
                                IconEntry::make('no_unresolved_concerns')
                                    ->label('No Unresolved Concerns')
                                    ->boolean(),
                                IconEntry::make('no_broken_links')
                                    ->label('No Broken Links Detected')
                                    ->boolean()
                                    ->tooltip(fn (KnowledgeBaseItem $record): string => $record->are_broken_links_detected
                                        ? implode("\n", $record->broken_links ?? [])
                                        : 'No broken links were detected in this article.'),
                                IconEntry::make('no_broken_images')
                                    ->label('No Broken Images Detected')
                                    ->boolean()
                                    ->tooltip(fn (KnowledgeBaseItem $record): string => $record->are_broken_images_detected
                                        ? implode("\n", $record->broken_images ?? [])
                                        : 'No broken images were detected in this article.'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull()
                    ->livewireProperty('tab'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateConcernAction::make(),
            EditAction::make()
                ->url(function (): string {
                    $parameters = ['record' => $this->getRecord()];

                    if ($this->tab) {
                        $parameters['tab'] = $this->tab;
                    }

                    return KnowledgeBaseItemResource::getUrl('edit', $parameters);
                }),
            DeleteAction::make(),
        ];
    }
}
