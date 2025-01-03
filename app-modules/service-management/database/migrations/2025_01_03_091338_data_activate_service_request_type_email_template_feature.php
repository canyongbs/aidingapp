<?php

use App\Features\ServiceRequestTypeEmailTemplateFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ServiceRequestTypeEmailTemplateFeature::activate();
    }

    public function down(): void
    {
        ServiceRequestTypeEmailTemplateFeature::deactivate();
    }
};
