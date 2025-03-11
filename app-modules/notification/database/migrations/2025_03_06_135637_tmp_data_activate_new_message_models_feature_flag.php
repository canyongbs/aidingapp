<?php

use App\Features\NewMessageModels;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class () extends Migration {
    public function up(): void
    {
        NewMessageModels::activate();

        Artisan::call('queue:restart');
        Artisan::call('schedule:interrupt');
        Artisan::call('schedule:clear-cache');
    }

    public function down(): void
    {
        NewMessageModels::deactivate();
    }
};
