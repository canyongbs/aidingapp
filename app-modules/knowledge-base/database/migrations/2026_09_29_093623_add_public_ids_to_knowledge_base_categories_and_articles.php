<?php

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
                $table->char('public_id', self::PUBLIC_ID_LENGTH)->nullable();
            });

            Schema::table('knowledge_base_articles', function (Blueprint $table) {
                $table->char('public_id', self::PUBLIC_ID_LENGTH)->nullable();
            });

            // TODO: Cleanup Task (knowledge-base-portal-stable-urls): remove this one-time backfill, its method, and the Str import from the retained migration.
            $this->backfillPublicIds('knowledge_base_categories');
            $this->backfillPublicIds('knowledge_base_articles');

            Schema::table('knowledge_base_categories', function (Blueprint $table) {
                $table->char('public_id', self::PUBLIC_ID_LENGTH)->nullable(false)->change();
                $table->uniqueIndex('public_id');
            });

            Schema::table('knowledge_base_articles', function (Blueprint $table) {
                $table->char('public_id', self::PUBLIC_ID_LENGTH)->nullable(false)->change();
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
