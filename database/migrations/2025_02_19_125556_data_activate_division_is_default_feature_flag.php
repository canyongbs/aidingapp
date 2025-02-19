<?php

use App\Features\DivisionIsDefault;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        DivisionIsDefault::activate();
    }

    public function down(): void
    {
        DivisionIsDefault::deactivate();
    }
};
