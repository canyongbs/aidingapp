<?php

use App\Features\SuperAdminRole;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        SuperAdminRole::activate();
    }

    public function down(): void
    {
        SuperAdminRole::deactivate();
    }
};
