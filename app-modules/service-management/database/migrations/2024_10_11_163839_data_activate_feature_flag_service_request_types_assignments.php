<?php

use Illuminate\Database\Migrations\Migration;
use App\Features\ServiceRequestTypeAssignments;

return new class () extends Migration {
    public function up(): void
    {
        ServiceRequestTypeAssignments::activate();
    }

    public function down(): void
    {
        ServiceRequestTypeAssignments::deactivate();
    }
};
