<?php

namespace AidingApp\IntegrationOpenAi\Services;

class OpenAiGptTestService extends BaseOpenAiService
{
    public function getApiKey(): string
    {
        return 'test';
    }

    public function getModel(): string
    {
        return 'test';
    }

    public function getDeployment(): ?string
    {
        return 'https://api.openai.com/v1';
    }
}
