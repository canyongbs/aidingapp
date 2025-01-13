<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('engagement_deliverables')
            ->where('channel', 'sms')
            ->chunkById(100, function ($deliverables) {
                $engagementIds = $deliverables->pluck('engagement_id')->toArray();
                $deliverableIds = $deliverables->pluck('id')->toArray();

                DB::table('timelines')
                    ->whereIn('timelineable_id', $engagementIds)
                    ->whereIn('timelineable_type', ['outbound_deliverable', 'engagement'])
                    ->delete();

                DB::table('outbound_deliverables')
                    ->whereIn('related_id', $deliverableIds)
                    ->where('channel', 'sms')
                    ->delete();

                DB::table('engagements')
                    ->whereIn('id', $engagementIds)
                    ->delete();

                DB::table('engagement_deliverables')
                    ->whereIn('id', $deliverableIds)
                    ->delete();
            });
    }
};
