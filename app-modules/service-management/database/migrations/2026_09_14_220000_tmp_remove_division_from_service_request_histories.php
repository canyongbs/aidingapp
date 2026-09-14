<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function (): void {
            DB::table('service_request_histories')
                ->where(function (Builder $query): void {
                    $query
                        ->whereRaw("jsonb_exists(original_values::jsonb, 'division_id')")
                        ->orWhereRaw("jsonb_exists(new_values::jsonb, 'division_id')");
                })
                ->orderBy('id')
                ->chunkById(100, function (Collection $histories): void {
                    foreach ($histories as $history) {
                        $originalValues = json_decode($history->original_values, true) ?? [];
                        $newValues = json_decode($history->new_values, true) ?? [];

                        unset($originalValues['division_id'], $newValues['division_id']);

                        if ($newValues === []) {
                            DB::table('service_request_histories')
                                ->where('id', $history->id)
                                ->delete();

                            continue;
                        }

                        DB::table('service_request_histories')
                            ->where('id', $history->id)
                            ->update([
                                'original_values' => json_encode($originalValues),
                                'new_values' => json_encode($newValues),
                            ]);
                    }
                });
        });
    }

    public function down(): void
    {
        // This migration is destructive and so the down method is intentionally left blank
    }
};
