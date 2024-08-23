<?php

namespace App\Listeners;

use ReflectionClass;
use Spatie\LaravelSettings\Events\LoadingSettings;

class LoadSettingsDefaults
{
    public function handle(LoadingSettings $event): void
    {
        foreach ((new ReflectionClass($event->settingsClass))->getProperties() as $property) {
            if ($event->properties->has($property->getName())) {
                continue;
            }

            $event->properties->put($property->getName(), $property->getDefaultValue());
        }
    }
}
