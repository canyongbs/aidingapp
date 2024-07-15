<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Pennant\Feature;

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
