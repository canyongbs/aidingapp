<?php

namespace AidingApp\Contact\Observers;

use AidingApp\Contact\Models\OrganizationIndustry;
use App\Models\User;

class OrganizationIndustryObserver
{
    /**
     * Handle the OrganizationIndustry "saving" event.
     */
    public function saving(OrganizationIndustry $organizationIndustry): void
    {
        $user = auth()->user();

        if ($user instanceof User && ! $organizationIndustry->createdBy) {
            $organizationIndustry->createdBy()->associate($user);
        }

        if ($organizationIndustry->is_default) {
            OrganizationIndustry::query()
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
