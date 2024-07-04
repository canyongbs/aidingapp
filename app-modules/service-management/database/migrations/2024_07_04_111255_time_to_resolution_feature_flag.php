<?php

use Illuminate\Database\Migrations\Migration;
use Laravel\Pennant\Feature;

return new class extends Migration
{
    public function up(): void
    {
        Feature::activate('time_to_resolution');
    }

    public function down(): void
    {
        Feature::deactivate('time_to_resolution');
    }
};
