<?php

namespace AidingApp\KnowledgeBase\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\KnowledgeBase\Enums\SystemIncidentStatusClassification;
use AidingApp\Team\Models\Team;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperIncident
 */
class Incident extends BaseModel implements Auditable
{
    use AuditableTrait;
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'title',
        'description',
        'severity_id',
        'status_id',
        'assigned_team_id',
    ];

    protected $casts = [
        'classification' => SystemIncidentStatusClassification::class,
    ];

    public function assignedTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'assigned_team_id', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(IncidentStatus::class);
    }

    public function severity(): BelongsTo
    {
        return $this->belongsTo(IncidentSeverity::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
