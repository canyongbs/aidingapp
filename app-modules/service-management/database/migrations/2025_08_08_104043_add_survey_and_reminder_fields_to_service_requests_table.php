<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->timestamp('survey_sent_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->boolean('is_reminders_enabled')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['survey_sent_at', 'reminder_sent_at', 'is_reminders_enabled']);
        });
    }
};
