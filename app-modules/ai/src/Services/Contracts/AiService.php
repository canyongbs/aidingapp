<?php

namespace AidingApp\Ai\Services\Contracts;

use AidingApp\Ai\Models\AiMessage;
use AidingApp\Ai\Models\Contracts\AiFile;
use Closure;

interface AiService
{
    /**
     * This method is passed a prompt and should return a completion for it.
     */
    public function complete(string $prompt, string $content): string;

    /**
     * This method is passed a prompt and message and should return a stream of the response.
     *
     * @param array<AiFile> $files
     * @param array<string, mixed> $options
     */
    public function stream(string $prompt, string $content, array $files = [], array $options = []): Closure;

    /**
     * This method is passed a prompt and message and should return a stream of plain text chunks.
     *
     * @param array<AiFile> $files
     * @param array<string, mixed> $options
     */
    public function streamRaw(string $prompt, string $content, array $files = [], array $options = []): Closure;

    /**
     * This method is passed an unsaved `AiMessage` model and should send the
     * message to the AI service. If that is successful, it should save the
     * message before fetching the response, in case the response fails
     * to generate.
     *
     * The method should return a new unsaved `AiMessage` model with the content
     * from the AI service set only, the other attributes will be set later.
     * 
     * @param array<AiFile> $files
     */
    public function sendMessage(AiMessage $message, array $files, bool $hasImageGeneration = false): Closure;

    /**
     * This method is passed an `AiMessage` model and should recover the
     * request to the AI service. If that is successful, it should save the
     * message before fetching the response, in case the response fails
     * to generate.
     *
     * The method should return a new unsaved `AiMessage` model with the content
     * from the AI service set only, the other attributes will be set later.
     * 
     * @param array<AiFile> $files
     */
    public function retryMessage(AiMessage $message, array $files, bool $hasImageGeneration = false): Closure;

    public function completeResponse(AiMessage $response): Closure;

    public function getMaxAssistantInstructionsLength(): int;

    /**
     * @param array<AiFile> $files
     */
    public function areFilesReady(array $files): bool;

    public function hasImageGeneration(): bool;
}
