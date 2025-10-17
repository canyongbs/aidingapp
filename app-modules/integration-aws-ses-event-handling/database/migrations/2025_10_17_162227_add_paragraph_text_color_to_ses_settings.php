<?php

use App\Features\ParagraphTextColorFeature;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        DB::transaction(function () {
            if (! $this->migrator->exists('ses.paragraph_text_color')) {
                $this->migrator->add('ses.paragraph_text_color', '#000000');
            }

            ParagraphTextColorFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            ParagraphTextColorFeature::deactivate();

            $this->migrator->deleteIfExists('ses.paragraph_text_color');
        });
    }
};
