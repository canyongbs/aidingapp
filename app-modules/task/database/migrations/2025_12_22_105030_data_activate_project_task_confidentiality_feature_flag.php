<?php

use AidingApp\Project\Models\Project;
use App\Features\ProjectTaskConfidentialityFeature;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        ProjectTaskConfidentialityFeature::activate();
    }

    public function down(): void
    {
        ProjectTaskConfidentialityFeature::deactivate();
    }
};
