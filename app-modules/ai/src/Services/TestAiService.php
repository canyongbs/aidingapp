<?php

namespace AidingApp\Ai\Services;

use AidingApp\Ai\Models\AiMessage;
use AidingApp\Ai\Services\Contracts\AiService;
use AidingApp\Ai\Support\StreamingChunks\Finish;
use AidingApp\Ai\Support\StreamingChunks\Text;
use Closure;
use Exception;
use Generator;

class TestAiService implements AiService
{
    public function complete(string $prompt, string $content): string
    {
        return fake()->paragraph();
    }

    /**
     * @param array<AiFile> $files
     * @param array<string, mixed> $options
     */
    public function stream(string $prompt, string $content, array $files = [], array $options = []): Closure
    {
        throw new Exception('Plain text streaming is not supported by this service.');
    }

    /**
     * @param array<AiFile> $files
     * @param array<string, mixed> $options
     */
    public function streamRaw(string $prompt, string $content, array $files = [], array $options = []): Closure
    {
        throw new Exception('Plain text streaming is not supported by this service.');
    }

    public function sendMessage(AiMessage $message, array $files, bool $hasImageGeneration = false): Closure
    {
        $message->context = fake()->paragraph();
        $message->save();

        $message->thread->name = fake()->words();
        $message->thread->save();

        if (! empty($files)) {
            $message->files()->saveMany($files);
        }

        return function (): Generator {
            yield new Text(fake()->paragraph());

            yield new Finish();
        };
    }

    public function retryMessage(AiMessage $message, array $files, bool $hasImageGeneration = false): Closure
    {
        return $this->sendMessage($message, $files);
    }

    public function completeResponse(AiMessage $response): Closure
    {
        return $this->sendMessage($response, files: []);
    }

    public function getMaxAssistantInstructionsLength(): int
    {
        return 30000;
    }

    /**
     * @param array<AiFile> $files
     */
    public function areFilesReady(array $files): bool
    {
        return true;
    }

    public function hasImageGeneration(): bool
    {
        return false;
    }
}
