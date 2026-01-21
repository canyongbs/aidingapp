<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\Ai\Http\Controllers\PortalAssistant;

use AidingApp\Ai\Jobs\PortalAssistant\PersistPortalAssistantUpload;
use AidingApp\Ai\Jobs\PortalAssistant\SendMessage;
use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SendMessageController
{
    public function __invoke(Request $request): StreamedResponse | JsonResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:25000'],
            'thread_id' => ['nullable', 'uuid'],
            'file_urls' => ['nullable', 'array', 'max:6'],
            // Path must be tmp/filename_uuid.extension
            // Allow alphanumeric, dash, underscore, space in filename
            'file_urls.*.path' => [
                'required_with:file_urls',
                'string',
                'regex:/^tmp\/[a-zA-Z0-9_\-\s]+_[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\.(pdf|xls|ppt|doc|pptx|xlsx|docx|jpg|jpeg|png|csv|md|markdown|mkd|txt|text|log|mp4|webm|ogg|quicktime|x-msvideo)$/i',
            ],
            'file_urls.*.original_name' => ['required_with:file_urls', 'string', 'max:255'],
        ]);

        $author = auth('contact')->user();

        if (filled($data['thread_id'] ?? null)) {
            $thread = PortalAssistantThread::query()
                ->whereKey($data['thread_id'])
                ->whereMorphedTo('author', $author)
                ->firstOrFail();
        } else {
            $thread = new PortalAssistantThread();
            $thread->author()->associate($author);
            $thread->save();
        }

        // Handle file attachments if present
        $fileUrls = $data['file_urls'] ?? [];
        $internalContent = null;

        if (! empty($fileUrls)) {
            $this->persistFileUploads($thread, $fileUrls);

            // Build internal content about attached files
            // Strip UUID from filenames for cleaner display to AI
            /** @var array<int, array{path: string, original_name: string}> $fileUrls */
            $fileNames = collect($fileUrls)
                ->pluck('original_name')
                ->map(function (string $filename): string {
                    // Remove the _uuid pattern before the extension
                    return preg_replace('/_[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\./i', '.', $filename) ?? $filename;
                })
                ->implode(', ');
            $fileCount = count($fileUrls);
            $internalContent = "User attached {$fileCount} " . ($fileCount === 1 ? 'file' : 'files') . ": {$fileNames}";
        }

        dispatch(new SendMessage(
            $thread,
            $data['content'],
            request: [
                'headers' => Arr::only(
                    request()->headers->all(),
                    ['host', 'sec-ch-ua', 'user-agent', 'sec-ch-ua-platform', 'origin', 'referer', 'accept-language'],
                ),
                'ip' => request()->ip(),
            ],
            internalContent: $internalContent,
        ));

        return response()->json([
            'message' => 'Message dispatched for processing via websockets.',
            'thread_id' => $thread->getKey(),
        ]);
    }

    /**
     * Persist uploaded files to the service request's uploads media collection.
     *
     * @param array<int, array{path: string, original_name: string}> $fileUrls
     */
    protected function persistFileUploads(PortalAssistantThread $thread, array $fileUrls): void
    {
        // Get the current draft service request
        $draft = ServiceRequest::withoutGlobalScope('excludeDrafts')
            ->where('portal_assistant_thread_id', $thread->getKey())
            ->where('is_draft', true)
            ->latest()
            ->first();

        if (! $draft) {
            return;
        }

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();

        $jobs = collect($fileUrls)->map(function (array $file) use ($draft, $uploadsMediaCollection) {
            return new PersistPortalAssistantUpload(
                $draft,
                $file['path'],
                $file['original_name'],
                $uploadsMediaCollection->getName(),
            );
        });

        if ($jobs->isNotEmpty()) {
            Bus::batch($jobs->all())
                ->name("persist-portal-assistant-uploads-{$draft->getKey()}")
                ->dispatchAfterResponse();
        }
    }
}
