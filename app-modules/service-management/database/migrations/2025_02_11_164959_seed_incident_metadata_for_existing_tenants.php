<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AidingApp\ServiceManagement\Enums\SystemIncidentStatusClassification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    private array $incidentSeverities = [
        ['name' => 'Critical', 'color' => 'red'],
        ['name' => 'Major', 'color' => 'orange'],
        ['name' => 'Minor', 'color' => 'yellow'],
        ['name' => 'Warning', 'color' => 'amber'],
        ['name' => 'Informational', 'color' => 'blue'],
    ];

    private array $incidentStatuses = [
        ['classification' => SystemIncidentStatusClassification::Open, 'name' => 'Identified'],
        ['classification' => SystemIncidentStatusClassification::Open, 'name' => 'Investigating'],
        ['classification' => SystemIncidentStatusClassification::Open, 'name' => 'Mitigating'],
        ['classification' => SystemIncidentStatusClassification::Open, 'name' => 'Monitoring'],
        ['classification' => SystemIncidentStatusClassification::Resolved, 'name' => 'Resolved'],
    ];

    public function up(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        DB::table('incident_severities')->insert(collect($this->incidentSeverities)->map(function ($item) {
            $item['id'] = Str::uuid()->toString();
            $item['created_at'] = now();

            return $item;
        })->toArray());

        DB::table('incident_statuses')->insert(collect($this->incidentStatuses)->map(function ($item) {
            $item['id'] = Str::uuid()->toString();
            $item['created_at'] = now();

            return $item;
        })->toArray());
    }

    public function down(): void
    {
        DB::table('incident_severities')
            ->whereIn('name', collect($this->incidentSeverities)->pluck('name'))
            ->delete();

        DB::table('incident_statuses')
            ->whereIn('name', collect($this->incidentStatuses)->pluck('name'))
            ->delete();
    }
};
