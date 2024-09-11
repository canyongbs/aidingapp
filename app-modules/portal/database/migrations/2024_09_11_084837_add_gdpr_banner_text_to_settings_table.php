<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('portal.cookie_gdpr_banner_text', [
            'text' => 'We use cookies to personalize content, to provide social media features, and to analyze our traffic. We also share information about your use of our site with our partners who may combine it with other information that you\'ve provided to them or that they\'ve collected from your use of their services.',
        ]);
    }

    public function down(): void
    {
        $this->migrator->delete('portal.cookie_gdpr_banner_text');
    }
};
