<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('work_number')->nullable();
            $table->unsignedInteger('work_extension')->nullable();
            $table->string('mobile')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'work_number',
                'work_extension',
                'mobile',
            ]);
        });
    }
};
