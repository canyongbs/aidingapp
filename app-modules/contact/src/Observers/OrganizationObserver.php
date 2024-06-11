<?php

namespace AidingApp\Contact\Observers;

use App\Models\User;
use AidingApp\Contact\Models\Organization;

class OrganizationObserver
{
    /**
     * Handle the Organization "saving" event.
     */
    public function saving(Organization $organization): void
    {
        $user = auth()->user();

        if ($user instanceof User && ! $organization->createdBy) {
            $organization->createdBy()->associate($user);
        }
    }
}
