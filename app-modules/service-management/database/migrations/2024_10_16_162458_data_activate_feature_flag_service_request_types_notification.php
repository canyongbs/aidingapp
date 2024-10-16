<?php

use App\Features\ServiceRequestTypeNotifications;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        ServiceRequestTypeNotifications::activate();
    }

    public function down(): void
    {
        ServiceRequestTypeNotifications::deactivate();
    }
};
