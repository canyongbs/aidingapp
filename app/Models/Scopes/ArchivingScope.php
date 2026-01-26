<?php

namespace App\Models\Scopes;

use App\Features\ServiceRequestFormAndTypeArchivingFeature;
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
            if (! ServiceRequestFormAndTypeArchivingFeature::active()) {
                return;
            }

            return $builder->withoutGlobalScope(ArchivingScope::class);
        });
    }

    /**
     * @param Builder<*>  $builder
     */
    protected function addWithoutArchived(Builder $builder): void
    {
        $builder->macro('withoutArchived', function (Builder $builder) {
            if (! ServiceRequestFormAndTypeArchivingFeature::active()) {
                return;
            }

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
            if (! ServiceRequestFormAndTypeArchivingFeature::active()) {
                return;
            }

            $model = $builder->getModel();

            $builder->withoutGlobalScope(ArchivingScope::class)->whereNotNull(
                method_exists($model, 'getQualifiedArchivedAtColumn') ? $model->getQualifiedArchivedAtColumn() : $model->qualifyColumn('archived_at'),
            );

            return $builder;
        });
    }
}
