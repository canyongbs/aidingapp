<?php

namespace AidingApp\ServiceManagement\Models;

use AidingApp\ServiceManagement\Database\Factories\SecretFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Secret extends Model
{
    /** @use HasFactory<SecretFactory> */
    use HasFactory;

    use HasUuids;

    protected $fillable = [
        'value',
        'author_type',
        'author_id',
        'related_type',
        'related_id',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function author(): MorphTo
    {
        return $this->morphTo('author');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function related(): MorphTo
    {
        return $this->morphTo('related');
    }
}
