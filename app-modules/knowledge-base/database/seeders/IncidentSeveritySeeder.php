<?php

namespace AidingApp\KnowledgeBase\Database\Seeders;

use AidingApp\KnowledgeBase\Models\IncidentSeverity;
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
