<?php

use Pest\Arch\Contracts\ArchExpectation;
use Pest\Arch\Expectations\Targeted;
use Pest\Arch\Objects\ObjectDescription;
use Pest\Arch\Support\FileLineFinder;




expect()->extend('toHaveDefaultsForAllProperties', function (): ArchExpectation {
    return Targeted::make(
        $this,
        fn (ObjectDescription $object): bool => collect($object->reflectionClass->getProperties())->every(fn ($property) => $property->hasDefaultValue()),
        "to have default for all properties",
        FileLineFinder::where(fn (string $line): bool => str_contains($line, 'class')),
    );
});