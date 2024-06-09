<?php

namespace AidingApp\Contact\Observers;

use AidingApp\Contact\Models\OrganizationType;
use App\Models\User;

class OrganizationTypeObserver
{
    /**
     * Handle the OrganizationType "saving" event.
     */
    public function saving(OrganizationType $organizationType): void
    {
        $user = auth()->user();

        if ($user instanceof User && ! $organizationType->createdBy) {
            $organizationType->createdBy()->associate($user);
        }
        
        if ($organizationType->is_default) {
            OrganizationType::query()
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }

  
}
