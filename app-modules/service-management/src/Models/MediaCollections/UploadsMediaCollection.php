<?php

namespace AidingApp\ServiceManagement\Models\MediaCollections;

use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;

class UploadsMediaCollection
{
    public static string $name = 'uploads';

    public static int $maxNumberOfFiles = 5;

    public static int $maxFileSizeInMB = 20;

    public static array $mimes = [
        'application/ms-excel',
        'application/msexcel',
        'application/msword',
        'application/pdf',
        'application/vnd.ms-excel',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/x-zip',
        'application/x-zip-compressed',
        'application/xml',
        'application/zip',
        'audio/mpeg',
        'image/gif',
        'image/jpeg',
        'image/png',
        'text/csv',
        'text/markdown',
        'text/plain',
        'video/mp4',
    ];

    public static function asMediaCollection(): MediaCollection
    {
        return MediaCollection::create(static::$name)
            ->acceptsFile(function (File $file) {
                return $file->size <= static::$maxFileSizeInMB * 1024 * 1024;
            })
            ->onlyKeepLatest(static::$maxNumberOfFiles)
            ->acceptsMimeTypes(static::$mimes);
    }
}
