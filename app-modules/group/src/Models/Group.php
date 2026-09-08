<?php

namespace AidingApp\Group\Models;

use AidingApp\Group\Database\Factories\GroupFactory;
use AidingApp\Group\Observers\GroupObserver;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([GroupObserver::class])]
class Group extends Model
{
    /** @use HasFactory<GroupFactory> */
    use HasFactory;

    use HasUuids;

    protected $fillable = [
        'name',
        'description',
    ];

    /** @return BelongsTo<User, $this> */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /** @return BelongsToMany<User, $this, GroupUser> */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->using(GroupUser::class)
            ->withPivot('id')
            ->withTimestamps();
    }
}
