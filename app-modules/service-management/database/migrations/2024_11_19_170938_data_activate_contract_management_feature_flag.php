<?php

use App\Features\ContractManagement;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        ContractManagement::activate();
    }

    public function down(): void
    {
        ContractManagement::deactivate();
    }
};
