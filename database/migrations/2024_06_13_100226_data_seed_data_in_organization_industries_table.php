<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    private array $organization_industries_data;

    public function __construct()
    {
        $this->organization_industries_data = [
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'Information Technology',
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'Healthcare',
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'Financial Services',
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'Retail',
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'Manufacturing',
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'Education',
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'Energy',
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'Transportation & Logistics',
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'Entertainment & Media',
                'created_at' => now(),
            ],
            [
                'id' => (string) Str::orderedUuid(),
                'name' => 'Food & Beverage',
                'created_at' => now(),
            ],
        ];
    }

    public function up(): void
    {
        $organization_industries = DB::table('organization_industries')->count();

        if ($organization_industries <= 0 && ! app()->runningUnitTests()) {
            DB::table('organization_industries')->insert($this->organization_industries_data);
        }
    }

    public function down(): void
    {
        $organization_industries = DB::table('organization_industries')->count();

        if ($organization_industries > 0 && ! app()->runningUnitTests()) {
            foreach ($this->organization_industries_data as $record) {
                DB::table('organization_industries')
                    ->where('name', $record['name'])
                    ->delete();
            }
        }
    }
};
