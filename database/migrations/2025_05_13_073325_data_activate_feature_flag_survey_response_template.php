<?php

use App\Features\SurveyResponseTemplate;
use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        SurveyResponseTemplate::activate();
    }

    public function down(): void
    {
        SurveyResponseTemplate::deactivate();
    }
};
