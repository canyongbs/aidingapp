<?php

namespace AidingApp\Contact\Observers;

use AidingApp\Contact\Models\Organization;
use App\Models\User;

class OrganizationObserver
{
    /**
     * Handle the Organization "creating" event.
     */
    public function creating(Organization $organization): void
    {
        $user = auth()->user();

        ($user instanceof User) ? $organization->createdBy()->associate($user) : '';
    }
}
