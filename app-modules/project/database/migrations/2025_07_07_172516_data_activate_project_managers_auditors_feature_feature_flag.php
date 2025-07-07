<?php

use App\Features\ProjectManagersAuditorsFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ProjectManagersAuditorsFeature::activate();
    }

    public function down(): void
    {
        ProjectManagersAuditorsFeature::deactivate();
    }
};
