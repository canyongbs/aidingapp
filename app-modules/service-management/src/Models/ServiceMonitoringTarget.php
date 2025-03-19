<?php

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\Team\Models\Team;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

class ServiceMonitoringTarget extends BaseModel implements Auditable
{
    use HasFactory;
    use AuditableTrait;

    protected $fillable = [
        'name',
        'description',
        'domain',
        'frequency',
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->using(ServiceMonitoringTargetTeam::class)
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class)
            ->using(ServiceMonitoringTargetUser::class)
            ->withTimestamps();
    }
}
