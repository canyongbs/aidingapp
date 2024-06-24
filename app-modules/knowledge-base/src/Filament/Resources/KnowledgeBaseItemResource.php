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

namespace AidingApp\KnowledgeBase\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\EditKnowledgeBaseItem;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\ViewKnowledgeBaseItem;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\ListKnowledgeBaseItems;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages\CreateKnowledgeBaseItem;

class KnowledgeBaseItemResource extends Resource
{
    protected static ?string $model = KnowledgeBaseItem::class;

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?int $navigationSort = 20;

    protected static ?string $breadcrumb = 'Knowledge Management';

    protected static ?string $modelLabel = 'knowledge management';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Knowledge Management';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'article_details'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->with(['quality', 'status', 'category', 'division']);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'Quality' => $record->quality?->name,
            'Status' => $record->status?->name,
            'Category' => $record->category?->name,
            'Division' => $record->division->pluck('name')->implode(', '),
        ], fn (mixed $value): bool => filled($value));
    }

    public static function getGlobalSearchResultUrl(Model $record): ?string
    {
        return static::getUrl('view', ['record' => $record]);
    }

    public static function form(Form $form): Form
    {
        return resolve(CreateKnowledgeBaseItem::class)->form($form);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKnowledgeBaseItems::route('/'),
            'view' => ViewKnowledgeBaseItem::route('/{record}'),
            'edit' => EditKnowledgeBaseItem::route('/{record}/edit'),
        ];
    }
}
