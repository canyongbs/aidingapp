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

namespace AidingApp\Project\Filament\Resources\Pipelines;

use AidingApp\Project\Filament\Resources\Pipelines\Pages\CreatePipeline;
use AidingApp\Project\Filament\Resources\Pipelines\Pages\EditPipeline;
use AidingApp\Project\Filament\Resources\Pipelines\Pages\ManagePipelineEntries;
use AidingApp\Project\Filament\Resources\Pipelines\Pages\ViewPipeline;
use AidingApp\Project\Filament\Resources\Projects\ProjectResource;
use AidingApp\Project\Models\Pipeline;
use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

class PipelineResource extends Resource
{
    protected static ?string $model = Pipeline::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $parentResource = ProjectResource::class;

    // Every page here is reached through a Project (see $parentResource below), whose own
    // access check already runs alongside this one. Real authorization is delegated to the
    // Project via PipelinePolicy, but Filament's automatic viewAny gate has no record to pass,
    // so it must be bypassed here rather than by the bare pipeline.view-any permission.
    public static function canAccess(): bool
    {
        return true;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        if ($page instanceof ManagePipelineEntries) {
            return [];
        }

        return $page->generateNavigationItems([
            ViewPipeline::class,
            EditPipeline::class,
            ManagePipelineEntries::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'create' => CreatePipeline::route('/create'),
            'view' => ViewPipeline::route('/{record}'),
            'edit' => EditPipeline::route('/{record}/edit'),
            'entries' => ManagePipelineEntries::route('/{record}/entries'),
        ];
    }

    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        if (isset($parameters['record'])) {
            return static::getUrl('view', $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
        }

        return '';
    }
}
