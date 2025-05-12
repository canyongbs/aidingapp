<?php

use App\Features\AssetCount;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        AssetCount::activate();
    }

    public function down(): void
    {
        AssetCount::deactivate();
    }
};
