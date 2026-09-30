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

use App\Features\KnowledgeBasePortalStableUrlsFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    private const int PUBLIC_ID_LENGTH = 8;

    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('knowledge_base_categories', function (Blueprint $table) {
                $table->string('public_id', self::PUBLIC_ID_LENGTH)->nullable();
            });

            Schema::table('knowledge_base_articles', function (Blueprint $table) {
                $table->string('public_id', self::PUBLIC_ID_LENGTH)->nullable();
            });

            // TODO: Cleanup Task (knowledge-base-portal-stable-urls): remove this one-time backfill, its method, and the Str import from the retained migration.
            $this->backfillPublicIds('knowledge_base_categories');
            $this->backfillPublicIds('knowledge_base_articles');

            Schema::table('knowledge_base_categories', function (Blueprint $table) {
                $table->string('public_id', self::PUBLIC_ID_LENGTH)->nullable(false)->change();
                $table->uniqueIndex('public_id');
            });

            Schema::table('knowledge_base_articles', function (Blueprint $table) {
                $table->string('public_id', self::PUBLIC_ID_LENGTH)->nullable(false)->change();
                $table->uniqueIndex('public_id');
            });

            KnowledgeBasePortalStableUrlsFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            KnowledgeBasePortalStableUrlsFeature::deactivate();

            Schema::table('knowledge_base_categories', function (Blueprint $table) {
                $table->dropIndex('knowledge_base_categories_public_id_unique');
                $table->dropColumn('public_id');
            });

            Schema::table('knowledge_base_articles', function (Blueprint $table) {
                $table->dropIndex('knowledge_base_articles_public_id_unique');
                $table->dropColumn('public_id');
            });
        });
    }

    private function backfillPublicIds(string $table): void
    {
        $usedPublicIds = DB::table($table)
            ->whereNotNull('public_id')
            ->pluck('public_id')
            ->mapWithKeys(fn (string $publicId): array => [$publicId => true])
            ->all();

        DB::table($table)
            ->whereNull('public_id')
            ->select('id')
            ->eachById(function (object $record) use ($table, &$usedPublicIds): void {
                do {
                    $publicId = Str::random(self::PUBLIC_ID_LENGTH);
                } while (isset($usedPublicIds[$publicId]));

                DB::table($table)
                    ->where('id', $record->id)
                    ->whereNull('public_id')
                    ->update(['public_id' => $publicId]);

                $usedPublicIds[$publicId] = true;
            });
    }
};
