<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Tag extends BaseModel
{
    protected $fillable = [
        'name',
        'type',
    ];

    public function scopeType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }

    /**
     * @param class-string $class
     */
    public function scopeClass(Builder $query, string $class): void
    {
        $query->where('type', app($class)::getTagType());
    }
}
