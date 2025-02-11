<?php

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\Team\Models\Team;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperIncident
 */
class Incident extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'severity_id',
        'status_id',
        'assigned_team_id',
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
}
