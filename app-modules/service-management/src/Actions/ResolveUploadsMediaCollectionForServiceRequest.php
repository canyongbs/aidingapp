<?php

namespace AidingApp\ServiceManagement\Actions;

use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\MediaCollections\UploadsMediaCollection;

class ResolveUploadsMediaCollectionForServiceRequest
{
    public function __invoke(): UploadsMediaCollection
    {
        return app(ServiceRequest::class)->getMediaCollection('uploads');
    }
}
