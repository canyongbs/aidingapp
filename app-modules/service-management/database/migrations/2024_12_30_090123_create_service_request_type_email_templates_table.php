<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('service_request_type_email_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_request_type_id')->constrained('service_request_types');
            $table->enum('type', ['created', 'assigned', 'update', 'status-change', 'closed', 'survey-response'])->default('created');
            $table->unique(['service_request_type_id', 'type']);
            $table->json('subject');
            $table->json('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_request_type_email_templates');
    }
};
