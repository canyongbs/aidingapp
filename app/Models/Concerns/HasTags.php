<?php

namespace App\Models\Concerns;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTags
{
    public static function getTagType(): string
    {
        return (new self())->getMorphClass();
    }

    public static function getTagLabel(): string
    {
        return str((new self())->getMorphClass())->classBasename()->headline();
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
