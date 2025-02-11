<?php

namespace AidingApp\ServiceManagement\Database\Seeders;

use AidingApp\KnowledgeBase\Enums\IncidentSeverityColorOptions;
use AidingApp\KnowledgeBase\Models\IncidentSeverity;
use Illuminate\Database\Seeder;

class IncidentSeveritySeeder extends Seeder
{
    public function run(): void
    {
        IncidentSeverity::factory()
            ->createMany(
                [
                    [
                        'name' => 'Critical',
                        'color' => IncidentSeverityColorOptions::Danger->value,
                    ],
                    [
                        'name' => 'Major',
                        'color' => IncidentSeverityColorOptions::Danger->value,
                    ],
                    [
                        'name' => 'Minor',
                        'color' => IncidentSeverityColorOptions::Gray->value,
                    ],
                    [
                        'name' => 'Warning',
                        'color' => IncidentSeverityColorOptions::Warning->value,
                    ],
                    [
                        'name' => 'Informational',
                        'color' => IncidentSeverityColorOptions::Info->value,
                    ],
                ]
            );
    }
}
