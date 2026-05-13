<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\KnowledgeBase\Jobs;

use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\Pool;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CheckKnowledgeBaseArticleImagesJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public KnowledgeBaseItem $knowledgeBaseItem) {}

    public function uniqueId(): string
    {
        return $this->knowledgeBaseItem->getKey();
    }

    public function uniqueFor(): int
    {
        return 600;
    }

    public function handle(): void
    {
        $brokenImages = [];

        $brokenMediaImages = $this->checkMediaImages();
        $brokenImages = array_merge($brokenImages, $brokenMediaImages);

        $externalUrls = $this->extractExternalImageUrls();

        if (! empty($externalUrls)) {
            $brokenExternalUrls = $this->checkUrls($externalUrls);
            $brokenImages = array_merge($brokenImages, $brokenExternalUrls);
        }

        $this->knowledgeBaseItem->updateQuietly([
            'are_broken_images_detected' => ! empty($brokenImages),
            'broken_images' => ! empty($brokenImages) ? $brokenImages : null,
        ]);
    }

    /**
     * @return array<string>
     */
    protected function extractMediaImageIds(): array
    {
        $content = $this->knowledgeBaseItem->article_details;

        if (empty($content)) {
            return [];
        }

        $ids = [];
        $this->findImageNodes($content, $ids);

        return array_unique($ids);
    }

    /**
     * @param array<mixed> $nodes
     * @param array<string> $ids
     */
    protected function findImageNodes(array $nodes, array &$ids): void
    {
        foreach ($nodes as $node) {
            if (! is_array($node)) {
                continue;
            }

            if (($node['type'] ?? null) === 'image' && isset($node['attrs']['id']) && is_string($node['attrs']['id']) && $node['attrs']['id'] !== '') {
                $ids[] = $node['attrs']['id'];
            }

            if (isset($node['content']) && is_array($node['content'])) {
                $this->findImageNodes($node['content'], $ids);
            }
        }
    }

    /**
     * @return array<string>
     */
    protected function checkMediaImages(): array
    {
        $imageIds = $this->extractMediaImageIds();

        if (empty($imageIds)) {
            return [];
        }

        $brokenImages = [];

        $mediaItems = $this->knowledgeBaseItem
            ->getMedia('article_details')
            ->keyBy('uuid');

        foreach ($imageIds as $uuid) {
            $media = $mediaItems->get($uuid);

            if (! $media) {
                $brokenImages[] = $uuid;

                continue;
            }

            try {
                $disk = Storage::disk($media->disk);

                if (! $disk->exists($media->getPathRelativeToRoot())) {
                    $brokenImages[] = $uuid;
                }
            } catch (Throwable) {
                $brokenImages[] = $uuid;
            }
        }

        return $brokenImages;
    }

    /**
     * @return array<string>
     */
    protected function extractExternalImageUrls(): array
    {
        $html = $this->knowledgeBaseItem->renderRichContent('article_details');

        if (blank($html)) {
            return [];
        }

        preg_match_all('/<img\s([^>]*)>/i', $html, $matches);

        $urls = [];

        foreach ($matches[1] as $attributes) {
            if (preg_match('/data-id=["\'][^"\']+["\']/', $attributes)) {
                continue;
            }

            if (preg_match('/src=["\']([^"\']+)["\']/', $attributes, $srcMatch)) {
                $urls[] = $srcMatch[1];
            }
        }

        $urls = array_unique($urls);

        return array_values(array_filter($urls, function (string $url): bool {
            $parsed = parse_url($url);
            $host = $parsed['host'] ?? null;

            if (blank($host)) {
                return false;
            }

            if (filter_var($host, FILTER_VALIDATE_IP)) {
                return false;
            }

            $scheme = $parsed['scheme'] ?? null;

            return in_array($scheme, ['http', 'https']);
        }));
    }

    /**
     * @param array<string> $urls
     *
     * @return array<string>
     */
    protected function checkUrls(array $urls): array
    {
        $brokenUrls = [];

        try {
            $responses = Http::pool(fn (Pool $pool) => array_map(
                fn (string $url) => $pool->as($url)->timeout(15)->head($url),
                $urls
            ));

            foreach ($urls as $url) {
                $response = $responses[$url] ?? null;

                if ($response instanceof Throwable) {
                    $brokenUrls[] = $url;
                } elseif ($response && ($response->clientError() || $response->serverError())) {
                    $brokenUrls[] = $url;
                }
            }
        } catch (Throwable) {
            $brokenUrls = $urls;
        }

        return $brokenUrls;
    }
}
