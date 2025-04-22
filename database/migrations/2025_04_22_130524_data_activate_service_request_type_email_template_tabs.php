<?php

use App\Features\ServiceRequestTypeEmailTemplateTabs;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ServiceRequestTypeEmailTemplateTabs::activate();
    }

    public function down(): void
    {
        ServiceRequestTypeEmailTemplateTabs::deactivate();
    }
};
