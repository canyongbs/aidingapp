<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\KnowledgeBase\Jobs;

use AidingApp\KnowledgeBase\Exceptions\KnowledgeBaseExternalMediaFileAccessException;
use AidingApp\KnowledgeBase\Exceptions\KnowledgeBaseExternalMediaValidationException;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KnowledgeBaseItemDownloadExternalMedia implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public KnowledgeBaseItem $knowledgeBaseItem) {}

    public function handle(): void
    {
        $content = $this->processContentItem($this->knowledgeBaseItem->article_details);

        $this->knowledgeBaseItem->article_details = $content;

        $this->knowledgeBaseItem::withoutEvents(fn () => $this->knowledgeBaseItem->save());
    }

    public function processContentItem(string | array | null $content): array
    {
        if (isset($content['type']) && $content['type'] === 'image') {
            $content['attrs']['src'] = $this->downloadExternalMedia($content['attrs']['src']);

            return $content;
        }

        return collect($content)->map(function ($item) {
            if (is_array($item)) {
                return $this->processContentItem($item);
            }

            return $item;
        })->toArray();
    }

    public function downloadExternalMedia(string $content): string
    {
        if (Str::isUrl($content)) {
            $disk = config('filament-tiptap-editor.disk');

            $diskConfig = Storage::disk($disk)->getConfig();

            $domains = [];

            if (Str::isUrl($diskConfig['url'])) {
                $domains[] = parse_url($diskConfig['url'])['host'];
            }

            if (Str::isUrl($diskConfig['endpoint'])) {
                $domains[] = parse_url($diskConfig['endpoint'])['host'];
            }

            if (! in_array(parse_url($content)['host'], $domains)) {
                try {
                    if (! $stream = @fopen($content, 'r')) {
                        throw new KnowledgeBaseExternalMediaFileAccessException('Unable to open stream for ' . $content);
                    }

                    $tempFile = tempnam(sys_get_temp_dir(), 'url-file-');

                    file_put_contents($tempFile, $stream);

                    $tmpFile = new UploadedFile($tempFile, basename($tempFile));

                    if (! in_array($tmpFile->getMimeType(), config('filament-tiptap-editor.accepted_file_types'))) {
                        throw new KnowledgeBaseExternalMediaValidationException('The file type is not allowed.');
                    }

                    if (($tmpFile->getSize() / 1000) > config('filament-tiptap-editor.max_file_size')) {
                        throw new KnowledgeBaseExternalMediaValidationException('The file size is too large.');
                    }

                    $media = $this->knowledgeBaseItem->addMedia($tmpFile)
                        ->toMediaCollection('article_details');

                    return "{{media|id:{$media->getKey()};}}";
                } catch (Exception $e) {
                    report($e);
                }
            }
        }

        return $content;
    }
}
