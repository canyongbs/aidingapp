<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ArchivingScope implements Scope
{
    /**
     * @var array<string>
     */
    protected array $extensions = ['Archive', 'Unarchive', 'WithArchived', 'WithoutArchived', 'OnlyArchived'];

    /**
     * @template TModel of Model
     *
     * @param Builder<TModel> $builder
     * @param  TModel  $model
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->whereNull(method_exists($model, 'getQualifiedArchivedAtColumn') ? $model->getQualifiedArchivedAtColumn() : $model->qualifyColumn('archived_at'));
    }

    /**
     * @param Builder<*>  $builder
     */
    public function extend(Builder $builder): void
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * @param Builder<*>  $builder
     */
    protected function addArchive(Builder $builder): void
    {
        $builder->macro('archive', function (Builder $builder) {
            $model = $builder->getModel();

            $time = $model->freshTimestamp();

            return $builder->update([(method_exists($model, 'getArchivedAtColumn') ? $model->getArchivedAtColumn() : 'archived_at') => $model->fromDateTime($time)]);
        });
    }

    /**
     * @param Builder<*>  $builder
     */
    protected function addUnarchive(Builder $builder): void
    {
        $builder->macro('unarchive', function (Builder $builder) {
            $builder->withoutGlobalScope(ArchivingScope::class);

            $model = $builder->getModel();

            return $builder->update([(method_exists($model, 'getArchivedAtColumn') ? $model->getArchivedAtColumn() : 'archived_at') => null]);
        });
    }

    /**
     * @param Builder<*>  $builder
     */
    protected function addWithArchived(Builder $builder): void
    {
        $builder->macro('withArchived', function (Builder $builder) {
            return $builder->withoutGlobalScope(ArchivingScope::class);
        });
    }

    /**
     * @param Builder<*>  $builder
     */
    protected function addWithoutArchived(Builder $builder): void
    {
        $builder->macro('withoutArchived', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope(ArchivingScope::class)->whereNull(
                method_exists($model, 'getQualifiedArchivedAtColumn') ? $model->getQualifiedArchivedAtColumn() : $model->qualifyColumn('archived_at'),
            );

            return $builder;
        });
    }

    /**
     * @param Builder<*>  $builder
     */
    protected function addOnlyArchived(Builder $builder): void
    {
        $builder->macro('onlyArchived', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope(ArchivingScope::class)->whereNotNull(
                method_exists($model, 'getQualifiedArchivedAtColumn') ? $model->getQualifiedArchivedAtColumn() : $model->qualifyColumn('archived_at'),
            );

            return $builder;
        });
    }
}
