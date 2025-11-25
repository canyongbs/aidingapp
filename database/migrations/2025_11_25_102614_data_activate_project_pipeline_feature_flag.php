<?php

use App\Features\ProjectPipelineFeature;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        ProjectPipelineFeature::activate();
    }

    public function down(): void
    {
        ProjectPipelineFeature::deactivate();
    }
};
