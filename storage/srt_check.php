<?php

use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;

echo json_encode([
    'types' => ServiceRequestType::query()->count(),
    'active' => ServiceRequestType::query()->withoutArchived()->count(),
    'categories' => ServiceRequestTypeCategory::query()->count(),
    'uncategorized' => ServiceRequestType::query()->withoutArchived()->whereDoesntHave('categories')->count(),
]) . PHP_EOL;
