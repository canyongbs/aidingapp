<?php

namespace AidingApp\Ai\Actions;

use AidingApp\Ai\Enums\AiMessageLogFeature;
use AidingApp\Ai\Enums\AiModel;
use AidingApp\Ai\Models\LegacyAiMessageLog;
use Illuminate\Support\Arr;

class CompletePrompt
{
    public function execute(AiModel $aiModel, string $prompt, string $content): string
    {
        $service = $aiModel->getService();

        $completion = $service->complete($prompt, $content);

        if (auth()->hasUser()) {
            LegacyAiMessageLog::create([
                'message' => $content,
                'metadata' => [
                    'prompt' => $prompt,
                    'completion' => $completion,
                ],
                'request' => [
                    'headers' => Arr::only(
                        request()->headers->all(),
                        ['host', 'sec-ch-ua', 'user-agent', 'sec-ch-ua-platform', 'origin', 'referer', 'accept-language'],
                    ),
                    'ip' => request()->ip(),
                ],
                'sent_at' => now(),
                'user_id' => auth()->id(),
                'ai_assistant_name' => 'Institutional Advisor',
                'feature' => AiMessageLogFeature::DraftWithAi,
            ]);
        }

        return $completion;
    }
}