<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor's trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Features\PipelineEntryEnhancedFieldsFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('pipeline_entries', function (Blueprint $table) {
                $table->string('assigned_to_type')->nullable()->after('due');
                $table->uuid('assigned_to_id')->nullable()->after('assigned_to_type');
                $table->index(['assigned_to_type', 'assigned_to_id']);
            });

            DB::table('pipeline_entries')
                ->whereNotNull('assigned_to')
                ->update([
                    'assigned_to_type' => 'user',
                    'assigned_to_id' => DB::raw('"assigned_to"'),
                ]);

            Schema::table('pipeline_entries', function (Blueprint $table) {
                $table->dropForeign(['assigned_to']);
                $table->dropColumn('assigned_to');
            });

            Schema::table('pipeline_entries', function (Blueprint $table) {
                $table->dropForeign(['related_to']);
                $table->dropColumn('related_to');
            });

            Schema::table('pipeline_entries', function (Blueprint $table) {
                $table->boolean('is_visible_to_guests')->default(true);
            });

            Schema::create('pipeline_entry_milestones', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('pipeline_entry_id')->constrained()->cascadeOnDelete();
                $table->foreignUuid('project_milestone_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });

            Schema::create('pipeline_entry_assets', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('pipeline_entry_id')->constrained()->cascadeOnDelete();
                $table->foreignUuid('asset_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });

            Schema::create('pipeline_entry_service_requests', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('pipeline_entry_id')->constrained()->cascadeOnDelete();
                $table->foreignUuid('service_request_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });

            PipelineEntryEnhancedFieldsFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            PipelineEntryEnhancedFieldsFeature::deactivate();

            Schema::dropIfExists('pipeline_entry_service_requests');
            Schema::dropIfExists('pipeline_entry_assets');
            Schema::dropIfExists('pipeline_entry_milestones');

            Schema::table('pipeline_entries', function (Blueprint $table) {
                $table->dropColumn('is_visible_to_guests');
            });

            Schema::table('pipeline_entries', function (Blueprint $table) {
                $table->foreignUuid('related_to')->nullable()->constrained('contacts')->nullOnDelete();
            });

            Schema::table('pipeline_entries', function (Blueprint $table) {
                $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            });

            DB::table('pipeline_entries')
                ->whereNotNull('assigned_to_id')
                ->where('assigned_to_type', 'user')
                ->update([
                    'assigned_to' => DB::raw('"assigned_to_id"'),
                ]);

            Schema::table('pipeline_entries', function (Blueprint $table) {
                $table->dropIndex(['assigned_to_type', 'assigned_to_id']);
                $table->dropColumn(['assigned_to_type', 'assigned_to_id']);
            });
        });
    }
};
