<?php

use AidingApp\ServiceManagement\Enums\IncidentSeverityColorOptions;
use AidingApp\ServiceManagement\Enums\SystemIncidentStatusClassification;
use AidingApp\ServiceManagement\Models\IncidentSeverity;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Tenant::all()->each(function (Tenant $tenant) {
            $tenant->makeCurrent();

            foreach ([
                ['name' => 'Critical', 'color' => IncidentSeverityColorOptions::Danger->value],
                ['name' => 'Major', 'color' => IncidentSeverityColorOptions::Danger->value],
                ['name' => 'Minor', 'color' => IncidentSeverityColorOptions::Gray->value],
                ['name' => 'Warning', 'color' => IncidentSeverityColorOptions::Warning->value],
                ['name' => 'Informational', 'color' => IncidentSeverityColorOptions::Info->value],
            ] as $severity) {
                IncidentSeverity::firstOrCreate(['name' => $severity['name']], $severity);
            }

            foreach ([
                ['classification' => SystemIncidentStatusClassification::Open, 'name' => 'Identified'],
                ['classification' => SystemIncidentStatusClassification::Open, 'name' => 'Investigating'],
                ['classification' => SystemIncidentStatusClassification::Open, 'name' => 'Mitigating'],
                ['classification' => SystemIncidentStatusClassification::Open, 'name' => 'Monitoring'],
                ['classification' => SystemIncidentStatusClassification::Resolved, 'name' => 'Resolved'],
            ] as $status) {
                IncidentStatus::firstOrCreate(['name' => $status['name']], $status);
            }

            app(Tenant::class)::forgetCurrent();
        });
    }
};
