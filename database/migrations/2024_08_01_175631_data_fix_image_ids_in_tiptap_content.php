<?php

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
        foreach (($block['content'] ?? []) as $blockIndex => $nestedBlock) {
            $block['content'][$blockIndex] = $this->fixTipTapBlock($nestedBlock, $mediaUuidMap);
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
