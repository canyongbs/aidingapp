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
                    ['name' => 'Critical'],
                    ['name' => 'Major'],
                    ['name' => 'Minor'],
                    ['name' => 'Warning'],
                    ['name' => 'Informational'],
                ]
            );
    }
}
