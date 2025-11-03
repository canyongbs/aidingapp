<?php

namespace AidingApp\Ai\Enums;

use AidingApp\Ai\Services\Contracts\AiService;
use AidingApp\Ai\Settings\AiIntegrationsSettings;
use Exception;
use Filament\Support\Contracts\HasLabel;

enum AiModel: string implements HasLabel
{
    case OpenAiGpt5 = 'openai_gpt_5';

    case OpenAiGpt5Mini = 'openai_gpt_5_mini';

    case OpenAiGpt5Nano = 'openai_gpt_5_nano';

    case OpenAiGptTest = 'openai_gpt_test';

    case LlamaParse = 'llamaparse';

    case Test = 'test';

    public function getLabel(): string
    {
        $aiIntegrationSettings = app(AiIntegrationsSettings::class);

        return match ($this) {
            self::OpenAiGpt5 => $aiIntegrationSettings->open_ai_gpt_5_model_name ?? 'Canyon 5',
            self::OpenAiGpt5Mini => $aiIntegrationSettings->open_ai_gpt_5_mini_model_name ?? 'Canyon 5 mini',
            self::OpenAiGpt5Nano => $aiIntegrationSettings->open_ai_gpt_5_nano_model_name ?? 'Canyon 5 nano',
            self::LlamaParse => $aiIntegrationSettings->llamaparse_model_name ?? 'Canyon Parsing Service',
            self::OpenAiGptTest => 'Canyon Test',
            self::Test => 'Test',
        };
    }

    /**
     * @return array<AiModelApplicabilityFeature>
     */
    public function getApplicableFeatures(): array
    {
        $aiIntegrationSettings = app(AiIntegrationsSettings::class);

        $features = match ($this) {
            self::OpenAiGpt5 => $aiIntegrationSettings->open_ai_gpt_5_applicable_features,
            self::OpenAiGpt5Mini => $aiIntegrationSettings->open_ai_gpt_5_mini_applicable_features,
            self::OpenAiGpt5Nano => $aiIntegrationSettings->open_ai_gpt_5_nano_applicable_features,
            self::LlamaParse => [],
            self::OpenAiGptTest => app()->hasDebugModeEnabled() ? AiModelApplicabilityFeature::cases() : [],
            self::Test => app()->hasDebugModeEnabled() ? AiModelApplicabilityFeature::cases() : [],
        };

        return array_map(AiModelApplicabilityFeature::parse(...), $features);
    }

    public function hasService(): bool
    {
        return match ($this) {
            self::LlamaParse => false,
            default => true,
        };
    }

    /**
     * @return class-string<AiService>
     */
    public function getServiceClass(): string
    {
        return match ($this) {
            self::OpenAiGpt5 => OpenAiGpt5Service::class,
            self::OpenAiGpt5Mini => OpenAiGpt5MiniService::class,
            self::OpenAiGpt5Nano => OpenAiGpt5NanoService::class,
            self::OpenAiGptTest => OpenAiGptTestService::class,
            self::Test => TestAiService::class,
            default => throw new Exception('No Service class found for this model.'),
        };
    }

    public function getService(): AiService
    {
        return app($this->getServiceClass());
    }

    public static function parse(string | self | null $value): ?self
    {
        if ($value instanceof self) {
            return $value;
        }

        return self::tryFrom($value);
    }
}
