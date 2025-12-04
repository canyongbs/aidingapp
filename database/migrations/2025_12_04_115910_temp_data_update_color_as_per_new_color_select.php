<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('notification_settings')->whereNotNull('primary_color')->whereIn('primary_color', ['slate', 'zinc', 'neutral', 'stone'])->update(['primary_color' => 'gray']);

        DB::table('incident_severities')->whereNotNull('color')->whereIn('color', ['slate', 'zinc', 'neutral', 'stone'])->update(['color' => 'gray']);

        DB::table('service_request_forms')->whereNotNull('primary_color')->whereIn('primary_color', ['slate', 'zinc', 'neutral', 'stone'])->update(['primary_color' => 'gray']);

        DB::table('settings')
            ->where('group', 'portal')
            ->where('name', 'knowledge_management_portal_primary_color')
            ->whereRaw('payload::text IN (?, ?, ?, ?)', [json_encode('slate'), json_encode('zinc'), json_encode('neutral'), json_encode('stone')])
            ->update(['payload' => json_encode('gray')]);
    }
};
