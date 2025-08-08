<?php

namespace AidingApp\Project\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectMilestone extends Model
{
    /** @use HasFactory<ProjectMilestoneFactory> */
    // use HasFactory;

    use HasUuids;
    use SoftDeletes;
    // use AuditableTrait;

    protected $fillable = [
        'title',
        'description',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<ProjectMilestoneStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectMilestoneStatus::class, 'status_id');
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
