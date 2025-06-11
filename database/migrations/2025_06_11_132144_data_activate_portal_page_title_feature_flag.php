<?php

use App\Features\PortalPageTitle;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        PortalPageTitle::activate();
    }

    public function down(): void
    {
        PortalPageTitle::deactivate();
    }
};
