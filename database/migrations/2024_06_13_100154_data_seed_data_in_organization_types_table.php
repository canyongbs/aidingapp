<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    private array $organization_types_data;

    public function __construct()
    {
        $this->organization_types_data = [
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'Company',
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'Non-Profit',
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'Education',
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'Government',
                'created_at' => now(),
            ],
        ];
    }

    public function up(): void
    {
        $organization_types = DB::table('organization_types')->count();

        if ($organization_types <= 0 && ! app()->runningUnitTests()) {
            DB::table('organization_types')->insert($this->organization_types_data);
        }
    }

    public function down(): void
    {
        $organization_types = DB::table('organization_types')->count();

        if ($organization_types > 0  && ! app()->runningUnitTests()) {
            foreach ($this->organization_types_data as $record) {
                DB::table('organization_types')
                    ->where('name', $record['name'])
                    ->delete();
            }
        }
    }
};
