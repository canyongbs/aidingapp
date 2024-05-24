<?php

namespace AidingApp\ServiceManagement\Models\MediaCollections;

use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;

class UploadsMediaCollection
{
    private static string $name = 'uploads';

    private static int $maxNumberOfFiles = 5;

    private static int $maxFileSizeInMB = 20;

    private static array $mimes = [
        'application/pdf' => ['pdf'],
        'application/vnd.ms-excel' => ['xls'],
        'application/vnd.ms-powerpoint' => ['ppt'],
        'application/vnd.ms-word' => ['doc'],
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['pptx'],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/pdf' => ['pdf'],
        'image/png' => ['png'],
        'text/csv' => ['csv'],
        'text/markdown' => ['md', 'markdown', 'mkd'],
        'text/plain' => ['txt', 'text'],
    ];

    public static function asMediaCollection(): MediaCollection
    {
        return MediaCollection::create(static::$name)
            ->acceptsFile(function (File $file) {
                return $file->size <= static::$maxFileSizeInMB * 1024 * 1024;
            })
            ->onlyKeepLatest(static::$maxNumberOfFiles)
            ->acceptsMimeTypes(collect(static::$mimes)->keys()->toArray());
    }

    public static function getName(): string
    {
        return static::$name;
    }

    public static function getMaxNumberOfFiles(): int
    {
        return static::$maxNumberOfFiles;
    }

    public static function getMaxFileSizeInMB(): int
    {
        return static::$maxFileSizeInMB;
    }

    public static function getMimes(): array
    {
        return collect(static::$mimes)
            ->keys()
            ->sort()
            ->values()
            ->toArray();
    }

    public static function getExtensions(): array
    {
        return collect(static::$mimes)
            ->flatten()
            ->unique()
            ->sort()
            ->map(
                fn (string $string): string => str($string)
                    ->prepend('.')
                    ->toString()
            )
            ->values()
            ->toArray();
    }
}
