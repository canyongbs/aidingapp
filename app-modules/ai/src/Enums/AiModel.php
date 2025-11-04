<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\Ai\Enums;

use AidingApp\Ai\Services\Contracts\AiService;
use AidingApp\Ai\Services\TestAiService;
use AidingApp\Ai\Settings\AiIntegrationsSettings;
use AidingApp\IntegrationOpenAi\Services\OpenAiGpt5MiniService;
use AidingApp\IntegrationOpenAi\Services\OpenAiGpt5NanoService;
use AidingApp\IntegrationOpenAi\Services\OpenAiGpt5Service;
use AidingApp\IntegrationOpenAi\Services\OpenAiGptTestService;
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
