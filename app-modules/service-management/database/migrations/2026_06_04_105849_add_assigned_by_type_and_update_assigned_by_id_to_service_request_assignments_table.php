<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Features\ServiceRequestAssignmentByTypeFeature;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('service_request_assignments', function (Blueprint $table) {
                $table->string('assigned_by_type')->nullable();
            });

            //TODO: ServiceRequestAssignmentByTypeFeature clean up: Please remove the fllowing code while removing feature flag. line: 53 to 55
            DB::table('service_request_assignments')
                ->whereNotNull('assigned_by_id')
                ->update(['assigned_by_type' => (new User())->getMorphClass()]);

            Schema::table('service_request_assignments', function (Blueprint $table) {
                $table->dropForeign(['assigned_by_id']);
                $table->index(['assigned_by_type', 'assigned_by_id']);
            });

            ServiceRequestAssignmentByTypeFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            ServiceRequestAssignmentByTypeFeature::deactivate();
            Schema::table('service_request_assignments', function (Blueprint $table) {
                $table->dropIndex(['assigned_by_type', 'assigned_by_id']);
            });

            //TODO: ServiceRequestAssignmentByTypeFeature clean up: Please remove the fllowing code while removing feature flag. line: 75 to 78
            DB::table('service_request_assignments')
                ->whereNotNull('assigned_by_type')
                ->where('assigned_by_type', '!=', (new User())->getMorphClass())
                ->update(['assigned_by_id' => null]);

            Schema::table('service_request_assignments', function (Blueprint $table) {
                $table->foreign('assigned_by_id')->references('id')->on('users');
                $table->dropColumn('assigned_by_type');
            });
        });
    }
};
