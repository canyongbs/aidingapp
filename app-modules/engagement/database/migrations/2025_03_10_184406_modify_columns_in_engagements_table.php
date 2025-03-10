<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('engagements', function (Blueprint $table) {
            $table->dropColumn('scheduled');
            $table->dropColumn('deliver_at');
            $table->string('channel');
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('dispatched_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('engagements', function (Blueprint $table) {
            $table->boolean('scheduled')->default(false);
            $table->dateTime('deliver_at');
            $table->dropColumn('channel');
            $table->dropColumn('scheduled_at');
            $table->dropColumn('dispatched_at');
        });
    }
};
