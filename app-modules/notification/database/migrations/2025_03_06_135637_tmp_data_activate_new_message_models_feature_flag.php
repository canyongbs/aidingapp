<?php

use App\Features\NewMessageModels;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        NewMessageModels::activate();
    }

    public function down(): void
    {
        NewMessageModels::deactivate();
    }
};
