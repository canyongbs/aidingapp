<?php

namespace AidingApp\Ai\Models\Contracts;

interface AiFile
{
    public function getKey(): string;

    public function getName(): ?string;

    public function getMimeType(): ?string;

    public function getFileId(): ?string;

    public function getParsingResults(): ?string;

    /**
     * @deprecated Non-responses-API OpenAI services only.
     */
    public function getTemporaryUrl(): ?string;
}