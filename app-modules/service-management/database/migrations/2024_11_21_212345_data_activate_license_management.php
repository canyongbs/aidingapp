<?php

use App\Features\LicenseManagement;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        LicenseManagement::activate();
    }

    public function down(): void
    {
        LicenseManagement::deactivate();
    }
};
