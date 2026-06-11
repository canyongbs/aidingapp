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
use App\Features\ContactTypeManagementFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * @var array<string, string>
     */
    private array $colorMap = [
        'info' => 'blue',
        'warning' => 'amber',
        'success' => 'green',
        'danger' => 'red',
        'primary' => 'gray',
    ];

    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('contact_types', function (Blueprint $table) {
                $table->boolean('is_default')->default(false);

                /*
                 * TODO: ContactTypeManagementFeature cleanup — once the feature flag is removed:
                 * - Drop the `classification` column entirely (a follow-up migration).
                 * It is only kept (made nullable) here so the flag-off path can continue to
                 * write/read classification while the feature is being rolled out.
                 */
                $table->string('classification')->nullable()->change();
            });

            foreach ($this->colorMap as $from => $to) {
                DB::table('contact_types')
                    ->where('color', $from)
                    ->update(['color' => $to]);
            }

            ContactTypeManagementFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            ContactTypeManagementFeature::deactivate();

            // 'primary' -> 'gray' is not reversible (both legacy 'primary' and 'gray'
            // become 'gray'), so 'gray' rows are left untouched on rollback.
            $reverse = [
                'blue' => 'info',
                'amber' => 'warning',
                'green' => 'success',
                'red' => 'danger',
            ];

            foreach ($reverse as $from => $to) {
                DB::table('contact_types')
                    ->where('color', $from)
                    ->update(['color' => $to]);
            }

            Schema::table('contact_types', function (Blueprint $table) {
                $table->dropColumn('is_default');

                $table->string('classification')->nullable(false)->change();
            });
        });
    }
};
