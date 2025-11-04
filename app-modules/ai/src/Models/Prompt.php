<?php

namespace AidingApp\Ai\Models;

use AidingApp\Ai\Database\Factories\PromptFactory;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperPrompt
 */
class Prompt extends Model
{
    /** @use HasFactory<PromptFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'prompt',
        'type_id',
        'is_smart',
    ];

    protected $casts = [
        'is_smart' => 'boolean',
    ];
}
