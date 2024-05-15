<?php

namespace App\Models\Scopes;

use App\Models\Contracts\HasTags;
use Illuminate\Database\Eloquent\Builder;

class TagsForClass
{
    public function __construct(
        protected HasTags $class
    ) {}

    public function __invoke(Builder $query): void
    {
        $query->tap(new TagsForType($this->class::getTagType()));
    }
}
