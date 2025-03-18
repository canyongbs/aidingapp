<?php

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Team\Models\Team;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ServiceMonitoringTargetTeam extends Pivot
{
    use HasUuids;
    use HasFactory;

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function serviceMonitoringTarget(): BelongsTo
    {
        return $this->belongsTo(ServiceMonitoringTarget::class);
    }
}
