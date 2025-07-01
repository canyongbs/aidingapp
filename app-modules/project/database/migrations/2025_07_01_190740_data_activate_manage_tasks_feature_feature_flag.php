<?php

use App\Features\ManageTasksFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ManageTasksFeature::activate();
    }

    public function down(): void
    {
        ManageTasksFeature::deactivate();
    }
};
