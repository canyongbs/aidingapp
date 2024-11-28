<?php

use App\Features\RoundRobinId;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        RoundRobinId::activate();
    }

    public function down(): void
    {
        RoundRobinId::deactivate();
    }
};
