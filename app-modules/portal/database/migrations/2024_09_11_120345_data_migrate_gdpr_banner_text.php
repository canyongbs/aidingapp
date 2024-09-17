<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        try {
            DB::beginTransaction();

            $oldGDPRBannerText = DB::table('settings')
                ->where('group', 'portal')
                ->where('name', 'gdpr_banner_text')
                ->first();

            if ($oldGDPRBannerText) {
                DB::table('settings')->where('group', 'portal')
                    ->where('name', 'gdpr_banner_text')
                    ->update([
                        'payload' => [
                            'type' => 'doc',
                            'content' => [
                                [
                                    'type' => 'paragraph',
                                    'attrs' => [
                                        'textAlign' => 'start',
                                    ],
                                    'content' => [
                                        [
                                            'type' => 'text',
                                            'text' => json_decode($oldGDPRBannerText->payload),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ]);
            }

            DB::commit();
        } catch (Exception $error) {
            DB::rollBack();

            throw $error;
        }
    }

    public function down(): void
    {
        DB::table('settings')
            ->where('group', 'portal')
            ->where('name', 'gdpr_banner_text')
            ->update([
                'payload' => json_encode('We use cookies to personalize content, to provide social media features, and to analyze our traffic. We also share information about your use of our site with our partners who may combine it with other information that you\'ve provided to them or that they\'ve collected from your use of their services.'),
            ]);
    }
};
