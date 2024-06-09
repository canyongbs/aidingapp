<?php

namespace AidingApp\Contact\Database\Seeders;

use AidingApp\Contact\Models\OrganizationType;
use Illuminate\Database\Seeder;

class OrganizationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrganizationType::factory()
            ->createMany(
                [
                    ['name' => 'Company'],
                    ['name' => 'Non-Profit'],
                    ['name' => 'Education'],
                    ['name' => 'Government'],
                ]
            );
    }
}
