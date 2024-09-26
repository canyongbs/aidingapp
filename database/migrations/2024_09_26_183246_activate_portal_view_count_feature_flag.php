<?php

use App\Features\PortalViewCount;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        PortalViewCount::activate();
    }

    public function down(): void
    {
        PortalViewCount::deactivate();
    }
};
