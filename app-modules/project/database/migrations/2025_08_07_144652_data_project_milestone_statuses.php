<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;
use AidingApp\Project\Database\Seeders\ProjectMilestoneStatusSeeder;

return new class extends Migration
{
    public function up(): void
    {
        DB::seed(ProjectMilestoneStatusSeeder::class);
    }

    public function down(): void
    {

    }
};
