<?php

namespace AidingApp\Ai\Settings;

use AidingApp\Ai\Enums\AiModel;
use Spatie\LaravelSettings\Settings;

class AiIntegratedAssistantSettings extends Settings
{
    public ?AiModel $default_model = null;

    public static function group(): string
    {
        return 'ai-integrated-assistant';
    }

    public function getDefaultModel(): AiModel
    {
        return $this->default_model ?? AiModel::OpenAiGpt5;
    }
}
