<?php

namespace AidingApp\IntegrationOpenAi\Services;

class OpenAiGpt5MiniService extends BaseOpenAiService
{
    public function getApiKey(): string
    {
        return $this->settings->open_ai_gpt_5_mini_api_key ?? config('integration-open-ai.gpt_5_mini_api_key');
    }

    public function getModel(): string
    {
        return $this->settings->open_ai_gpt_5_mini_model ?? config('integration-open-ai.gpt_5_mini_model');
    }

    public function getDeployment(): ?string
    {
        return $this->settings->open_ai_gpt_5_mini_base_uri ?? config('integration-open-ai.gpt_5_mini_base_uri');
    }

    public function getImageGenerationDeployment(): ?string
    {
        return $this->settings->open_ai_gpt_5_mini_image_generation_deployment;
    }

    public function hasTemperature(): bool
    {
        return false;
    }

    public function hasReasoning(): bool
    {
        return true;
    }
}
