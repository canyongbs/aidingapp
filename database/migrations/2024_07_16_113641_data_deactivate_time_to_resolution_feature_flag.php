<?php

use Laravel\Pennant\Feature;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Feature::purge('time_to_resolution');
    }

    public function down(): void
    {
        Feature::activate('time_to_resolution');
    }
};
