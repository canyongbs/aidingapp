<?php

use App\Features\IncidentUpdateFeature;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        IncidentUpdateFeature::activate();
    }

    public function down(): void
    {
        IncidentUpdateFeature::deactivate();
    }
};
