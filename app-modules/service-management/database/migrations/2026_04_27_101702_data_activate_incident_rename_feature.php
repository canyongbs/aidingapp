<?php

use App\Features\IncidentRenameFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        IncidentRenameFeature::activate();
    }

    public function down(): void
    {
        IncidentRenameFeature::deactivate();
    }
};
