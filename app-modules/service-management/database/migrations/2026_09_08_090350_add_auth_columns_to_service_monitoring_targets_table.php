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

use App\Features\ServiceMonitoringAuthTypeFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

/*
 * TODO: Cleanup Task (service-monitoring-report-configurations-feature): this migration is
 * timestamped to run before 2026_09_08_090400_tmp_backfill_service_monitoring_report_configurations
 * (not by actual authoring date) so the isolatedMigration() test targeting that migration in
 * tests/TenantMigrationTests.php still sees these columns present when it rolls the schema back.
 * Rename this file to a real current-date timestamp once that tmp migration and its test are deleted.
 */
return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('service_monitoring_targets', function (Blueprint $table) {
                $table->string('auth_type')->initial('none');
                $table->text('auth_username')->nullable();
                $table->text('auth_password')->nullable();
            });

            ServiceMonitoringAuthTypeFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            ServiceMonitoringAuthTypeFeature::deactivate();

            Schema::table('service_monitoring_targets', function (Blueprint $table) {
                $table->dropColumn(['auth_type', 'auth_username', 'auth_password']);
            });
        });
    }
};
