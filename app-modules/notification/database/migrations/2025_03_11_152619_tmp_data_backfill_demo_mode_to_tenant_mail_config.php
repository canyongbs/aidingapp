<?php

use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        $tenant = Tenant::current();

        $config = $tenant->config;

        $config->mail->isDemoModeEnabled = $config->mail->isDemoModeEnabled ?? false;
        $config->mail->isExcludingSystemNotificationsFromDemoMode = $config->mail->isExcludingSystemNotificationsFromDemoMode ?? true;

        $tenant->config = $config;

        $tenant->save();
    }

    public function down(): void
    {
        $tenant = Tenant::current();

        $config = $tenant->config;

        unset($config->mail->isDemoModeEnabled, $config->mail->isExcludingSystemNotificationsFromDemoMode);

        $tenant->config = $config;

        $tenant->save();
    }
};
