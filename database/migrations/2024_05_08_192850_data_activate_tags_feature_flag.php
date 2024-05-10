<?php

use Laravel\Pennant\Feature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::activate('tags');
    }

    public function down(): void
    {
        Feature::deactivate('tags');
    }
};
