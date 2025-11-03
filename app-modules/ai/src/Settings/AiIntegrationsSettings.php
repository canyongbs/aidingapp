<?php

namespace AidingApp\Ai\Settings;

use Spatie\LaravelSettings\Settings;

class AiIntegrationsSettings extends Settings
{
    public ?string $open_ai_gpt_5_model_name = null;

    public ?string $open_ai_gpt_5_base_uri = null;

    public ?string $open_ai_gpt_5_api_key = null;

    public ?string $open_ai_gpt_5_model = null;

    public ?string $open_ai_gpt_5_image_generation_deployment = null;

    /**
     * @var array<string>
     */
    public array $open_ai_gpt_5_applicable_features = [];

    public ?string $open_ai_gpt_5_mini_model_name = null;

    public ?string $open_ai_gpt_5_mini_base_uri = null;

    public ?string $open_ai_gpt_5_mini_api_key = null;

    public ?string $open_ai_gpt_5_mini_model = null;

    public ?string $open_ai_gpt_5_mini_image_generation_deployment = null;

    /**
     * @var array<string>
     */
    public array $open_ai_gpt_5_mini_applicable_features = [];

    public ?string $open_ai_gpt_5_nano_model_name = null;

    public ?string $open_ai_gpt_5_nano_base_uri = null;

    public ?string $open_ai_gpt_5_nano_api_key = null;

    public ?string $open_ai_gpt_5_nano_model = null;

    public ?string $open_ai_gpt_5_nano_image_generation_deployment = null;

    /**
     * @var array<string>
     */
    public array $open_ai_gpt_5_nano_applicable_features = [];

    public ?string $llamaparse_model_name = null;

    public ?string $llamaparse_api_key = null;

    public static function group(): string
    {
        return 'ai';
    }

    /** @return array<string> */
    public static function encrypted(): array
    {
        return [
            'open_ai_gpt_5_base_uri',
            'open_ai_gpt_5_api_key',
            'open_ai_gpt_5_model',
            'open_ai_gpt_5_image_generation_deployment',
            'open_ai_gpt_5_mini_base_uri',
            'open_ai_gpt_5_mini_api_key',
            'open_ai_gpt_5_mini_model',
            'open_ai_gpt_5_mini_image_generation_deployment',
            'open_ai_gpt_5_nano_base_uri',
            'open_ai_gpt_5_nano_api_key',
            'open_ai_gpt_5_nano_model',
            'open_ai_gpt_5_nano_image_generation_deployment',
            'llamaparse_api_key',
        ];
    }
}
