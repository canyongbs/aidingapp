<?php

use App\Features\TaskConfidential;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        TaskConfidential::activate();
    }

    public function down(): void
    {
        TaskConfidential::deactivate();
    }
};
