<?php

namespace App\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

interface HasTags
{
    public static function getTagType(): string;

    public static function getTagLabel(): string;

    public function tags(): MorphToMany;
}
