<?php

namespace AidingApp\ServiceManagement\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServiceRequestWidgetAssetController
{
    public function __invoke(Request $request, string $file): StreamedResponse
    {
        $path = "widgets/service-requests/{$file}";

        $disk = Storage::disk('public');

        abort_if(! $disk->exists($path), 404, 'File not found.');

        $mimeType = $disk->mimeType($path);

        $stream = $disk->readStream($path);

        abort_if(is_null($stream), 404, 'File not found.');

        return response()->streamDownload(
            function () use ($stream) {
                fpassthru($stream);
                fclose($stream);
            },
            $file,
            ['Content-Type' => $mimeType]
        );
    }
}
