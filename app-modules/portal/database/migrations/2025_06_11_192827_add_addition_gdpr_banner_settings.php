<?php

use AidingApp\Portal\Enums\GdprDeclineOptions;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('portal.gdpr_privacy_policy', false);
        $this->migrator->add('portal.gdpr_privacy_policy_url');
        $this->migrator->add('portal.gdpr_terms_of_use', false);
        $this->migrator->add('portal.gdpr_terms_of_use_url');
        $this->migrator->add('portal.gdpr_decline', false);
        $this->migrator->add('portal.gdpr_decline_value', GdprDeclineOptions::Decline);
    }

    public function down(): void
    {
        $this->migrator->delete('portal.gdpr_privacy_policy');
        $this->migrator->delete('portal.gdpr_privacy_policy_url');
        $this->migrator->delete('portal.gdpr_terms_of_use');
        $this->migrator->delete('portal.gdpr_terms_of_use_url');
        $this->migrator->delete('portal.gdpr_decline');
        $this->migrator->delete('portal.gdpr_decline_value');
    }
};
