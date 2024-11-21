<?php

namespace AidingApp\ServiceManagement\Observers;

use AidingApp\ServiceManagement\Models\ProductLicense;

class ProductLicenseObserver
{
    /**
     * Handle the Product "creating" event.
     */
    public function creating(ProductLicense $productLicense): void
    {
        $productLicense->created_by_id = auth()->user()?->getKey();
    }
}
