<?php

namespace AidingApp\Ai\Enums;

use AidingApp\Ai\Settings\AiSettings;
use Filament\Support\Contracts\HasLabel;

enum AiAssistantApplication: string implements HasLabel
{
    case Copilot = 'personal_assistant';

    case Test = 'test';

    public function getLabel(): string
    {
        return match ($this) {
            self::Copilot => 'Copilot',
            self::Test => 'Test',
        };
    }

    public static function getDefault(): self
    {
        return self::Copilot;
    }

    public function getDefaultModel(): AiModel
    {
        $settings = app(AiSettings::class);

        return match ($this) {
            self::Copilot => $settings->default_model ?? AiModel::OpenAiGpt5,
            self::Test => AiModel::Test,
        };
    }

    public static function parse(string | self | null $value): ?self
    {
        if ($value instanceof self) {
            return $value;
        }

        return self::tryFrom($value);
    }
}
