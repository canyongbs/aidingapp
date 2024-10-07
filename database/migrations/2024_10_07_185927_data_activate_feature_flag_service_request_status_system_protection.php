<?php

use Illuminate\Database\Migrations\Migration;
use App\Features\ServiceRequestStatusSystemProtection;

return new class () extends Migration {
    public function up(): void
    {
        ServiceRequestStatusSystemProtection::activate();
    }

    public function down(): void
    {
        ServiceRequestStatusSystemProtection::deactivate();
    }
};
