<?php

namespace AidingApp\Task\Models;

use AidingApp\Task\Database\Factories\TaskConfidentialTeamFactory;
use AidingApp\Team\Models\Team;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperTaskConfidentialTeam
 */
class TaskConfidentialTeam extends Pivot
{
    /** @use HasFactory<TaskConfidentialTeamFactory> */
    use HasFactory;

    use HasUuids;

    /**
    * @return BelongsTo<Task, $this>
    */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
