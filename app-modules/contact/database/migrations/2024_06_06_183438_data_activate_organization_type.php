<?php

use Illuminate\Database\Migrations\Migration;
use Laravel\Pennant\Feature;

return new class extends Migration
{
    public function up(): void
    {
        Feature::activate('organization-type');
    }

    public function down(): void
    {
        Feature::deactivate('organization-type');
    }
};