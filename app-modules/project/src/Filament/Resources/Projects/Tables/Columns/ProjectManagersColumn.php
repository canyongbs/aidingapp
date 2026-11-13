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

namespace AidingApp\Project\Filament\Resources\Projects\Tables\Columns;

use AidingApp\Project\Models\Project;
use App\Models\Scopes\WhereFullNameLike;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class ProjectManagersColumn
{
    protected const VISIBLE_MANAGER_LIMIT = 3;

    public static function make(): ImageColumn
    {
        $getManagerName = fn (Project $record, string $state): ?string => $record->allManagers()
            ->find(Str::afterLast($state, '#'))
            ?->full_name;

        return ImageColumn::make('managers')
            ->label('Manager(s)')
            ->state(fn (Project $record): array => static::getManagers($record)
                ->map(fn (User $manager): string => static::getAvatarUrl($manager))
                ->all())
            ->circular()
            ->stacked()
            ->ring(2)
            ->limit(static::VISIBLE_MANAGER_LIMIT)
            ->limitedRemainingText()
            ->limitedRemainingTextTooltip(fn (Project $record): string => static::getManagers($record)
                ->skip(static::VISIBLE_MANAGER_LIMIT)
                ->pluck('full_name')
                ->implode(', '))
            ->tooltip($getManagerName)
            ->alt($getManagerName)
            ->placeholder('N/A')
            ->searchable(query: function (Builder $query, string $search): Builder {
                return $query
                    ->whereHas(
                        'managerUsers',
                        fn (Builder $query) => $query->tap(new WhereFullNameLike($search)),
                    )
                    ->orWhereHas(
                        'managerDepartments.users',
                        fn (Builder $query) => $query->tap(new WhereFullNameLike($search)),
                    );
            });
    }

    /**
     * Managers can share an avatar URL, so their key is appended as a URL fragment to identify
     * which manager each image belongs to. Browsers ignore the fragment when loading the image.
     */
    public static function getAvatarUrl(User $manager): string
    {
        return Filament::getUserAvatarUrl($manager) . '#' . $manager->getKey();
    }

    /**
     * @return Collection<int, User>
     */
    protected static function getManagers(Project $record): Collection
    {
        return $record->allManagers()
            ->sortBy('full_name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }
}
