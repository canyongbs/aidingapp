<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ModifySoftDeletableSelectQuery
{
    /**
     * @param Builder<Model> $query
     *
     * @return Builder<Model>
     */
    public function __invoke(Builder $query, ?Model $record, Select $component): Builder
    {
        return $query->where(
            fn (Builder $query) => $query /** @phpstan-ignore method.notFound */
                ->withoutTrashed()
                ->orWhere(
                    $component->getRelationship()->getQualifiedOwnerKeyName(),
                    $record?->getAttributeValue($component->getRelationship()->getForeignKeyName()),
                ),
        );
    }
}
