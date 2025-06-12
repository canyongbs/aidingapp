<?php

use App\Features\AdditionalInfomationInPortalSettings;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        AdditionalInfomationInPortalSettings::activate();
    }

    public function down(): void
    {
        AdditionalInfomationInPortalSettings::deactivate();
    }
};
