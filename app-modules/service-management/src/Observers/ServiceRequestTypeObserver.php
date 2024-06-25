<?php

namespace AidingApp\ServiceManagement\Observers;

use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Illuminate\Support\Facades\Log;

class ServiceRequestTypeObserver
{
    public function deleted(ServiceRequestType $serviceRequestType): void
    {
        $serviceRequestType->priorities()->delete();
    }

    // It seems that trashed does not currently get called by Laravel. Investigate at some other time as they may be a Laravel bug, or it may be a L11 feature that is in the docs but doesn't work in L10
    public function trashed(ServiceRequestType $serviceRequestType): void
    {
        $serviceRequestType->priorities()->delete();
    }

    public function restored(ServiceRequestType $serviceRequestType): void
    {
        $serviceRequestType->priorities()->restore();
    }
}
