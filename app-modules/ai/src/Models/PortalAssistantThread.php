<?php

namespace AidingApp\Ai\Models;

use App\Models\BaseModel;
use Database\Factories\PortalAssistantThreadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PortalAssistantThread extends BaseModel
{
    /** @use HasFactory<PortalAssistantThreadFactory> */
    use HasFactory;

    public $fillable = [
        'author_type',
        'author_id',
    ];

    /**
     * @return HasMany<PortalAssistantMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(PortalAssistantMessage::class, 'thread_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function author(): MorphTo
    {
        return $this->morphTo('author');
    }
}
