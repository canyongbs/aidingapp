<?php

use App\Features\ServiceRequestClosedNotificationColumns;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ServiceRequestClosedNotificationColumns::activate();
    }

    public function down(): void
    {
        ServiceRequestClosedNotificationColumns::deactivate();
    }
};
