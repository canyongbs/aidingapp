<?php

namespace AidingApp\Group\Observers;

use AidingApp\Group\Models\Group;
use App\Models\User;

class GroupObserver
{
    public function creating(Group $group): void
    {
        if ($group->createdBy instanceof User) {
            return;
        }

        $user = auth()->user();
        assert($user instanceof User);

        $group->createdBy()->associate($user);
    }
}
