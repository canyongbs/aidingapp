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

use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\KnowledgeBaseItemResource;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class ViewKnowledgeBaseItem extends ViewRecord
{
    protected static string $resource = KnowledgeBaseItemResource::class;

    public function getTitle(): string|Htmlable
    {
        assert($this->record instanceof KnowledgeBaseItem);

        return $this->record->title;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('knowledge-base::filament.pages.badges'),
                Tabs::make()
                    ->tabs([
                        Tab::make('Content')
                            ->label('Resource')
                            ->schema([
                                TextEntry::make('article_details')
                                    ->columnSpanFull()
                                    ->hiddenLabel()
                                    ->html(),
                            ])
                            ->id('content'),
                        Tab::make('Properties')
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Article Title')
                                    ->columnSpanFull(),
                                TextEntry::make('notes')
                                    ->label('Notes')
                                    ->columnSpanFull(),
                                TextEntry::make('public')
                                    ->label('Public')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                                TextEntry::make('is_featured')
                                    ->label('Featured')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                                TextEntry::make('tags')
                                    ->getStateUsing(fn (KnowledgeBaseItem $record) => $record->tags->pluck('name'))
                                    ->badge(),
                            ])
                            ->id('properties')
                            ->columns(2),
                        Tab::make('Metadata')
                            ->schema([
                                TextEntry::make('quality.name')
                                    ->label('Quality'),
                                TextEntry::make('status.name')
                                    ->label('Status'),
                                TextEntry::make('category.name')
                                    ->label('Category'),
                                TextEntry::make('division.name')
                                    ->label('Division'),
                            ])
                            ->id('metadata'),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
