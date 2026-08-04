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

namespace AidingApp\Project\Filament\Resources\Projects;

use AidingApp\Project\Filament\Resources\Projects\Pages\CreateProject;
use AidingApp\Project\Filament\Resources\Projects\Pages\EditProject;
use AidingApp\Project\Filament\Resources\Projects\Pages\ListProjects;
use AidingApp\Project\Filament\Resources\Projects\Pages\ManageAuditors;
use AidingApp\Project\Filament\Resources\Projects\Pages\ManageFiles;
use AidingApp\Project\Filament\Resources\Projects\Pages\ManageGuests;
use AidingApp\Project\Filament\Resources\Projects\Pages\ManageManagers;
use AidingApp\Project\Filament\Resources\Projects\Pages\ManageMilestones;
use AidingApp\Project\Filament\Resources\Projects\Pages\ManagePipelines;
use AidingApp\Project\Filament\Resources\Projects\Pages\ViewProject;
use AidingApp\Project\Models\Project;
use App\Enums\NavigationGroup;
use App\Features\ProjectArchivingFeature;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use UnitEnum;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Projects;

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = true;

    public static function getRecordTitle(?Model $record): string | Htmlable | null
    {
        if (! $record instanceof Project) {
            return parent::getRecordTitle($record);
        }

        return Str::limit($record->name, 16);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    /**
     * @return Builder<Project>
     */
    public static function getEloquentQuery(): Builder
    {
        /** @var Builder<Project> $query */
        $query = parent::getEloquentQuery();

        return $query
            ->when(ProjectArchivingFeature::active(), fn (Builder $query): Builder => $query->withoutArchived());
    }

    /**
     * @return Builder<Project>
     */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        /** @var Builder<Project> $query */
        $query = parent::getGlobalSearchEloquentQuery();

        return $query
            ->when(ProjectArchivingFeature::active(), fn (Builder $query): Builder => $query->withoutArchived());
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        assert($record instanceof Project);

        return [
            'Department' => $record->department->name ?? 'N/A',
            'Start Date' => $record->start_date?->translatedFormat('M j, Y') ?? 'N/A',
            'Target Go-Live' => $record->target_completion_date?->translatedFormat('M j, Y') ?? 'Indefinite',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string | Htmlable
    {
        assert($record instanceof Project);

        return new HtmlString(view('project::filament.resources.projects.global-search-title', [
            'icon' => $record->icon,
            'name' => $record->name,
            'color' => $record->color?->value,
        ])->render());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'view' => ViewProject::route('/{record}'),
            'edit' => EditProject::route('/{record}/edit'),
            'manage-managers' => ManageManagers::route('/{record}/managers'),
            'manage-auditors' => ManageAuditors::route('/{record}/auditors'),
            'manage-files' => ManageFiles::route('/{record}/files'),
            'pipelines' => ManagePipelines::route('/{record}/pipelines'),
            'manage-milestones' => ManageMilestones::route('/{record}/milestones'),
            'manage-guests' => ManageGuests::route('/{record}/guests'),
        ];
    }
}
