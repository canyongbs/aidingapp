<?php

namespace AidingApp\ServiceManagement\Observers;

use AidingApp\ServiceManagement\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "creating" event.
     */
    public function creating(Product $product): void
    {
        $product->created_by_id = auth()->user()?->getKey();
    }

}
