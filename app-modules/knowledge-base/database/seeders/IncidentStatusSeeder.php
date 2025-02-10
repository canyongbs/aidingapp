<?php

namespace AidingApp\KnowledgeBase\Database\Seeders;

use AidingApp\KnowledgeBase\Enums\SystemIncidentStatusClassification;
use AidingApp\KnowledgeBase\Models\IncidentStatus;
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
