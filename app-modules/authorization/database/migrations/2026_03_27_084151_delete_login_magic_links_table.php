<?php

use App\Features\OtpCodeLoginFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            Schema::dropIfExists('login_magic_links');

            OtpCodeLoginFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            OtpCodeLoginFeature::deactivate();

            Schema::create('login_magic_links', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->text('code');
                $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamp('used_at')->nullable();
                $table->timestamps();
            });
        });
    }
};
