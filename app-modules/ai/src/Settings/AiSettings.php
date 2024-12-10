<?php

namespace AidingApp\Ai\Settings;

use AdvisingApp\Ai\Enums\AiModel;
use Spatie\LaravelSettings\Settings;
use AdvisingApp\Ai\Enums\AiMaxTokens;

class AiSettings extends Settings
{
    public ?string $url = null;

    public ?string $key = null;

    public ?string $api_version = null;

    public ?string $model = null;

    public static function group(): string
    {
        return 'ai';
    }

     public static function encrypted(): array
    {
        return [
            'url',
            'key',
            'api_version',
            'model',
        ];
    }
}
