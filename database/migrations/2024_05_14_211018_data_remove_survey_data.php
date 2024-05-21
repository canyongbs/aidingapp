<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('survey_field_submission');
        Schema::dropIfExists('survey_submissions');
        Schema::dropIfExists('survey_authentications');
        Schema::dropIfExists('survey_fields');
        Schema::dropIfExists('survey_steps');
        Schema::dropIfExists('surveys');
    }

    public function down(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('embed_enabled')->default(false);
            $table->json('allowed_domains')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('rounding')->nullable();
            $table->boolean('is_authenticated')->default(false);
            $table->boolean('is_wizard')->default(false);
            $table->boolean('recaptcha_enabled')->default(false);
            $table->json('content')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('survey_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('label');
            $table->json('content')->nullable();
            $table->foreignUuid('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->integer('sort');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('survey_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('label');
            $table->text('type');
            $table->boolean('is_required');
            $table->json('config');

            $table->foreignUuid('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->foreignUuid('step_id')->nullable()->constrained('survey_steps')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('survey_authentications', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('author_id')->nullable();
            $table->string('author_type')->nullable();
            $table->string('code')->nullable();
            $table->foreignUuid('survey_id')->constrained('surveys')->cascadeOnDelete();

            $table->timestamps();

            $table->index(['author_type', 'author_id']);
        });

        Schema::create('survey_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->string('author_id')->nullable();
            $table->string('author_type')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->string('request_method')->nullable();
            $table->text('request_note')->nullable();
            $table->foreignUuid('requester_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['author_type', 'author_id']);
        });

        Schema::create('survey_field_submission', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->longText('response');
            $table->foreignUuid('field_id')->constrained('survey_fields')->cascadeOnDelete();
            $table->foreignUuid('submission_id')->constrained('survey_submissions')->cascadeOnDelete();

            $table->timestamps();
        });
    }
};