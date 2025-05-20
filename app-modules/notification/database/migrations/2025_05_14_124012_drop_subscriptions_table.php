<?php

use Illuminate\Database\Migrations\Migration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('subscriptions');
    }

    public function down(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('subscribable_id');
            $table->string('subscribable_type');

            $table->timestamps();

            $table->uniqueIndex(['user_id', 'subscribable_id', 'subscribable_type']);
        });
    }
};
