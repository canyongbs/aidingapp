<?php

namespace AidingApp\Contact\Database\Seeders;

use AidingApp\Contact\Models\OrganizationIndustry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationIndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrganizationIndustry::factory()
        ->createMany(
            [
                ['name' => 'Information Technology'],
                ['name' => 'Healthcare'],
                ['name' => 'Financial Services'],
                ['name' => 'Retail'],
                ['name' => 'Manufacturing'],
                ['name' => 'Education'],
                ['name' => 'Energy'],
                ['name' => 'Transportation & Logistics'],
                ['name' => 'Entertainment & Media'],
                ['name' => 'Food & Beverage'],
                
            ]
        );
    }
}
