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

namespace AidingApp\Ai\Http\Controllers\AssistantWidget;

use AidingApp\Ai\Jobs\PortalAssistant\SendMessage;
use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Portal\Settings\PortalSettings;
use App\Features\EmbeddableSupportAssistantFeature;
use App\Http\Controllers\Controller;
use Filament\Support\Colors\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssistantWidgetController extends Controller
{
    public function config(Request $request): JsonResponse
    {
        $manifestPath = public_path('storage/widgets/assistant/.vite/manifest.json');
        /** @var array<string, array{file: string, name: string, src: string, isEntry: bool}> $manifest */
        $manifest = json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);

        $widgetEntry = $manifest['src/widget.js'];

        $settings = app(PortalSettings::class);

        return response()->json([
            'asset_url' => route('widgets.assistant.asset'),
            'js' => route('widgets.assistant.asset', ['file' => $widgetEntry['file']]),
            'send_message_url' => route('widgets.assistant.api.messages'),
            'websockets_config' => config('filament.broadcasting.echo'),
            'primary_color' => collect(Color::all()[$settings->knowledge_management_portal_primary_color?->value ?? 'blue'])
                ->map(Color::convertToRgb(...))
                ->map(fn (string $value): string => (string) str($value)->after('rgb(')->before(')'))
                ->all(),
            'rounding' => $settings->knowledge_management_portal_rounding?->value ?? 'md',
            'is_authenticated' => (bool) auth('contact')->user(),
            'guest_token_enabled' => EmbeddableSupportAssistantFeature::active(),
        ]);
    }

    public function asset(Request $request, string $file): StreamedResponse
    {
        $path = "widgets/assistant/{$file}";

        $disk = Storage::disk('public');

        abort_if(! $disk->exists($path), 404, 'File not found.');

        $mimeType = $disk->mimeType($path);

        $stream = $disk->readStream($path);

        abort_if(is_null($stream), 404, 'File not found.');

        return response()->streamDownload(
            function () use ($stream) {
                fpassthru($stream);
                fclose($stream);
            },
            $file,
            ['Content-Type' => $mimeType]
        );
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:25000'],
            'thread_id' => ['nullable', 'uuid'],
            'guest_token' => ['nullable', 'uuid'],
        ]);

        $author = auth('contact')->user();
        $featureActive = EmbeddableSupportAssistantFeature::active();

        if (filled($data['thread_id'] ?? null)) {
            $query = PortalAssistantThread::query()
                ->whereKey($data['thread_id']);

            if ($author) {
                $query->whereMorphedTo('author', $author);
            } else {
                $query->whereNull('author_type')
                    ->whereNull('author_id');

                if ($featureActive && filled($data['guest_token'] ?? null)) {
                    $query->where('guest_token', $data['guest_token']);
                }
            }

            $thread = $query->firstOrFail();
        } else {
            $thread = new PortalAssistantThread();
            if ($author) {
                $thread->author()->associate($author);
            }
            
            if ($featureActive) {
                $thread->guest_token = $data['guest_token'] ?? Str::uuid()->toString();
            }
            
            $thread->save();
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
        ));

        return response()->json([
            'message' => 'Message dispatched for processing via websockets.',
            'thread_id' => $thread->getKey(),
            'guest_token' => $thread->guest_token,
        ]);
    }
}
