<?php

use AidingApp\ServiceManagement\Enums\SystemIncidentStatusClassification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    private array $incidentSeverities = [
        ['name' => 'Critical'],
        ['name' => 'Major'],
        ['name' => 'Minor'],
        ['name' => 'Warning'],
        ['name' => 'Informational'],
    ];

    private array $incidentStatuses = [
        ['classification' => SystemIncidentStatusClassification::Open, 'name' => 'Identified'],
        ['classification' => SystemIncidentStatusClassification::Open, 'name' => 'Investigating'],
        ['classification' => SystemIncidentStatusClassification::Open, 'name' => 'Mitigating'],
        ['classification' => SystemIncidentStatusClassification::Open, 'name' => 'Monitoring'],
        ['classification' => SystemIncidentStatusClassification::Resolved, 'name' => 'Resolved'],
    ];

    public function up(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        DB::table('incident_severities')->insert(collect($this->incidentSeverities)->map(function ($item) {
            $item['id'] = Str::uuid()->toString();
            $item['created_at'] = now();

            return $item;
        })->toArray());

        DB::table('incident_statuses')->insert(collect($this->incidentStatuses)->map(function ($item) {
            $item['id'] = Str::uuid()->toString();
            $item['created_at'] = now();

            return $item;
        })->toArray());
    }

    public function down(): void
    {
        DB::table('incident_severities')
            ->whereIn('name', collect($this->incidentSeverities)->pluck('name'))
            ->delete();

        DB::table('incident_statuses')
            ->whereIn('name', collect($this->incidentStatuses)->pluck('name'))
            ->delete();
    }
};
