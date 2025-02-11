<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->longText('description');
            $table->foreignUuid('severity_id')->references('id')->on('incident_severities')->onDelete('cascade');
            $table->foreignUuid('status_id')->references('id')->on('incident_statuses')->onDelete('cascade');
            $table->foreignUuid('assigned_team_id')->nullable()->references('id')->on('teams')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
