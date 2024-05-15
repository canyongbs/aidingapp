<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;

class TagsForType
{
    public function __construct(
        protected string $type
    ) {}

    public function __invoke(Builder $query): void
    {
        $query->where('type', $this->type);
    }
}
