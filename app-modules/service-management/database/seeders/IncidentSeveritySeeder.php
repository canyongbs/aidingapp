<?php

namespace AidingApp\ServiceManagement\Database\Seeders;

use AidingApp\ServiceManagement\Models\IncidentSeverity;
use Illuminate\Database\Seeder;

class IncidentSeveritySeeder extends Seeder
{
    public function run(): void
    {
        IncidentSeverity::factory()
            ->createMany(
                [
                    ['name' => 'Critical', 'color' => 'red'],
                    ['name' => 'Major', 'color' => 'orange'],
                    ['name' => 'Minor', 'color' => 'yellow'],
                    ['name' => 'Warning', 'color' => 'amber'],
                    ['name' => 'Informational', 'color' => 'blue'],
                ]
            );
    }
}
