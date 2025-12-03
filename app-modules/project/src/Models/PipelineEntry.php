<?php

namespace AidingApp\Project\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PipelineEntry extends Model
{
    /** @use HasFactory<PipelineEntryFactory> */
    use HasFactory;

    use HasUuids;

    protected $table = 'pipeline_entries';

    protected $fillable = [
        'name',
        'pipeline_stage_id',
        'organizable_id',
        'organizable_type',
    ];

    /**
     * @return BelongsTo<PipelineStage, $this>
     */
    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function organizable(): MorphTo
    {
        return $this->morphTo();
    }
}
