<?php

use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use App\Features\PipelineEntryFeature;
use Illuminate\Database\Migrations\Migration;
use Livewire\Pipe;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        PipelineEntryFeature::activate();
    }

    public function down(): void
    {
        PipelineEntryFeature::deactivate();
    }
};
