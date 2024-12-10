<?php

namespace AidingApp\Ai\Settings;

use Spatie\LaravelSettings\Settings;

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
