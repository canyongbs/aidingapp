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

use App\Features\FullNameFeature;
use CanyonGBS\Common\Parser\Parser;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('users', function (Blueprint $table) {
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('preferred_name')->nullable();
                $table->string('employee_id')->nullable();
                $table->string('student_id')->nullable();
                $table->string('school')->nullable();
                $table->string('academic_department')->nullable();
                $table->string('program')->nullable();
                $table->string('address')->nullable();
                $table->string('address_2')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('country')->nullable();
            });

            //TODO: FullNameFeature - Remove this query section once the feature flag is removed.
            DB::table('users')
                ->whereNull('first_name')
                ->chunkById(500, function (Collection $users) {
                    foreach ($users as $user) {
                        $name = (new Parser())->parse(trim($user->name));

                        DB::table('users')
                            ->where('id', $user->id)
                            ->update([
                                'first_name' => $name->getFirstname() ?: trim($user->name),
                                'last_name' => $name->getLastname() ?: '',
                                'name' => trim($user->name),
                            ]);
                    }
                });

            Schema::table('users', function (Blueprint $table) {
                $table->string('first_name')->nullable(false)->change();
                $table->string('last_name')->nullable(false)->change();
                $table->string('name')->nullable(false)->change();
            });

            FullNameFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            FullNameFeature::deactivate();

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn([
                    'first_name',
                    'last_name',
                    'preferred_name',
                    'employee_id',
                    'student_id',
                    'school',
                    'academic_department',
                    'program',
                    'address',
                    'address_2',
                    'city',
                    'state',
                    'postal_code',
                    'country',
                ]);
            });
        });
    }
};
