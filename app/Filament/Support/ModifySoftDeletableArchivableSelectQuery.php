<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ModifySoftDeletableArchivableSelectQuery
{
    public function __invoke(Builder $query, ?Model $record, Select $component): Builder
    {
        return $query->where(
            fn (Builder $query) => $query
                ->where(
                    fn (Builder $query) => $query
                        ->withoutTrashed()
                        ->withoutArchived(),
                )
                ->orWhere(
                    $component->getRelationship()->getQualifiedOwnerKeyName(),
                    $record?->getAttributeValue($component->getRelationship()->getForeignKeyName()),
                ),
        );
    }
}
