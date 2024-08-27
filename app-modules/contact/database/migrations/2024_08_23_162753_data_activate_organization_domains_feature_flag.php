<?php

use Laravel\Pennant\Feature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Feature::activate('organization_domains');
    }

    public function down(): void
    {
        Feature::purge('organization_domains');
    }
};
