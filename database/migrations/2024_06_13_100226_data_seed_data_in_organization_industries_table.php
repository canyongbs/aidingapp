<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
