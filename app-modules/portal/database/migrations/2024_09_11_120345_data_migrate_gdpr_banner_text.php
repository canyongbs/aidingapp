<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
