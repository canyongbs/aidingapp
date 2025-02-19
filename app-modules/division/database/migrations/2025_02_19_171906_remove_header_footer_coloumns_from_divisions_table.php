<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('divisions')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->dropColumn(['header', 'footer']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->longText('header')->nullable();
            $table->longText('footer')->nullable();
        });
    }
};
