<?php

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
