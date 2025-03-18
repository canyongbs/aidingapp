<?php

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Team\Models\Team;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServiceMonitoringTarget extends BaseModel
{
    use HasFactory;

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
