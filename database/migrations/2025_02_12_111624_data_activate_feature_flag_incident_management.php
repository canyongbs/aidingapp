<?php

use App\Features\IncidentSeverityStatus;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        IncidentSeverityStatus::activate();
    }

    public function down(): void
    {
        IncidentSeverityStatus::deactivate();
    }
};
