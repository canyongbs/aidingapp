<?php

namespace AidingApp\Contact\Observers;

use AidingApp\Contact\Models\Organization;

class OrganizationObserver
{
    /**
     * Handle the Organization "creating" event.
     */
    public function creating(Organization $organization): void
    {
        $user = auth()->user();

        $organization->createdBy()->associate($user);
    }
}
