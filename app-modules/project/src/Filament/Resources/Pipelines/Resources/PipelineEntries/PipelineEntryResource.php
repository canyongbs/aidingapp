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

namespace AidingApp\Project\Filament\Resources\Pipelines\Resources\PipelineEntries;

use AidingApp\Project\Filament\Resources\Pipelines\Pages\EditPipelineEntry;
use AidingApp\Project\Filament\Resources\Pipelines\Pages\ViewPipelineEntry;
use AidingApp\Project\Filament\Resources\Pipelines\PipelineResource;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PipelineEntryResource extends Resource
{
    protected static ?string $model = PipelineEntry::class;

    protected static ?string $slug = 'entries';

    protected static ?string $breadcrumb = 'Pipeline Entries';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPipelineEntry::class,
            EditPipelineEntry::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'view' => ViewPipelineEntry::route('/{record}/view'),
            'edit' => EditPipelineEntry::route('/{record}/edit'),
        ];
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return PipelineResource::asParent()
            ->relationship('entries')
            ->inverseRelationship('pipeline');
    }

    public static function canAccess(): bool
    {
        return PipelineResource::canAccess();
    }

    public static function canViewAny(): bool
    {
        return PipelineResource::canViewAny();
    }

    public static function canView(Model $record): bool
    {
        $pipeline = self::resolvePipeline($record);

        return $pipeline ? PipelineResource::canView($pipeline) : false;
    }

    public static function canEdit(Model $record): bool
    {
        $pipeline = self::resolvePipeline($record);

        return $pipeline ? PipelineResource::canEdit($pipeline) : false;
    }

    public static function canDelete(Model $record): bool
    {
        $pipeline = self::resolvePipeline($record);

        return $pipeline ? PipelineResource::canDelete($pipeline) : false;
    }

    /**
     * @param  Builder<PipelineEntry>  $query
     *
     * @return Builder<PipelineEntry>
     */
    public static function scopeEloquentQueryToParent(Builder $query, Model $parentRecord): Builder
    {
        return $query->whereHas(
            'pipelineStage',
            function (Builder $query) use ($parentRecord): void {
                $query->whereBelongsTo($parentRecord, 'pipeline');
            },
        );
    }

    private static function resolvePipeline(Model $record): ?Pipeline
    {
        if (! $record instanceof PipelineEntry) {
            return null;
        }

        return $record->pipelineStage->pipeline;
    }
}
