<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('product_licenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products');
            $table->longText('license');
            $table->longText('description')->nullable();
            $table->foreignUuid('assigned_to')->nullable()->constrained('contacts');
            $table->date('start_date');
            $table->date('expiration_date')->nullable();
            $table->foreignUuid('created_by_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_licenses');
    }
};
