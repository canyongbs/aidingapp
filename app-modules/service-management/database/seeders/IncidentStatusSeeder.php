<?php

namespace AidingApp\ServiceManagement\Database\Seeders;

use AidingApp\ServiceManagement\Enums\SystemIncidentStatusClassification;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use Illuminate\Database\Seeder;

class IncidentStatusSeeder extends Seeder
{
    public function run(): void
    {
        IncidentStatus::factory()
            ->createMany(
                [
                    [
                        'classification' => SystemIncidentStatusClassification::Open,
                        'name' => 'Identified',
                    ],
                    [
                        'classification' => SystemIncidentStatusClassification::Open,
                        'name' => 'Investigating',
                    ],
                    [
                        'classification' => SystemIncidentStatusClassification::Open,
                        'name' => 'Mitigating',
                    ],
                    [
                        'classification' => SystemIncidentStatusClassification::Open,
                        'name' => 'Monitoring',
                    ],
                    [
                        'classification' => SystemIncidentStatusClassification::Resolved,
                        'name' => 'Resolved',
                    ],
                ]
            );
    }
}
