<?php

use App\Features\ProloadServiceRequestTypeFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ProloadServiceRequestTypeFeature::activate();
    }

    public function down(): void
    {
        ProloadServiceRequestTypeFeature::deactivate();
    }
};
