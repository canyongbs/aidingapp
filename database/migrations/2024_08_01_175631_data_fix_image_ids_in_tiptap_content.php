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

return new class extends Migration
{
    public function up(): void
    {
        $mediaUuidMap = DB::table('media')
            ->pluck('uuid', 'id')
            ->all();

        DB::table('knowledge_base_articles')
            ->lazyById(100)
            ->each(function (stdClass $article) use ($mediaUuidMap) {
                $originalArticleDetails = $article->article_details;

                $newArticleDetails = $this->fixTipTapContent($article->article_details, $mediaUuidMap);

                if ($originalArticleDetails === $newArticleDetails) {
                    return;
                }

                DB::table('knowledge_base_articles')
                    ->where('id', $article->id)
                    ->update([
                        'article_details' => $newArticleDetails
                    ]);
            });
    }

    protected function fixTipTapContent(string $content, array $mediaUuidMap): string
    {
        $content = json_decode($content, associative: true);

        foreach (($content['content'] ?? []) as $blockIndex => $block) {
            $content['content'][$blockIndex] = $this->fixTipTapBlock($block, $mediaUuidMap);
        }

        return json_encode($content);
    }

    protected function fixTipTapBlock(array $block, array $mediaUuidMap): array
    {
        foreach (($block['content'] ?? []) as $blockIndex => $block) {
            $block['content'][$blockIndex] = $this->fixTipTapBlock($block, $mediaUuidMap);
        }

        if ($block['type'] !== 'image') {
            return $block;
        }

        if (blank($block['attrs']['src'] ?? null)) {
            return $block;
        }

        $id = (int) str_replace(['{{media|id:', ';}}'], '', $block['attrs']['src']);
        $uuid = $mediaUuidMap[$id] ?? null;

        if (blank($uuid)) {
            return $block;
        }

        unset($block['attrs']['src']);

        $block['attrs']['id'] = $uuid;

        return $block;
    }
};
