<?php

use App\Features\SettingsPermissions;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        SettingsPermissions::activate();
    }

    public function down(): void
    {
        SettingsPermissions::deactivate();
    }
};
