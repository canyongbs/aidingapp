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

namespace AidingApp\Report\Filament\Exports;

use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class KnowledgeBaseArticlesExporter extends Exporter
{
    protected static ?string $model = KnowledgeBaseItem::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('title')
                ->label('Article Title'),
            ExportColumn::make('managers_list')
                ->label('Manager')
                ->state(fn (KnowledgeBaseItem $record): string => $record->managers->pluck('name')->join(', ')),
            ExportColumn::make('portal_view_count')
                ->label('Views'),
            ExportColumn::make('rating')
                ->label('Rating')
                ->state(function (KnowledgeBaseItem $record): string {
                    $totalVotes = $record->votes_count;

                    if ($totalVotes === 0) {
                        return 'Unrated';
                    }

                    return (int) round(($record->getAttribute('helpful_votes_count') / $totalVotes) * 100) . '%';
                }),
            ExportColumn::make('category.name')
                ->label('Category'),
            ExportColumn::make('health_status')
                ->label('Health')
                ->state(fn (KnowledgeBaseItem $record): string => $record->health ? 'Healthy' : 'Unhealthy'),
            ExportColumn::make('status.name')
                ->label('Status'),
            ExportColumn::make('public')
                ->label('Public')
                ->state(fn (KnowledgeBaseItem $record): string => $record->public ? 'Yes' : 'No'),
            ExportColumn::make('updated_at')
                ->label('Last Updated'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your knowledge base articles export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
