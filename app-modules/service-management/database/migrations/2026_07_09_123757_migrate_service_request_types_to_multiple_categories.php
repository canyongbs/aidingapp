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
      of the licensor in the software. Any use of the licensor’s trademarks is subject
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

use App\Features\ServiceRequestTypeMultipleCategoriesFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            // Create the pivot table that allows a service request type to belong to many categories.
            Schema::create('service_request_category_types', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('service_request_type_id')
                    ->constrained('service_request_types', indexName: 'srct_service_request_type_id_foreign')
                    ->cascadeOnDelete();
                $table->foreignUuid('service_request_type_category_id')
                    ->constrained('service_request_type_categories', indexName: 'srct_service_request_type_category_id_foreign')
                    ->cascadeOnDelete();
                $table->unsignedInteger('sort')->default(0);
                $table->timestamps();

                $table->unique(
                    ['service_request_type_id', 'service_request_type_category_id'],
                    'srct_type_category_unique',
                );
            });

            // Backfill the pivot from the existing single category assignment, preserving the
            // type's existing order as the per-area sort value.
            DB::table('service_request_category_types')->insertUsing(
                ['id', 'service_request_type_id', 'service_request_type_category_id', 'sort', 'created_at', 'updated_at'],
                DB::table('service_request_types')
                    ->whereNotNull('category_id')
                    ->select([
                        DB::raw('gen_random_uuid()'),
                        'id',
                        'category_id',
                        'sort',
                        DB::raw('now()'),
                        DB::raw('now()'),
                    ]),
            );

            // Activate the feature flag now that the pivot is populated.
            ServiceRequestTypeMultipleCategoriesFeature::activate();

            // Drop the now-redundant single category column.
            Schema::table('service_request_types', function (Blueprint $table) {
                $table->dropConstrainedForeignId('category_id');
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            ServiceRequestTypeMultipleCategoriesFeature::deactivate();

            Schema::table('service_request_types', function (Blueprint $table) {
                $table->foreignUuid('category_id')
                    ->nullable()
                    ->constrained('service_request_type_categories');
            });

            // Restore the single category from the first pivot assignment.
            DB::statement(<<<'SQL'
                update service_request_types
                set category_id = (
                    select service_request_type_category_id
                    from service_request_category_types
                    where service_request_category_types.service_request_type_id = service_request_types.id
                    limit 1
                )
                SQL);

            Schema::dropIfExists('service_request_category_types');
        });
    }
};
