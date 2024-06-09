<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->nullable();
            $table->text('phone_number')->nullable();
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->foreignUuid('industry_id')->nullable()->references('id')->on('organization_industries');
            $table->foreignUuid('type_id')->nullable()->references('id')->on('organization_types');
            $table->longText('description')->nullable();
            $table->integer('number_of_employees')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postalcode')->nullable();
            $table->string('country')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->foreignUuid('created_by_id')->nullable()->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
